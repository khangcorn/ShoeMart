<?php
namespace App\Providers;

use App\Models\CartDetail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sử dụng Bootstrap cho phân trang
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();

        // Truyền số lượng sản phẩm trong giỏ hàng đến tất cả view
        View::composer('*', function ($view) {
            $cartCount = Auth::check() 
            ? CartDetail::whereHas('cart', function ($query) {
                $query->where('user_id', Auth::id());
            })->sum('quantity') 
            : 0;
            $view->with('cartCount', $cartCount);
        });
    }
}
