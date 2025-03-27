<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Lấy danh sách sản phẩm trong giỏ hàng
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem giỏ hàng.');
        }

        // Lấy tất cả sản phẩm trong giỏ hàng của user
        $cartItems = CartDetail::whereHas('cart', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('product', 'variant')->get();

        $total = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'variant_id' => 'nullable|exists:product_variants,variant_id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.'], 401);
        }
    
        // Tìm giỏ hàng của user, nếu chưa có thì tạo mới
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
    
        // Xác định giá sản phẩm dựa trên biến thể hoặc sản phẩm gốc
        if ($request->variant_id) {
            $variant = ProductVariant::find($request->variant_id);
            $price = $variant->price ?? 0;
        } else {
            $product = Product::find($request->product_id);
            $price = $product->price ?? 0;
        }
    
        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $cartItem = CartDetail::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();
    
        if ($cartItem) {
            // Nếu đã có, cập nhật số lượng
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Nếu chưa có, tạo mới
            CartDetail::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                'price' => $price,
            ]);
        }
    
        // Lấy danh sách sản phẩm trong giỏ hàng sau khi thêm
        $cartItems = CartDetail::whereHas('cart', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('product')->get();
    
        // Tính tổng tiền
        $total = $cartItems->sum(fn($item) => $item->price * $item->quantity);
    
        return response()->json([
            'message' => 'Sản phẩm đã được thêm vào giỏ hàng.',
            'cartItems' => $cartItems,
            'total' => number_format($total, 0, ',', '.')
        ]);
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartDetail = CartDetail::findOrFail($id);
        $cartDetail->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Số lượng đã được cập nhật', 'cartDetail' => $cartDetail]);
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function destroy($id)
    {
        $cartDetail = CartDetail::findOrFail($id);
        $cartDetail->delete();

        return response()->json(['message' => 'Sản phẩm đã được xóa khỏi giỏ hàng']);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->cartDetails()->delete();
        }

        return response()->json(['message' => 'Giỏ hàng đã được làm trống']);
    }
}
