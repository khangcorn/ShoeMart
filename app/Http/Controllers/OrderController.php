<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderCoupon;
use App\Models\OrderReview;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RefundRequest;
use App\Models\ShippingFee;
use App\Models\UserAddresses;
// Nếu chưa có
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Stripe\Charge;
use Stripe\Stripe;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng của người dùng
    public function index()
    {
        $orders = auth()->user()->orders()->with('status')->orderBy('created_at', 'desc')->paginate(10);

        return view('order.index', compact('orders'));
    }

    // Hiển thị chi tiết đơn hàng cho người dùng
    public function show($order_id)
    {
        $order = Order::with([
            'userAddresses',
            'orderDetails.product.images',
            'orderDetails.variant.attributes.variantAttribute',
            'orderCoupons',
            'status',
        ])->find($order_id);

        if (! $order) {
            return redirect()->route('order.index')->with('error', 'Đơn hàng không tồn tại.');
        }

        return view('order.show', compact('order'));
    }

    /**
     * Hiển thị giao diện đặt hàng (chi tiết đơn hàng)
     */
    public function create(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để đặt hàng.');
        }

        $user = Auth::user();
        $cartDetailIds = $request->query('cart_detail_ids', []);

        // Đảm bảo $cartDetailIds là một mảng
        if (! is_array($cartDetailIds)) {
            $cartDetailIds = explode(',', $cartDetailIds);
        }

        if (! empty($cartDetailIds)) {
            $cartItems = CartDetail::whereIn('cart_detail_id', $cartDetailIds)
                ->with(['product', 'variant'])
                ->get();
        } else {
            $cart = Cart::where('user_id', $user->user_id)->first();
            if (! $cart) {
                return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống.');
            }
            $cartItems = $cart->details()->with(['product', 'variant'])->get();
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống.');
        }

        $total = $cartItems->sum(function ($item) {
            return ($item->variant ? ($item->variant->price_sale ?? $item->variant->price) : ($item->product->price_sale ?? $item->product->price)) * $item->quantity;
        });

        $addresses = $user->userAddresses;
        $shippingFees = \App\Models\ShippingFee::all();
        $userAddress = $user->userAddresses->firstWhere('is_default', true);

        // Mặc định lấy phí ship ID = 3 (địa chỉ không rõ ràng)
        $defaultShippingFee = $shippingFees->firstWhere('shipping_id', 3);

        // Tìm phí vận chuyển khớp hoàn toàn với địa chỉ người dùng
        $shippingFee = null;
        if ($userAddress) {
            // Kiểm tra giá trị thực tế của city, district, ward
            // dd($userAddress->city, $userAddress->district, $userAddress->ward); // Debug
            $shippingFee = $shippingFees->firstWhere(function ($fee) use ($userAddress) {
                return strtolower($fee->province) === strtolower($userAddress->city) &&
                       strtolower($fee->district) === strtolower($userAddress->district) &&
                       strtolower($fee->ward) === strtolower($userAddress->ward);
            });
        }

        // Nếu không khớp, dùng phí mặc định (ID = 3)
        $shippingFee = $shippingFee ?: $defaultShippingFee;

        $shippingFeeValue = $shippingFee ? $shippingFee->fee : 50000; // fallback cuối cùng
        $shippingId = $shippingFee ? $shippingFee->shipping_id : null;
        $coupons = Coupon::orderBy('created_at', 'desc')->get();

        return view('order.create', compact(
            'cartItems',
            'total',
            'addresses',
            'shippingFees',
            'shippingFeeValue',
            'shippingId',
            'cartDetailIds',
            'coupons'
        ));

    }

    /**
     * Xử lý lưu đơn hàng
     */
public function store(Request $request)
{
    $user = Auth::user();

    DB::beginTransaction();

    try {
        $request->validate([
            'payment_method' => 'required|in:cod,wallet,vnpay,momo',
            'shipping_id' => 'required|exists:shipping_fees,shipping_id',
        ]);

        $cart = Cart::where('user_id', $user->user_id)->first();
        $addressId = $request->address_id;

        if (!$addressId && !$user->userAddresses->where('is_default', true)->first()) {
            return redirect()->back()->withErrors(['address' => 'Bạn cần chọn một địa chỉ hợp lệ trước khi đặt hàng.']);
        }

        $address = UserAddresses::where('user_id', $user->user_id)
            ->where('address_id', $addressId)
            ->first();

        if (!$address || empty($address->address_name) || empty($address->recipient_name) || empty($address->recipient_phone)) {
            return redirect()->back()->withErrors([
                'address' => 'Vui lòng cập nhật đầy đủ thông tin địa chỉ: tên địa chỉ, người nhận và số điện thoại.',
            ]);
        }

        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống.');
        }

        $selectedIds = $request->input('cart_detail_ids', []);
        if (empty($selectedIds)) {
            return redirect()->route('cart.index')->with('error', 'Không có sản phẩm nào được chọn để đặt hàng.');
        }

        $cartItems = $cart->details()
            ->whereIn('cart_detail_id', $selectedIds)
            ->with(['product', 'variant'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống hoặc không hợp lệ.');
        }

        // Kiểm tra tồn kho
        foreach ($cartItems as $item) {
            if ($item->variant) {
                $variant = ProductVariant::lockForUpdate()->find($item->variant->variant_id);
                if (!$variant || $variant->stock < $item->quantity) {
                    DB::rollBack();
                    return back()->with('error', "Biến thể '{$variant->name}' không đủ tồn kho. Còn lại: {$variant->stock}");
                }
            } else {
                $product = Product::lockForUpdate()->find($item->product->product_id);
                if (!$product || $product->stock < $item->quantity) {
                    DB::rollBack();
                    return back()->with('error', "Sản phẩm '{$product->name}' không đủ tồn kho. Còn lại: {$product->stock}");
                }
            }
        }

        $orderTotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price;
            $orderTotal += $price * $item->quantity;
        }

        $province = strtolower(trim($address->city));
        $shippingFee = ShippingFee::whereRaw('LOWER(province) = ?', [$province])->first();

        $orderDiscount = (int)$request->input('order_discount', 0);
        $shippingDiscount = (int)$request->input('shipping_discount', 0);
        $shippingFeeValue = max(0, (int)$request->input('shipping_fee', 0));

        $finalTotal = max(0, $orderTotal - $orderDiscount - $shippingDiscount + $shippingFeeValue);

        $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $paymentMethod = $request->payment_method;
        $statusId = $paymentMethod === 'vnpay' ? 9 : 1;
        $paymentStatus = $paymentMethod === 'vnpay' ? 'pending' : 'success';

        $order = Order::create([
            'order_code' => $orderCode,
            'user_id' => $user->user_id,
            'address_id' => $address->address_id,
            'total' => $finalTotal,
            'status_id' => $statusId,
            'shipping_fee' => $shippingFeeValue,
            'payment_method' => $paymentMethod,
            'shipping_id' => $request->shipping_id,
            'discount_amount' => $orderDiscount + $shippingDiscount,
            'payment_status' => $paymentStatus,
        ]);

        foreach ($cartItems as $item) {
            $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price;
            $subtotal = $price * $item->quantity;

            $orderDetail = $order->orderDetails()->create([
                'product_id' => $item->product->product_id,
                'variant_id' => $item->variant->variant_id ?? null,
                'quantity' => $item->quantity,
                'price' => $price,
                'discount_amount' => $orderDiscount + $shippingDiscount,
                'subtotal' => $subtotal,
                'total_price' => $subtotal,
                'product_name' => $item->product->name,
                'variant_name' => $item->variant
                    ? $item->variant->variantAttributes->pluck('attribute_name')->implode(', ')
                    : null,

                'attributes' => $item->variant
                    ? $item->variant->variantAttributes->pluck('attribute_value')->implode(', ')
                    : null,

                'original_price' => $item->variant->price_sale ?? $item->product->price_sale,
                'final_price' => $subtotal,
            ]);

            // Trừ kho
            if ($orderDetail->variant_id) {
                ProductVariant::where('variant_id', $orderDetail->variant_id)
                    ->decrement('stock', $orderDetail->quantity);
            } else {
                Product::where('product_id', $orderDetail->product_id)
                    ->decrement('stock', $orderDetail->quantity);
            }
        }

        // Xóa sản phẩm khỏi giỏ hàng
        $cart->details()->whereIn('cart_detail_id', $selectedIds)->delete();

        // Trừ tiền ví nếu cần
        if ($paymentMethod === 'wallet') {
            $wallet = $user->wallet;

            if (!$wallet || $wallet->balance < $finalTotal) {
                $order->orderDetails()->delete();
                $order->delete();
                DB::rollBack();
                return back()->with('error', 'Số dư ví không đủ để thanh toán đơn hàng.');
            }

            $wallet->decrement('balance', $finalTotal);
            $wallet->transactions()->create([
                'type' => 'payment',
                'amount' => $finalTotal,
                'description' => 'Thanh toán đơn hàng qua ví',
                'status' => 'completed',
            ]);
        }

        // Áp dụng mã giảm giá nếu có
        if ($request->order_coupon_id) {
            OrderCoupon::create([
                'order_id' => $order->order_id,
                'coupon_id' => $request->order_coupon_id,
                'applied_amount' => $orderDiscount,
            ]);
            Coupon::where('coupon_id', $request->order_coupon_id)->increment('usage_count');
        }

        if ($request->shipping_coupon_id) {
            OrderCoupon::create([
                'order_id' => $order->order_id,
                'coupon_id' => $request->shipping_coupon_id,
                'applied_amount' => $shippingDiscount,
            ]);
            Coupon::where('coupon_id', $request->shipping_coupon_id)->increment('usage_count');
        }

        DB::commit();

        return $paymentMethod === 'vnpay'
            ? redirect()->route('payment.vnpay.redirect', [
                'order_id' => $order->order_id,
                'amount' => $finalTotal,
            ])
            : redirect()->route('order.success')
                ->with('success', 'Đặt hàng thành công! Mã đơn hàng: ' . $orderCode)
                ->with('order', $order);

    } catch (QueryException $e) {
        DB::rollBack();
        if ($e->getCode() === '22003' || str_contains($e->getMessage(), 'Out of range value')) {
            return redirect()->back()->withInput()->withErrors(['total' => 'Tổng tiền đơn hàng quá lớn, vui lòng kiểm tra lại.']);
        }
        return redirect()->back()->withInput()->withErrors(['error' => 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->withErrors(['error' => 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage()]);
    }
}
    /**
     * Xử lý thanh toán đơn hàng qua Stripe
     */
    public function processPayment(Request $request, $order_id)
    {
        try {
            // Lấy đơn hàng cần thanh toán
            $order = Order::findOrFail($order_id);

            // Khởi tạo Stripe với secret key từ config/services.php
            Stripe::setApiKey(config('services.stripe.secret'));

            // Tạo charge trên Stripe (Stripe tính bằng cents)
            $charge = Charge::create([
                'amount' => $order->total * 100, // Chuyển sang cents
                'currency' => 'usd',
                'source' => $request->token,
                'description' => 'Thanh toán đơn hàng: '.$order->order_code,
            ]);

            if ($charge->status === 'succeeded') {
                // Cập nhật trạng thái đơn hàng nếu thanh toán thành công (ví dụ: status_id = 2: "Đã thanh toán")
                $order->update(['status_id' => 2]);

                return response()->json(['success' => true, 'message' => 'Thanh toán thành công!']);
            } else {
                return response()->json(['success' => false, 'error' => 'Thanh toán không thành công.']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hiển thị trang đơn hàng thành công
     */
    public function paymentSuccess()
    {
        $order = session('order');  // Lấy thông tin đơn hàng từ session

        return view('order.success', compact('order'));  // Truyền đơn hàng vào view
    }

    public function ensureCancelledStatus()
    {
        $statuses = [
            3 => ['name' => 'Đã hủy bởi người mua', 'description' => 'Người mua đã hủy đơn hàng'],
            5 => ['name' => 'Đã hủy', 'description' => 'Đơn hàng đã bị hủy'],
        ];

        foreach ($statuses as $status_id => $data) {
            // Kiểm tra xem trạng thái có tồn tại không
            $status = OrderStatus::where('status_id', $status_id)->first();

            // Nếu chưa tồn tại, thêm vào bảng order_statuses
            if (! $status) {
                OrderStatus::create([
                    'status_id' => $status_id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]);
            }
        }
    }

   public function cancel($order_id, Request $request)
{
    try {
        // Tìm đơn hàng
        $order = Order::with('orderDetails')->findOrFail($order_id);

        // Kiểm tra nếu đơn đã bị hủy
        if (in_array($order->status_id, [3, 5])) {
            return redirect()->route('order.index')->with('error', 'Đơn hàng này đã bị hủy.');
        }

        // Kiểm tra nếu đơn đang ở trạng thái mới (status_id == 1 hoặc 9)
        if (!in_array($order->status_id, [1, 9])) {
            return redirect()->route('order.index')->with('error', 'Chỉ có thể hủy đơn hàng mới.');
        }

        // Lấy lý do hủy (có thể null hoặc string)
        $cancelReason = $request->input('cancel_reason', null);

        // Cộng lại số lượng tồn kho cho từng sản phẩm trong đơn
        foreach ($order->orderDetails as $detail) {
            if ($detail->variant_id) {
                $variant = \App\Models\ProductVariant::find($detail->variant_id);
                if ($variant) {
                    $variant->increment('stock', $detail->quantity);
                }
            } else {
                $product = \App\Models\Product::find($detail->product_id);
                if ($product) {
                    $product->increment('stock', $detail->quantity);
                }
            }
        }

        // Tăng usage_count của mã giảm giá nếu có
        $orderCoupons = OrderCoupon::where('order_id', $order_id)->get();
        foreach ($orderCoupons as $orderCoupon) {
            $coupon = Coupon::find($orderCoupon->coupon_id);
            if ($coupon) {
                $coupon->decrement('usage_count');
            }
        }

        // Hoàn tiền nếu không phải COD
        if ($order->payment_method != 'cod') {
            if ($order->payment_method === 'wallet' || ($order->payment_method !== 'wallet' && $order->status_id == 1)) {
                $user = auth()->user();
                $wallet = \App\Models\Wallet::where('user_id', $user->user_id)->first();
                if (! $wallet) {
                    return redirect()->route('order.index')->with('error', 'Không tìm thấy ví để hoàn tiền.');
                }

                $wallet->increment('balance', $order->total);

                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->wallet_id,
                    'amount' => $order->total,
                    'type' => 'refund',
                    'description' => 'Hoàn tiền khi hủy đơn hàng #'.$order->order_code,
                    'status' => 'completed',
                ]);
            }
        }

        // Cập nhật trạng thái đơn hàng thành "Đã hủy" và lưu lý do hủy (nếu có)
        $order->status_id = 3;
        $order->cancel_reason = $cancelReason;
        $order->save();

        return redirect()->route('order.index')->with('success', 'Đã hủy đơn và cập nhật kho thành công.');
    } catch (\Exception $e) {
        Log::error('Lỗi khi hủy đơn: '.$e->getMessage());

        return redirect()->route('order.index')->with('error', 'Đã xảy ra lỗi khi hủy đơn.');
    }
}


    public function confirmReceived($order_id)
    {
        try {
            // Tìm đơn hàng theo ID
            $order = Order::findOrFail($order_id);

            // Kiểm tra trạng thái đơn hàng có phải là "Đã giao hàng" (id = 7)
            if ($order->status_id != 7) {
                return redirect()->route('order.index')->with('error', 'Đơn hàng không thể nhận do không phải trạng thái "Đã giao hàng".');
            }

            // Cập nhật trạng thái đơn hàng thành "Đã nhận hàng" (id = 4)
            $order->status_id = 4;
            $order->delivered_at = now();
            $order->save();

            return redirect()->route('order.index')->with('success', 'Nhận hàng thành công.');
        } catch (\Exception $e) {
            // Log lỗi nếu có exception
            Log::error('Lỗi khi nhận hàng: '.$e->getMessage());

            return redirect()->route('order.index')->with('error', 'Đã xảy ra lỗi khi nhận hàng.');
        }
    }

    public function returnOrder($order_id)
    {
        try {
            // Lấy đơn hàng theo ID
            $order = Order::findOrFail($order_id);

            // Kiểm tra nếu trạng thái đơn hàng là "Đã nhận hàng" (id = 4)
            if ($order->status_id == 4) {
                // Hoàn tiền vào ví người dùng
                $user = $order->user;
                $wallet = $user->wallet;

                if ($wallet) {
                    $wallet->balance += $order->total; // Cộng tiền vào ví
                    $wallet->save();

                    // Ghi lịch sử giao dịch hoàn tiền
                    \App\Models\WalletTransaction::create([
                        'wallet_id' => $wallet->wallet_id,
                        'amount' => $order->total,
                        'type' => 'refund',
                        'description' => 'Hoàn tiền cho đơn hàng #'.$order->order_code,
                        'status' => 'completed',
                    ]);
                }

                // Cập nhật trạng thái đơn hàng thành "Đơn trả hàng" (id = 8)
                $order->status_id = 8;
                $order->save();

                return redirect()->route('order.index')->with('success', 'Đơn hàng đã hoàn tiền và chuyển sang trạng thái Đơn trả hàng.');
            }

            return redirect()->route('order.index')->with('error', 'Đơn hàng không đủ điều kiện để hoàn tiền.');

        } catch (\Exception $e) {
            Log::error('Lỗi khi hoàn tiền đơn hàng: '.$e->getMessage());

            return redirect()->route('order.index')->with('error', 'Đã xảy ra lỗi khi hoàn tiền.');
        }
    }

    public function returnRequest(Request $request)
    {
        Log::info('Return request received for order_id: '.$request->order_id);

        // Kiểm tra dữ liệu gửi lên
        Log::info('Request Data: ', $request->all());

        // Validate request
        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'reason' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi,tmp|max:10240',
            'amount' => 'required|numeric',
        ]);

        // Tìm đơn hàng
        $order = Order::findOrFail($request->order_id);

        Log::info('Order found: ', ['order_id' => $order->order_id]);

        // Kiểm tra xem đã có yêu cầu trả hàng chưa
        if ($order->returnRequest) {
            Log::info('Return request already exists for order_id: '.$order->order_id);

            return redirect()->back()->with('error', 'Bạn đã gửi yêu cầu trước đó.');
        }

        // Xử lý file đính kèm
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                Log::info('Uploading file: '.$file->getClientOriginalName());
                $attachmentPaths[] = $file->store('refunds', 'public');
            }
        }

        Log::info('Attachments stored: ', $attachmentPaths);

        try {
            $refundRequest = RefundRequest::create([
                'order_id' => $order->order_id,
                'user_id' => auth()->id(),
                'reason' => $request->reason,
                'attachments' => json_encode($attachmentPaths),
                'amount' => $request->amount,
                'status' => 'pending',
            ]);

            Log::info('Refund request created successfully for order_id: '.$refundRequest->order_id);

            return redirect()->back()->with('success', 'Yêu cầu trả hàng đã được gửi.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo refund request: '.$e->getMessage());

            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi gửi yêu cầu trả hàng.');
        }

        // Ghi thông tin hoàn tất yêu cầu hoàn tiền
        Log::info('Refund request created for order_id: '.$refundRequest->order_id);

        // Trả về kết quả
        return redirect()->back()->with('success', 'Yêu cầu trả hàng đã được gửi.');
    }

    public function autoCompleteOrderStatus()
    {
        $orders = Order::where('status_id', 7)
            ->whereNotNull('delivered_at')
            ->get();

        foreach ($orders as $order) {
            $differenceInDays = now()->diffInDays($order->delivered_at);

            if ($differenceInDays >= 3) {
                $order->status_id = 6;
                $order->save();
            }
        }
    }

    public function submitReview(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'ratings' => 'required|array',
            'comments' => 'required|array',
        ]);

        $order = Order::with([
            'orderDetails.product',
            'orderDetails.variant.variantAttributeValues.variantAttribute',
        ])->findOrFail($request->order_id);

        // Kiểm tra điều kiện: người dùng có quyền đánh giá, trạng thái đơn hàng hợp lệ
        if ($order->user_id !== auth()->id() || $order->status_id != 4) {
            return back()->with('error', 'Không thể đánh giá đơn hàng này.');
        }

        // Mảng lưu các lỗi
        $errors = [];
        $orderDetails = $order->orderDetails->filter(function ($detail) {
            // Giả sử bạn có cột trạng thái chi tiết đơn (ví dụ: $detail->status)
            // hoặc dựa vào điều kiện biến thể hủy, bạn kiểm tra ở đây:
            return $detail->status != 'cancelled'; // hoặc điều kiện bạn dùng để xác định đã hủy
        });
        // Kiểm tra từng chi tiết đơn hàng
        foreach ($orderDetails as $orderDetail) {
            // Kiểm tra nếu sản phẩm đã có đánh giá thì bỏ qua
            if ($orderDetail->reviews()->exists()) {
                continue;
            }

            $id = $orderDetail->order_detail_id;

            // Kiểm tra xem rating và comment có hợp lệ không
            if (! isset($request->ratings[$id]) || ! isset($request->comments[$id])) {
                $errors[$id][] = 'Bạn phải đánh giá đầy đủ cho mỗi sản phẩm.';

                continue;
            }

            // Kiểm tra hình ảnh/video
            $mediaFiles = $request->file('media')[$id] ?? [];
            if (! $mediaFiles || count($mediaFiles) == 0) {
                $errors[$id][] = 'Phải có ít nhất 1 ảnh hoặc video.';

                continue;
            }

            $imageCount = 0;
            $videoCount = 0;
            $validExtensions = ['jpeg', 'jpg', 'png', 'mp4', 'webm', 'mov'];
            $mediaPaths = [];

            // Kiểm tra từng file media
            foreach ($mediaFiles as $file) {
                if ($file && $file->isValid()) {
                    $ext = strtolower($file->getClientOriginalExtension());

                    // Kiểm tra định dạng tệp
                    if (! in_array($ext, $validExtensions)) {
                        $errors[$id][] = "Tệp $ext không hợp lệ.";

                        continue;
                    }

                    // Kiểm tra số lượng ảnh và video
                    if (in_array($ext, ['jpeg', 'jpg', 'png'])) {
                        $imageCount++;
                    }
                    if (in_array($ext, ['mp4', 'webm', 'mov'])) {
                        $videoCount++;
                    }

                    if ($imageCount > 5) {
                        $errors[$id][] = 'Tối đa 5 ảnh.';
                        break;
                    }
                    if ($videoCount > 1) {
                        $errors[$id][] = 'Chỉ được 1 video.';
                        break;
                    }

                    $mediaPaths[] = $file->store('reviews', 'public');
                } else {
                    $errors[$id][] = 'Tệp không hợp lệ.';
                }
            }

            // Nếu không có lỗi, lưu đánh giá
            if (! isset($errors[$id])) {
                OrderReview::create([
                    'order_id' => $order->order_id,
                    'user_id' => auth()->id(),
                    'rating' => $request->ratings[$id],
                    'comment' => $request->comments[$id],
                    'media_paths' => $mediaPaths,
                    'order_detail_id' => $id,
                    'product_id' => $orderDetail->product_id,
                    'variant_id' => $orderDetail->variant_id ?? null,
                ]);
            }
        }

        // Nếu có lỗi, lưu lỗi vào session và không submit
        if (! empty($errors)) {
            // Lưu lỗi vào session và quay lại form
            Session::flash('review_errors', $errors);

            return back()->withInput();
        }

        // Kiểm tra xem tất cả sản phẩm trong đơn hàng đã được đánh giá chưa
        $allReviewed = $orderDetails->every(function ($detail) {
            return $detail->reviews()->exists();
        });

        // Nếu tất cả sản phẩm đã được đánh giá, cập nhật trạng thái đơn hàng
        if ($allReviewed) {
            $order->status_id = 6; // Đơn hàng đã hoàn tất
            $order->save();
        }

        // Trả về thông báo thành công
        return back()->with('success', 'Đánh giá thành công!');
    }
}
