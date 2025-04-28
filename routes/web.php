<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderCouponController;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ShippingFeeController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgetPassWordController;
use App\Http\Controllers\GHNController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\VariantAttributeController;
use App\Http\Controllers\WishlistController;
use App\Models\VariantAttribute;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Authentication




// Xử lý đăng ký, đăng nhập, đăng xuất
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login.form');

Route::post('/login', [UserController::class, 'login'])->name('login');


Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [UserController::class, 'register'])->name('register');



Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [ForgetPasswordController::class, 'showLinkRequest'])->name('password.request');
Route::post('/forgot-password', [ForgetPasswordController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', [ForgetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgetPasswordController::class, 'resetPassword'])->name('password.update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update-address', [UserController::class, 'updateAddress'])->name('update-address');
    Route::post('/profile/update-avatar', [UserController::class, 'updateAvatar'])->name('update-avatar');

});
Route::middleware('auth')->group(function () {
    // Giỏ hàng
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::put('/cart/{cartDetailId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartDetailId}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::post('/cart/checkout-selected', [CartController::class, 'checkoutSelected'])->name('cart.checkoutSelected');
    Route::get('/cart/count',[CartController::class, 'count'] )->name('cart.count');
    



     // Đơn hàng
     Route::get('/checkout', [OrderController::class, 'create'])->name('cart.checkout');
    // Có thể sử dụng POST cho việc tạo đơn hàng
    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
    Route::post('/order/{order_id}/process-payment', [OrderController::class, 'processPayment'])->name('order.processPayment');
    Route::get('/order/success', [OrderController::class, 'paymentSuccess'])->name('order.success');    
    Route::get('/order/details', [OrderController::class, 'showOrderDetails'])->name('order.details');
    Route::get('/orders', [OrderController::class, 'index'])->name('order.index'); // Danh sách đơn hàng
    Route::get('/orders/{order_id}', [OrderController::class, 'show'])->name('order.show'); // Chi tiết đơn hàng
    Route::patch('/orders/{order_id}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    


    Route::get('/address', [AddressController::class, 'index'])->name('address.index');
    Route::get('/address/create', [AddressController::class, 'create'])->name('address.create');
    Route::post('/address', [AddressController::class, 'store'])->name('address.store');

    Route::get('/address/{address_id}/edit', [AddressController::class, 'edit'])->name('address.edit');
    Route::put('/address/{address_id}', [AddressController::class, 'update'])->name('address.update');
    Route::delete('/address/{address_id}', [AddressController::class, 'destroy'])->name('address.delete');
    Route::patch('/address/{address_id}/set-default', [AddressController::class, 'setDefault'])->name('address.setDefault');
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('/add', [WishlistController::class, 'store'])->name('store');
        Route::get('/delete', [WishlistController::class, 'delete'])->name('delete');
    });
});



Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/products/{id}/variant-details', [HomeController::class, 'getVariantDetails'])->name('products.variantDetails');
Route::get('/products', [HomeController::class, 'getall'])->name('products.all');
Route::get('/products/{id}', [HomeController::class, 'showdetail'])->name('products.detail');
Route::get('/vouchers', [App\Http\Controllers\HomeController::class, 'indexVoucher'])->name('vouchers.index');


Route::prefix('admin')->group(function() {
    Route::get('refunds', [RefundController::class, 'index'])->name('refunds.index');
    Route::patch('refunds/{refundId}/approve', [RefundController::class, 'approve'])->name('refunds.approve');
    
    // Route để từ chối yêu cầu hoàn tiền
    Route::patch('refunds/{refundId}/reject', [RefundController::class, 'reject'])->name('refunds.reject');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('users', AdminUserController::class);
    Route::resource('sizes', SizeController::class);
    Route::resource('colors', ColorController::class);
    Route::resource('order-coupons', OrderCouponController::class);
    Route::resource('order-statuses', OrderStatusController::class);
    Route::resource('sliders', SliderController::class);
    Route::resource('shipping-fees', ShippingFeeController::class);
    Route::resource('coupons', CouponController::class);
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

});
// Định nghĩa route DELETE để xóa biến thể
Route::post('/admin/products/{product_id}/variants/{variant_id}/delete', [ProductController::class, 'deleteVariant'])
    ->name('products.variants.delete');
 
  

// routes/web.php hoặc routes/api.php
Route::post('/coupons/validate', [CouponController::class, 'validateCoupons'])->name('coupon.check');
// Trong routes/web.php
Route::patch('/order/{orderId}/refund', [OrderController::class, 'requestRefund'])->name('order.requestRefund');



// routes/web.php
