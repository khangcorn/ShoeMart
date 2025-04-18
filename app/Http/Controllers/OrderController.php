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
use App\Models\ShippingFee; 
use App\Helpers\AddressHelper;
use App\Models\Refund;

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
    
        // Lấy địa chỉ mặc định của người dùng
        $defaultAddress = $user->userAddresses()->where('is_default', 1)->first();
    
        $shippingFee = null;
    
        // Kiểm tra nếu có địa chỉ mặc định, tìm phí vận chuyển
        if ($defaultAddress) {
            // Sử dụng hàm normalizeAddress để chuẩn hóa địa chỉ
            $province = normalizeAddress($defaultAddress->city); // ✅ đúng cột city từ user_addresses

            $district = normalizeAddress($defaultAddress->district);
            $ward     = normalizeAddress($defaultAddress->ward);
    
            // Tìm phí vận chuyển từ bảng shipping_fees
            $shippingFee = ShippingFee::where(function ($query) use ($province) {
                $query->where('province', $province)->orWhere('province', '*');
            })
            ->where(function ($q) use ($district) {
                $q->where('district', $district)->orWhereNull('district')->orWhere('district', '*');
            })
            ->where(function ($q) use ($ward) {
                $q->where('ward', $ward)->orWhereNull('ward')->orWhere('ward', '*');
            })
            ->orderByRaw("
                (province != '*') DESC,
                (district IS NOT NULL AND district != '*') DESC,
                (ward IS NOT NULL AND ward != '*') DESC
            ")
            ->first();
        
        
    
        return view('order.create', compact('cartItems', 'total', 'addresses', 'shippingFee'));
    }
    
    
}



    /**
     * Xử lý lưu đơn hàng
     */
    public function store(Request $request)
    {
        $request->validate([
            'address_id'     => 'required|exists:user_addresses,address_id',
            'payment_method' => 'required|in:cod,bank_transfer,credit_card,paypal',
            'shipping_id'    => 'nullable|exists:shipping_fees,shipping_id',
            'codes'           => 'nullable|string',  // Mã giảm giá nhập vào
        ]);
    
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->user_id)->first();
    
        if (!$cart || $cart->details()->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống.');
        }
    
        $cartItems = $cart->details()->with(['product', 'variant'])->get();
    
        DB::beginTransaction();
    
        try {
            // Tính tổng đơn hàng
            $orderTotal = $cartItems->sum(function ($item) {
                $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price_sale ?? $item->product->price;
                return $price * $item->quantity;
            });
    
            // Khởi tạo các biến cho mã giảm giá
            $discountAmount = 0;
            $shippingDiscount = 0;
            $couponOrder = null;  // Mã giảm giá cho đơn hàng
            $couponShipping = null;  // Mã giảm giá cho phí vận chuyển
    
            // Tính phí vận chuyển (dù có mã giảm giá hay không)
            $shippingFee = ShippingFee::find($request->shipping_id) ?: (object) ['fee' => 0]; // Mặc định là 0 nếu không có phí vận chuyển
    
            // Kiểm tra và tách mã giảm giá từ input
            if ($request->filled('codes')) {
                $codes = explode(',', $request->codes);  // Tách các mã giảm giá
            
                foreach ($codes as $code) {
                    $coupon = Coupon::where('code', trim($code))
                        ->where('status', 'active')
                        ->where('expiration_date', '>=', now())
                        ->first();
            
                    if ($coupon) {
                        if ($coupon->apply_to === 'order' && !$couponOrder) {
                            $couponOrder = $coupon;
                        } elseif ($coupon->apply_to === 'shipping' && !$couponShipping) {
                            $couponShipping = $coupon;
                        }
                    }
                }
            }
            
    
            // Xử lý mã giảm giá cho đơn hàng
            if ($couponOrder) {
                if ($couponOrder->usage_limit <= 0 || $couponOrder->usage_count >= $couponOrder->usage_limit) {
                    return back()->with('error', 'Mã giảm giá cho đơn hàng đã được sử dụng hết.');
                }
    
                // Kiểm tra giá trị tối thiểu
                if ($couponOrder->min_order_value && $orderTotal < $couponOrder->min_order_value) {
                    return back()->with('error', 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã giảm giá.');
                }
    
                if ($couponOrder->apply_to === 'order') {
                    if ($couponOrder->discount_type === 'percentage') {
                        $discountAmount = $orderTotal * ($couponOrder->discount_value / 100);
    
                        // Áp dụng giới hạn tối đa nếu có
                        if ($couponOrder->max_discount_value) {
                            $discountAmount = min($discountAmount, $couponOrder->max_discount_value);
                        }
                    } elseif ($couponOrder->discount_type === 'fixed') {
                        $discountAmount = $couponOrder->discount_value;
                    }
                }
            }
    
            // Xử lý mã giảm giá cho phí vận chuyển
            if ($couponShipping) {
                if ($couponShipping->usage_limit <= 0 || $couponShipping->usage_count >= $couponShipping->usage_limit) {
                    return back()->with('error', 'Mã giảm giá vận chuyển đã được sử dụng hết.');
                }
    
                // Kiểm tra giá trị tối thiểu đơn hàng
                if ($couponShipping->min_order_value && $orderTotal < $couponShipping->min_order_value) {
                    return back()->with('error', 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã giảm giá vận chuyển.');
                }
    
                if ($couponShipping->apply_to === 'shipping') {
                    if ($couponShipping->discount_type === 'percentage') {
                        $shippingDiscount = $shippingFee->fee * ($couponShipping->discount_value / 100);
    
                        // Áp dụng giới hạn tối đa nếu có
                        if ($couponShipping->max_discount_value) {
                            $shippingDiscount = min($shippingDiscount, $couponShipping->max_discount_value);
                        }
                    } elseif ($couponShipping->discount_type === 'fixed') {
                        $shippingDiscount = $couponShipping->discount_value;
                    }
                }
            }
    
            // Tính tổng đơn hàng sau khi áp dụng mã giảm giá
            $finalTotal = $orderTotal + ($shippingFee->fee ?? 0) - $discountAmount - $shippingDiscount;
    
            // Tạo đơn hàng
            $order = Order::create([
                'order_code'     => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'user_id'        => $user->user_id,
                'address_id'     => $request->address_id,
                'status_id'      => 2,
                'shipping_id'    => $request->shipping_id,
                'shipping_fee'   => $shippingFee->fee ?? 0, // Lưu phí vận chuyển vào database
                'shipping_discount'  => $shippingDiscount,
                'discount_amount'=> $discountAmount,
                'payment_method' => $request->payment_method,
                'total'          => $finalTotal,
                'coupon_id'      => $couponOrder?->coupon_id,  // Lưu couponOrder nếu có
            ]);
    
            // Thêm chi tiết đơn hàng
            foreach ($cartItems as $item) {
                $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price_sale ?? $item->product->price;
                $subtotal = $price * $item->quantity;
    
                $order->orderDetails()->create([
                    'product_id'      => $item->product->product_id,
                    'variant_id'      => $item->variant?->variant_id,
                    'quantity'        => $item->quantity,
                    'price'           => $price,
                    'discount_amount' => 0,
                    'subtotal'        => $subtotal,
                    'total_price'     => $subtotal,
                ]);
    
                // Cập nhật lại số lượng tồn kho
                if ($item->variant) {
                    $item->variant->decrement('stock', $item->quantity);
                }
            }
    
            // Áp dụng mã giảm giá vào đơn hàng
            if ($couponOrder) {
                OrderCoupon::create([
                    'order_id'       => $order->order_id,
                    'coupon_id'      => $couponOrder->coupon_id,
                    'applied_amount' => $discountAmount,
                ]);
    
                $couponOrder->decrement('usage_limit');
                $couponOrder->increment('usage_count');
            }
    
            // Áp dụng mã giảm giá phí vận chuyển (nếu có)
            if ($couponShipping) {
                OrderCoupon::create([
                    'order_id'       => $order->order_id,
                    'coupon_id'      => $couponShipping->coupon_id,
                    'applied_amount' => $shippingDiscount,
                ]);
    
                $couponShipping->decrement('usage_limit');
                $couponShipping->increment('usage_count');
            }
    
            // Xóa chi tiết giỏ hàng
            $cart->details()->delete();
    
            DB::commit();
    
            // Trả về view thành công
            return view('order.success', [
                'shippingDiscount' => $shippingDiscount,
                'discount' => $discountAmount,
                'finalTotal' => $finalTotal,
                'subtotal' => $orderTotal,
                'shipping' => $shippingFee->fee ?? 0
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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
        $order = Order::find($order_id);
        
        // Chỉ cho phép hủy nếu đơn hàng đang "Chờ xác nhận" (status_id = 2)
        if ($order && $order->status_id == 2) {
            $order->status_id = 6; // "Đã hủy"
            $order->save();
    
            return redirect()->route('order.index')->with('success', 'Đơn hàng đã được hủy.');
        }
    
        return redirect()->route('order.index')->with('error', 'Không thể hủy đơn hàng.');
    }
    public function requestRefund(Request $request)
    {
        // Kiểm tra quyền sở hữu đơn hàng
        $order = Order::findOrFail($request->order_id);
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
    
        // Kiểm tra điều kiện hoàn tiền (Đơn hàng đã giao và chưa có yêu cầu hoàn tiền)
        if ($order->status_id != 5 || $order->refund) {
            return back()->with('error', 'Đơn hàng không đủ điều kiện hoàn tiền.');
        }
    
        // Xử lý tải lên hình ảnh và video (nếu có)
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('refund_attachments', 'public');
                $attachments[] = $path;
            }
        }
    
        // Tạo yêu cầu hoàn tiền
        Refund::create([
            'order_id' => $order->order_id,
            'user_id' => Auth::id(),
            'amount' => $order->total,
            'status' => 'pending', // Trạng thái ban đầu là chờ xử lý
            'reason' => $request->reason,
            'attachments' => json_encode($attachments),
        ]);
    
        // Cập nhật trạng thái đơn hàng (đánh dấu đã yêu cầu hoàn tiền)
        $order->update(['refund' => true]);
    
        return back()->with('success', 'Yêu cầu hoàn tiền đã được gửi. Đơn hàng của bạn sẽ được xử lý.');
    }
    
    

}
