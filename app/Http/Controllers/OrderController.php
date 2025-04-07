<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingFee; 

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
         $order = Order::with(['userAddresses', 'orderDetails.product', 'status'])->find($order_id);
     
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
        $shippingFees = ShippingFee::all();
    
        return view('order.create', compact('cartItems', 'total', 'addresses', 'shippingFees'));
    }
    



    /**
     * Xử lý lưu đơn hàng
     */
    public function store(Request $request)
    {
        // Validate thông tin đặt hàng
        $request->validate([
            'address_id'     => 'required|exists:user_addresses,address_id',
            'payment_method' => 'required|in:cod,bank_transfer,credit_card,paypal',
            'shipping_id'    => 'required|exists:shipping_fees,shipping_id',  // Kiểm tra trường shipping_id
        ]);

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->user_id)->first();
        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống.');
        }
        $cartItems = $cart->details()->with(['product', 'variant'])->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống.');
        }

        // Tính tổng tiền giỏ hàng
        $orderTotal = 0;
        foreach ($cartItems as $item) {
            if ($item->variant) {
                $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price;
            } else {
                $price = $item->product->price_sale ?? $item->product->price;
            }
            $orderTotal += $price * $item->quantity;
        }

        // Lấy thông tin phí vận chuyển từ bảng shipping_fees
        $shippingFee = ShippingFee::find($request->shipping_id);  // Lấy phí vận chuyển theo shipping_id

        // Tính tổng tiền bao gồm phí vận chuyển
        $finalTotal = $orderTotal + $shippingFee->fee; // Cộng phí vận chuyển vào tổng đơn hàng
        
        // Tạo mã đơn hàng độc nhất: ORD-YYYYMMDD-XXXXXX
        $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Tạo đơn hàng (bản ghi trong bảng orders)
        $order = Order::create([
            'order_code'     => $orderCode,
            'user_id'        => $user->user_id,
            'address_id'     => $request->address_id, // Địa chỉ đã chọn
            'total'          => $finalTotal, // Tổng tiền có bao gồm phí vận chuyển
            'status_id'      => 1, // 1: "Mới"
            'shipping_fee'   => $shippingFee->fee, // Lưu phí vận chuyển vào đơn hàng
            'payment_method' => $request->payment_method,
            'shipping_id'    => $request->shipping_id,  // Lưu shipping_id
        ]);

        // Tạo chi tiết đơn hàng cho từng mặt hàng
        foreach ($cartItems as $item) {
            if ($item->variant) {
                $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price;
            } else {
                $price = $item->product->price_sale ?? $item->product->price;
            }
            $subtotal = $price * $item->quantity;

            $order->orderDetails()->create([
                'product_id'      => $item->product->product_id,
                'variant_id'      => $item->variant ? $item->variant->variant_id : null,
                'quantity'        => $item->quantity,
                'price'           => $price,
                'discount_amount' => 0, // Nếu có giảm giá, cập nhật ở đây
                'subtotal'        => $subtotal,
                'total_price'     => $subtotal, // Nếu có giảm giá: subtotal - discount_amount
            ]);
        }

        // Xóa giỏ hàng sau khi đặt hàng thành công
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
    // Tìm đơn hàng
    $order = Order::find($order_id);
    
    // Kiểm tra nếu đơn hàng tồn tại và chưa bị hủy
    if ($order && $order->status_id != 3 && $order->status_id != 5) {
        // Cập nhật trạng thái của đơn hàng thành "Đã hủy bởi người mua" (status_id = 3)
        $order->status_id = 3; 
        $order->save();

        // Trả về thông báo thành công
        return redirect()->route('order.index')->with('success', 'Đơn hàng đã được hủy bởi bạn.');
    }

    return redirect()->route('order.index')->with('error', 'Không thể hủy đơn hàng.');
}

}
