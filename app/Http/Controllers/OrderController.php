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
            $shippingFee = ShippingFee::where('province', $province)
            ->where(function ($q) use ($district) {
                $q->whereNull('district')->orWhere('district', $district);
            })
            ->where(function ($q) use ($ward) {
                $q->whereNull('ward')->orWhere('ward', $ward);
            })
            ->first();
        
        }
    
        return view('order.create', compact('cartItems', 'total', 'addresses', 'shippingFee'));
    }
    
    
    



    /**
     * Xử lý lưu đơn hàng
     */
    public function store(Request $request)
    {
        $request->validate([
            'address_id'     => 'required|exists:user_addresses,address_id',
            'payment_method' => 'required|in:cod,bank_transfer,credit_card,paypal',
            'shipping_id'    => 'required|exists:shipping_fees,shipping_id',
            'code'           => 'nullable|string',
        ]);
    
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->user_id)->first();
    
        if (!$cart || $cart->details()->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống.');
        }
    
        $cartItems = $cart->details()->with(['product', 'variant'])->get();
    
        DB::beginTransaction();
    
        try {
            $orderTotal = $cartItems->sum(function ($item) {
                $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price_sale ?? $item->product->price;
                return $price * $item->quantity;
            });
    
            // 👉 Mã giảm giá
            $discountAmount = 0;
            $coupon = null;
    
            if ($request->filled('code')) {
                $coupon = Coupon::where('code', $request->code)
                    ->where('status', 'active')
                    ->where('expiration_date', '>=', now())
                    ->first();
    
                if (!$coupon) {
                    return back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
                }
    
                if ($coupon->usage_limit <= 0) {
                    return back()->with('error', 'Mã giảm giá đã được sử dụng hết.');
                }
    
                $alreadyUsed = OrderCoupon::whereHas('order', function ($query) use ($user) {
                    $query->where('user_id', $user->user_id);
                })->where('coupon_id', $coupon->coupon_id)->exists();
    
                if ($alreadyUsed) {
                    return back()->with('error', 'Bạn đã sử dụng mã giảm giá này rồi.');
                }
    
                // Nếu có giới hạn đơn hàng tối thiểu
                if ($coupon->min_order_value && $orderTotal < $coupon->min_order_value) {
                    return back()->with('error', 'Đơn hàng của bạn chưa đạt mức tối thiểu để áp dụng mã.');
                }
    
                $discountAmount = $coupon->discount_type === 'percentage'
                    ? $orderTotal * ($coupon->discount_value / 100)
                    : $coupon->discount_value;
    
                if ($coupon->max_discount_value) {
                    $discountAmount = min($discountAmount, $coupon->max_discount_value);
                }
    
                $discountAmount = min($discountAmount, $orderTotal);
                $discountAmount = round($discountAmount); // Làm tròn để tránh số lẻ
            }
    
            $shippingFee = ShippingFee::findOrFail($request->shipping_id);
            $finalTotal = $orderTotal + $shippingFee->fee - $discountAmount;
    
            $order = Order::create([
                'order_code'     => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'user_id'        => $user->user_id,
                'address_id'     => $request->address_id,
                'status_id'      => 2,
                'shipping_id'    => $request->shipping_id,
                'shipping_fee'   => $shippingFee->fee,
                'discount_amount'=> $discountAmount,
                'payment_method' => $request->payment_method,
                'total'          => $finalTotal,
                'coupon_id'      => $coupon?->coupon_id,
            ]);
    
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
    
                if ($item->variant) {
                    $item->variant->decrement('stock', $item->quantity);
                }
            }
    
            if ($coupon) {
                OrderCoupon::create([
                    'order_id'       => $order->order_id,
                    'coupon_id'      => $coupon->coupon_id,
                    'applied_amount' => $discountAmount,
                ]);
    
                $coupon->decrement('usage_limit');
                $coupon->increment('usage_count');
            }
    
            $cart->details()->delete();
    
            DB::commit();
            return redirect()->route('order.success')->with('success', 'Đặt hàng thành công!');
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
    

}
