<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class CartController extends Controller
{
    /**
     * Lấy danh sách sản phẩm trong giỏ hàng
     */
    public function index()
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem giỏ hàng.');
        }

        // Lấy danh sách sản phẩm trong giỏ hàng
        $cartItems = CartDetail::whereHas('cart', function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        })->with(['product.images', 'variant.attributes.variantAttribute'])->get();

        // Tính tổng tiền (ưu tiên giá khuyến mãi nếu có)
        $total = $cartItems->sum(function ($item) {
            // Lấy giá của sản phẩm hoặc biến thể
            if ($item->variant) {
                $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price;
            } else {
                $price = $item->product->price_sale ?? $item->product->price;
            }

            return $price * $item->quantity;
        });

        // Tính số lượng sản phẩm đã có trong giỏ hàng
        $cartQuantity = [];
        foreach ($cartItems as $item) {
            $cartQuantity[$item->product_id] = $item->quantity;
        }

        return view('cart.index', compact('cartItems', 'total', 'cartQuantity'));

    }

    private function getProductPrice($productId, $variantId = null)
    {
        if ($variantId) {
            $productVariant = ProductVariant::findOrFail($variantId);

            return $productVariant->price_sale ?? $productVariant->price;
        }
        $product = Product::findOrFail($productId);

        return $product->price_sale ?? $product->price;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart(Request $request)
    {
        try {
            
            Log::info('Request data:', $request->all()); // log toàn bộ input nhận được
            $intendedUrl = $request->input('current_url', url()->previous());
            Log::info('URL intended nhận:', ['url' => $intendedUrl]);

            if (!Auth::check()) {
                // Lưu session cart tạm
                $cartItems = session('cart.items', []);
                $cartItems[] = [
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'quantity' => $request->quantity,
                ];
                session(['cart.items' => $cartItems]);

                session(['url.intended' => $intendedUrl]);
                Log::info('URL intended lưu vào session:', ['url' => session('url.intended')]);

                return response()->json([
                    'message' => 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.',
                    'redirect' => route('login')
                ], 401);
            }

            $user = Auth::user();

            // Validate dữ liệu đầu vào
            $request->validate([
                'product_id' => 'required|exists:products,product_id',
                'variant_id' => 'nullable|exists:product_variants,variant_id',
                'quantity' => 'required|integer|min:1',
            ]);

            // Kiểm tra giỏ hàng có tồn tại không, nếu chưa thì tạo mới
            $cart = Cart::firstOrCreate(['user_id' => $user->user_id]);

            // Xác định giá sản phẩm và kiểm tra tồn kho
            if ($request->variant_id) {
                $productVariant = ProductVariant::findOrFail($request->variant_id);
                $stock = $productVariant->stock;
                $price = $productVariant->price_sale ?? $productVariant->price ?? 0;
            } else {
                $product = Product::findOrFail($request->product_id);
                $stock = $product->stock;
                $price = $product->price_sale ?? $product->price ?? 0;
            }

            // Kiểm tra tổng số lượng trong giỏ hàng
            $cartItem = CartDetail::where('cart_id', $cart->cart_id)
                ->where('product_id', $request->product_id)
                ->where('variant_id', $request->variant_id)
                ->first();

            $currentQuantityInCart = $cartItem ? $cartItem->quantity : 0;
            $remainingStock = $stock - $currentQuantityInCart;

            // Kiểm tra số lượng thêm vào có vượt quá tồn kho không
            if ($request->quantity > $remainingStock) {
                return response()->json([
                    'message' => "Bạn đã có $currentQuantityInCart sản phẩm trong giỏ. Không thể thêm số lượng đã chọn.",
                ], 400);
            }

            if ($cartItem) {
                // Cộng dồn số lượng nhưng không vượt quá tồn kho
                $cartItem->update([
                    'quantity' => \Illuminate\Support\Facades\DB::raw("quantity + {$request->quantity}"),
                    'price' => $price, // Cập nhật giá nếu có sự thay đổi
                ]);
            } else {
                // Nếu chưa có, tạo mới
                CartDetail::create([
                    'cart_id' => $cart->cart_id,
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'quantity' => $request->quantity,
                    'price' => $price, // Gán giá cho sản phẩm khi tạo mới
                ]);
            }

            return response()->json(['message' => 'Thêm vào giỏ hàng thành công!']);
        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm vào giỏ hàng: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thêm vào giỏ hàng.',
            ]);
        }
    }

    public function update(Request $request, $cartDetailId)
    {
        try {
            $cartItem = CartDetail::findOrFail($cartDetailId);

            $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            // Kiểm tra nếu số lượng có thay đổi
            if ($cartItem->quantity != $request->quantity) {
                $cartItem->update(['quantity' => $request->quantity]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật số lượng thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function destroy($cartDetailId)
    {
        try {
            $userId = Auth::id();

            // Lấy giỏ hàng của user
            $cart = Cart::where('user_id', $userId)->first();

            if (! $cart) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy giỏ hàng của bạn!']);
            }

            // Tìm sản phẩm trong giỏ hàng
            $cartDetail = $cart->details()->where('cart_detail_id', $cartDetailId)->first();

            if (! $cartDetail) {
                return response()->json(['success' => false, 'message' => '⚠️ Sản phẩm không tồn tại hoặc đã bị xóa!']);
            }

            // Xóa sản phẩm
            $cartDetail->delete();

            // Kiểm tra lại nếu giỏ hàng đã trống sau khi xóa
            if (! $cart->details()->exists()) {
                return response()->json(['success' => true, 'message' => '🛒 Giỏ hàng hiện đã trống!']);
            }

            return response()->json(['success' => true, 'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Đã có lỗi xảy ra, vui lòng thử lại sau.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function clearCart()
    {
        try {
            $userId = Auth::id();

            // Lấy giỏ hàng của user
            $cart = Cart::where('user_id', $userId)->first();

            if (! $cart) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ Giỏ hàng của bạn đã trống, không có gì để xóa!',
                ], 400);
            }

            // Xóa toàn bộ sản phẩm trong giỏ hàng
            $cart->details()->delete();

            return response()->json([
                'success' => true,
                'message' => '🛒 Giỏ hàng đã được xóa thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Lỗi khi xóa giỏ hàng.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkoutSelected(Request $request)
    {
        $cartDetailIds = $request->input('cart_details');

        if (empty($cartDetailIds)) {
            return redirect()->route('cart.index')->with('error', 'Không có sản phẩm nào được chọn.');
        }

        $user = auth()->user();
        $cart = Cart::where('user_id', $user->user_id)->first();

        if (! $cart) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }

        // Lấy chi tiết sản phẩm được chọn
        $cartItems = $cart->details()
            ->with(['product', 'variant'])
            ->whereIn('cart_detail_id', $cartDetailIds)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy sản phẩm được chọn.');
        }
        $total = 0;
        foreach ($cartItems as $item) {
            $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price_sale ?? $item->product->price;
            $total += $price * $item->quantity;
        }

        // Danh sách địa chỉ, phương thức thanh toán, shipping để hiển thị ở trang checkout
        $addresses = $user->addresses;
        $shippingFees = ShippingFee::all();

        return view('client.order.create', [
            'cartItems' => $cartItems,
            'selectedItems' => $cartDetailIds,
            'addresses' => $addresses,
            'shippingFees' => $shippingFees,
            'total' => $total,
        ]);

    }

    public function count()
    {
        // Kiểm tra nếu người dùng đã đăng nhập
        $count = Auth::check()
            ? Cart::where('user_id', Auth::id()) // Lọc theo user_id của người dùng đã đăng nhập
                ->with('details') // Eager load CartDetails để truy vấn chi tiết giỏ hàng
                ->get()
                ->pluck('details') // Lấy tất cả cart details
                ->flatten() // Làm phẳng mảng để có tất cả các CartDetail
                ->sum('quantity') // Tính tổng số lượng trong các CartDetail
            : 0; // Nếu không đăng nhập, giỏ hàng sẽ không có sản phẩm

        return response()->json(['count' => $count])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate') // Đảm bảo không cache kết quả API
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
