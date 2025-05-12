<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Stripe\Stripe;
use Stripe\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderCoupon;
use App\Models\UserAddress;
use App\Models\User; // Nếu chưa có
use App\Models\Coupon;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RefundRequest;
use App\Models\ShippingFee;
use App\Models\UserAddresses;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;

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
             'orderDetails.product',
             'orderDetails.variant.attributes.variantAttribute',
             'orderCoupons', // THÊM DÒNG NÀY
             'status'
         ])->find($order_id);
     
         if (!$order) {
             return redirect()->route('order.index')->with('error', 'Đơn hàng không tồn tại.');
         }
     
         return view('order.show', compact('order'));
     }
     
     

    /**
     * Hiển thị giao diện đặt hàng (chi tiết đơn hàng)
     */
public function create(Request $request)
{
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để đặt hàng.');
    }

    $user = Auth::user();
    $cartDetailIds = $request->query('cart_detail_ids', []);

    // Đảm bảo $cartDetailIds là một mảng
    if (!is_array($cartDetailIds)) {
        $cartDetailIds = explode(',', $cartDetailIds);
    }

    if (!empty($cartDetailIds)) {
        $cartItems = CartDetail::whereIn('cart_detail_id', $cartDetailIds)
            ->with(['product', 'variant'])
            ->get();
    } else {
        $cart = Cart::where('user_id', $user->user_id)->first();
        if (!$cart) {
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
    $userAddress = $addresses->first(); // Có thể thay bằng address đã chọn từ request nếu có

    // Tìm phí vận chuyển khớp hoàn toàn
    $shippingFee = $shippingFees->firstWhere(function ($fee) use ($userAddress) {
        return $fee->province === $userAddress->city &&
               $fee->district === $userAddress->district &&
               $fee->ward === $userAddress->ward;
    });

    $shippingFeeValue = $shippingFee ? $shippingFee->fee : 120000;
    
    return view('order.create', compact('cartItems', 'total', 'addresses', 'shippingFees', 'shippingFeeValue', 'cartDetailIds'));
}


    /**
     * Xử lý lưu đơn hàng
     */
public function store(Request $request)
{

    $request->validate([
        'address_id'     => 'required|exists:user_addresses,address_id',
        'payment_method'  => 'required|in:cod,bank_transfer,credit_card,paypal,wallet',
    ]);

    $user = Auth::user();
    $cart = Cart::where('user_id', $user->user_id)->first();

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

    // Tính tổng tiền của giỏ hàng
    $orderTotal = 0;
    foreach ($cartItems as $item) {
        $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price;
        $orderTotal += $price * $item->quantity;
    }

    // Lấy thông tin địa chỉ và tính phí ship
$address = UserAddresses::findOrFail($request->address_id);
$province = strtolower(trim($address->city));  // Lấy tỉnh từ city
$shippingFeeValue = (int) $request->input('shipping_fee', 0);

    // Tìm shipping fee dựa trên tỉnh
    $shippingFee = ShippingFee::whereRaw('LOWER(province) = ?', [$province])->first();
    if (!$shippingFee) {
        return back()->with('error', 'Không tìm thấy phí vận chuyển phù hợp.');
    }

    // Giảm giá đơn hàng và phí ship
  $orderDiscount = $request->input('order_discount', 0);
$shippingDiscount = $request->input('shipping_discount', 0);
$finalTotal = max(0, $orderTotal + $shippingFeeValue - $orderDiscount - $shippingDiscount);



    // Thanh toán qua ví
    if ($request->payment_method === 'wallet') {
        $wallet = $user->wallet;
        if (!$wallet || $wallet->balance < $finalTotal) {
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

    // Tạo mã đơn hàng
    $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    $order = Order::create([
        'order_code'      => $orderCode,
        'user_id'         => $user->user_id,
        'address_id'      => $request->address_id,
        'total_price'           => $finalTotal,
        'status_id'       => 1,
        'shipping_fee'    => $shippingFeeValue,
        'payment_method'  => $request->payment_method,
        'shipping_id'     => $shippingFee->shipping_id,  // Lưu ID của phí vận chuyển
        'discount_amount' => $orderDiscount + $shippingDiscount,
    ]);

    // Lưu mã giảm giá vào bảng trung gian nếu có
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

  $totalDiscount = $orderDiscount + $shippingDiscount;
$totalQuantity = $cartItems->sum('quantity');
$discountPerItem = $totalQuantity > 0 ? $totalDiscount / $totalQuantity : 0;

foreach ($cartItems as $item) {
    $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price;
    $subtotal = $price * $item->quantity;

    // Phân bổ giảm giá theo số lượng sản phẩm
    $discountForItem = round($discountPerItem * $item->quantity);
    $totalPrice = max(0, $subtotal - $discountForItem);

    $order->orderDetails()->create([
        'product_id'      => $item->product->product_id,
        'variant_id'      => $item->variant ? $item->variant->variant_id : null,
        'quantity'        => $item->quantity,
        'price'           => $price,
        'discount_amount' => $discountForItem,
        'subtotal'        => $subtotal,
        'total_price'     => $totalPrice,
    ]);


    // Trừ tồn kho
    if ($item->variant) {
        $item->variant->decrement('stock', $item->quantity);
    }
}


    // Xóa giỏ hàng sau khi đặt hàng
    $cart->details()->delete();

    return redirect()->route('order.success')->with('success', 'Đặt hàng thành công! Mã đơn hàng: ' . $orderCode);
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
                'amount'      => $order->total * 100, // Chuyển sang cents
                'currency'    => 'usd',
                'source'      => $request->token,
                'description' => "Thanh toán đơn hàng: " . $order->order_code,
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
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hiển thị trang đơn hàng thành công
     */
    public function paymentSuccess()
    {
        return view('order.success');
    }
    public function ensureCancelledStatus()
    {
        $statuses = [
            3 => ['name' => 'Đã hủy bởi người mua', 'description' => 'Người mua đã hủy đơn hàng'],
            5 => ['name' => 'Đã hủy', 'description' => 'Đơn hàng đã bị hủy']
        ];
    
        foreach ($statuses as $status_id => $data) {
            // Kiểm tra xem trạng thái có tồn tại không
            $status = OrderStatus::where('status_id', $status_id)->first();
    
            // Nếu chưa tồn tại, thêm vào bảng order_statuses
            if (!$status) {
                OrderStatus::create([
                    'status_id'   => $status_id,
                    'name'        => $data['name'],
                    'description' => $data['description'],
                ]);
            }
        }
    }
    
    public function cancel($order_id)
    {
        try {
            // Tìm đơn hàng
            $order = Order::with('orderDetails')->findOrFail($order_id);
    
            // Kiểm tra nếu đơn đã bị hủy
            if (in_array($order->status_id, [3, 5])) {
                return redirect()->route('order.index')->with('error', 'Đơn hàng này đã bị hủy.');
            }
    
            // Kiểm tra nếu đơn đang ở trạng thái mới (status_id == 1)
            if ($order->status_id != 1) {
                return redirect()->route('order.index')->with('error', 'Chỉ có thể hủy đơn hàng mới.');
            }
    
            // Cộng lại số lượng tồn kho cho từng sản phẩm trong đơn
            foreach ($order->orderDetails as $detail) {
                if ($detail->variant_id) {
                    $variant = \App\Models\ProductVariant::find($detail->variant_id);
                    if ($variant) {
                        $variant->increment('stock', $detail->quantity);
                    }
                }
            }
    
             // Kiểm tra nếu đơn có mã giảm giá, tăng usage_count của mã giảm giá
             $orderCoupons = OrderCoupon::where('order_id', $order_id)->get(); // Lấy tất cả các mã giảm giá của đơn hàng
             foreach ($orderCoupons as $orderCoupon) {
                 // Lấy coupon từ coupon_id trong bảng order_coupons
                 $coupon = Coupon::find($orderCoupon->coupon_id);
                 if ($coupon) {
                     // Tăng usage_count của mã giảm giá
                     $coupon->decrement('usage_count');
                 }
             }
    
            // Nếu không phải COD thì hoàn tiền vào ví
            if ($order->payment_method != 'cod') {
                $user = auth()->user();
                $wallet = \App\Models\Wallet::where('user_id', $user->user_id)->first();
                if (!$wallet) {
                    return redirect()->route('order.index')->with('error', 'Không tìm thấy ví để hoàn tiền.');
                }
    
                $wallet->increment('balance', $order->total);
    
                \App\Models\WalletTransaction::create([
                    'wallet_id'   => $wallet->wallet_id,
                    'amount'      => $order->total,
                    'type'        => 'refund',
                    'description' => 'Hoàn tiền khi hủy đơn hàng #' . $order->order_code,
                    'status'      => 'completed',
                ]);
            }
    
            // Cập nhật trạng thái đơn hàng thành "Đã hủy"
            $order->status_id = 3;  
            $order->save();
    
            return redirect()->route('order.index')->with('success', 'Đã hủy đơn và cập nhật kho thành công.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi hủy đơn: ' . $e->getMessage());
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
            $order->save();
        
            
            return redirect()->route('order.index')->with('success', 'Nhận hàng thành công.');
        } catch (\Exception $e) {
            // Log lỗi nếu có exception
            Log::error('Lỗi khi nhận hàng: ' . $e->getMessage());
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
                        'description' => 'Hoàn tiền cho đơn hàng #' . $order->order_code,
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
            Log::error('Lỗi khi hoàn tiền đơn hàng: ' . $e->getMessage());
            return redirect()->route('order.index')->with('error', 'Đã xảy ra lỗi khi hoàn tiền.');
        }
    }
    public function returnRequest(Request $request)
    {
        Log::info('Return request received for order_id: ' . $request->order_id);
        
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
            Log::info('Return request already exists for order_id: ' . $order->order_id);
            return redirect()->back()->with('error', 'Bạn đã gửi yêu cầu trước đó.');
        }
    
        // Xử lý file đính kèm
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                Log::info('Uploading file: ' . $file->getClientOriginalName());
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
        
            Log::info('Refund request created successfully for order_id: ' . $refundRequest->order_id);
            return redirect()->back()->with('success', 'Yêu cầu trả hàng đã được gửi.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo refund request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi gửi yêu cầu trả hàng.');
        }
        
        // Ghi thông tin hoàn tất yêu cầu hoàn tiền
        Log::info('Refund request created for order_id: ' . $refundRequest->order_id);
        
        // Trả về kết quả
        return redirect()->back()->with('success', 'Yêu cầu trả hàng đã được gửi.');
    }
    
    
    

    public function autoCompleteOrderStatus()
    {
        // Cập nhật tất cả các đơn hàng có trạng thái "Đã giao hàng" (id = 7)
        $orders = Order::where('status_id', 7)->get();
        foreach ($orders as $order) {
            $orderDate = $order->created_at;
            $now = now();
            $differenceInDays = $now->diffInDays($orderDate);
    
            // Nếu đơn hàng đã giao và đã quá 7 ngày thì chuyển sang trạng thái "Đơn hoàn thành" (id = 8)
            if ($differenceInDays >= 7) {
                $order->status_id = 8;
                $order->save();
            }
        }
    }
    
    

}