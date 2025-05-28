<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            $currentUrl = url()->current(); // hoặc url()->full()

            session()->put('wishlist.intended', $currentUrl);
            session()->put('wishlist.intended_time', now()->timestamp);

            Log::info('Đã lưu session wishlist.intended:', [
                'url' => $currentUrl,
                'time' => session('wishlist.intended_time')
            ]);

            return redirect()->route('login');
        }

        $wishlist = Wishlist::where('user_id', auth()->user()->user_id)
            ->with(['product', 'product.mainImage'])
            ->whereHas('product', function ($query) {
                $query->where('is_hidden', 0);
            })
            ->orderBy('created_at', 'desc')
            ->get();


        return view('client.wishlist.index', ['wishlist' => $wishlist]);
    }



    public function delete(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->back()->with([
                'message' => 'Bạn cần đăng nhập để thực hiện thao tác này',
                'type' => 'error',
            ]);
        }
        $id = $request->product_id;
        if (empty($id)) {
            return redirect()->back()->with([
                'message' => 'ID sản phẩm không hợp lệ',
                'type' => 'error',
            ]);
        }

        $wishlistItem = auth()->user()->wishlist()->where('product_id', $id)->first();

        if (!$wishlistItem) {
            return redirect()->back()->with([
                'message' => 'Không tìm thấy sản phẩm trong danh sách yêu thích',
                'type' => 'error',
            ]);
        }

        try {
            $wishlistItem->delete();

            return redirect()->back()->with([
                'message' => 'Sản phẩm đã được xoá khỏi yêu thích',
                'type' => 'success',
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'message' => 'Đã xảy ra lỗi khi xoá sản phẩm',
                'type' => 'error',
            ]);
        }
    }
    public function toggle(Request $request)
    {
        if (!auth()->check()) {
            $currentUrl = url()->previous();
            Log::info('url()->previous() trong toggle:', ['url' => $currentUrl]);

            session()->put('favorite.intended', $currentUrl);
            session()->put('favorite.intended_time', now()->timestamp); // ➕ Lưu thời gian tạo

            Log::info('Session favorite.intended hiện tại:', ['url' => session('favorite.intended')]);
            Log::info('Thời gian lưu:', ['time' => session('favorite.intended_time')]);
            return redirect()->route('login');
        }


        $userId = auth()->id();
        $productId = $request->product_id;

        // Kiểm tra sản phẩm đã có trong wishlist chưa
        $wishlistItem = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlistItem) {
            // Nếu có rồi thì xóa
            $wishlistItem->delete();

            return redirect()->back()->with([
                'message' => 'Sản phẩm đã được xoá khỏi yêu thích',
                'type' => 'success',
            ]);
        } else {
            // Nếu chưa có thì thêm mới
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            return redirect()->back()->with([
                'message' => 'Sản phẩm đã được thêm vào yêu thích',
                'type' => 'success',
            ]);
        }
    }
}
