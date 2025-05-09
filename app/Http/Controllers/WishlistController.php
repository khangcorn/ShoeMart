<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;


class WishlistController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->back()->with(noti('Vui lòng đăng nhập để sử dụng chức năng này', 'warning'));
        }
        $wishlist = Wishlist::where('user_id', auth()->user()->user_id)
            ->with(['product','product.mainImage'])
            ->orderBy('created_at', 'desc')->get();
            // dd($wishlist);
        return view('client.wishlist.index', ['wishlist' => $wishlist]);
    }
    public function store(Request $request)
    {
        if (auth()->check()) {
            $userId = auth()->user()->user_id;
            $wishlist = Wishlist::where('user_id', $userId)->where('product_id', $request->product_id)->count();

            if ($wishlist < 1) {
                $wishlist = new Wishlist;
                $wishlist->user_id = $userId;
                $wishlist->product_id = $request->product_id;
                $wishlist->save();
                return [
                    'success' => 'success',
                    'message' => 'Sản phẩm đã được thêm vào yêu thích'
                ];
            } else {
                return [
                    'success' => 'warning',
                    'message' => 'Sản phẩm đã có trong danh sách yêu thích'
                ];
            }
        } else {
            return [
                'success' => 'warning',
                'message' => 'Đăng nhập để sử dụng chức năng này'
            ];
        }
    }

    public function delete(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->back()->with(noti('Bạn cần đăng nhập để thực hiện thao tác này', 'error'));
        }
        $id = $request->id;
        if (empty($id)) {
            return redirect()->back()->with(noti('ID sản phẩm không hợp lệ', 'error'));
        }

        $wishlistItem = auth()->user()->wishlist()->where('product_id', $id)->first();

        if (!$wishlistItem) {
            return redirect()->back()->with(noti('Không tìm thấy sản phẩm trong danh sách yêu thích', 'error'));
        }

        try {
            $wishlistItem->delete();
            return redirect()->back()->with(noti('Sản phẩm đã được xoá khỏi yêu thích', 'success'));
        } catch (\Throwable $th) {
            return redirect()->back()->with(noti('Đã xảy ra lỗi khi xoá sản phẩm', 'error'));
        }
    }

}
