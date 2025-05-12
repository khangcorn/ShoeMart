<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BankAccountController;
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
use App\Http\Controllers\Admin\RefundRequestController;
use App\Http\Controllers\Admin\WithdrawRequestController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\VariantAttributeController;
use App\Http\Controllers\WalletController;
use App\Models\VariantAttribute;
use App\Http\Controllers\WishlistController;

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
    
    Route::get('/', [WalletController::class, 'index']);
    Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
    Route::post('/wallet/link-bank', [WalletController::class, 'linkBank'])->name('wallet.link-bank');
    Route::delete('/wallet/unlink-bank/{bank}', [WalletController::class, 'unlinkBank'])->name('wallet.unlink-bank');
    Route::post('wallet/withdraw', [WithdrawRequestController::class, 'store'])->name('wallet.withdraw');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist', [WishlistController::class, 'delete'])->name('wishlist.delete');


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
    Route::patch('/orders/return-request', [OrderController::class, 'returnRequest'])->name('order.returnRequest');



    Route::get('/address', [AddressController::class, 'index'])->name('address.index');
    Route::get('/address/create', [AddressController::class, 'create'])->name('address.create');
    Route::post('/address', [AddressController::class, 'store'])->name('address.store');
    Route::get('/address/{address_id}/edit', [AddressController::class, 'edit'])->name('address.edit');
    Route::put('/address/{address_id}', [AddressController::class, 'update'])->name('address.update');
    Route::delete('/address/{address_id}', [AddressController::class, 'destroy'])->name('address.delete');
    Route::patch('/address/{address_id}/set-default', [AddressController::class, 'setDefault'])->name('address.setDefault');
    
    Route::post('/orders/{order}/confirm-received', [OrderController::class, 'confirmReceived'])->name('orders.confirmReceived');
    // Hoàn trả đơn hàng
    Route::post('/orders/{order_id}/return', [OrderController::class, 'returnOrder'])->name('orders.return');

    // Tự động cập nhật trạng thái đơn hàng sau 7 ngày
    Route::get('/orders/auto-complete', [OrderController::class, 'autoCompleteOrderStatus'])->name('orders.autoComplete');
});



Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/products/{id}/variant-details', [HomeController::class, 'getVariantDetails'])->name('products.variantDetails');
Route::get('/products', [HomeController::class, 'getall'])->name('products.all');
Route::get('/products/{id}', [HomeController::class, 'showdetail'])->name('products.detail');
Route::get('/vouchers', [App\Http\Controllers\HomeController::class, 'indexVoucher'])->name('vouchers.index');
Route::post('/check-coupon', [CouponController::class, 'check'])->name('coupon.check');
Route::post('/wishlist/store', [WishlistController::class, 'store'])->name('wishlist.store');


// Các route cho admin, thêm middleware 'permission' vào
Route::prefix('admin')->middleware('auth')->group(function() {
    // Refund Requests
    Route::get('/refund-requests', [RefundRequestController::class, 'index'])
        ->middleware('check_permission:view_refunds')
        ->name('admin.refunds.index');

    Route::post('/refund-requests/{id}/approve', [RefundRequestController::class, 'approve'])

        ->name('admin.refunds.approve');

    Route::post('/refund-requests/{id}/reject', [RefundRequestController::class, 'reject'])

        ->name('admin.refunds.reject');

    // Withdraw Requests
    Route::get('withdraw', [WithdrawRequestController::class, 'index'])

        ->name('admin.withdraw.index');

    Route::patch('withdraw/{withdraw}', [WithdrawRequestController::class, 'update'])

        ->name('admin.withdraw.update');
    
    // Quản lý sản phẩm, categories, sizes, colors, v.v...
    Route::resource('products', ProductController::class)
        ->middleware('check_permission:view_products');
       
    Route::resource('categories', CategoryController::class)
        ->middleware('check_permission:view_categories');
       

    Route::resource('sizes', SizeController::class)
        ->middleware('check_permission:view_sizes');
       

    Route::resource('colors', ColorController::class)
        ->middleware('check_permission:view_colors');
        

    Route::resource('order-coupons', OrderCouponController::class)
        ->middleware('check_permission:view_order_statuses');


    Route::resource('order-statuses', OrderStatusController::class)
        ->middleware('check_permission:view_order_statuses');
        

    Route::resource('sliders', SliderController::class)
        ->middleware('check_permission:view_sliders');


    Route::resource('shipping-fees', ShippingFeeController::class)
        ->middleware('check_permission:view_shipping_fees');


    Route::resource('coupons', CouponController::class)
        ->middleware('check_permission:view_coupons');


    Route::resource('users', AdminUserController::class)
        ->middleware('check_permission:view_users');


    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])
        ->middleware('check_permission:view_orders')
        ->name('admin.orders.index');

    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
        ->middleware('check_permission:view_order_details')
        ->name('admin.orders.show');

    Route::put('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])

        ->name('admin.orders.cancel');

    Route::put('/orders/{order}/ajax-update-status', [AdminOrderController::class, 'ajaxUpdateStatus'])
        ->middleware('check_permission:update_order_status')
        ->name('admin.orders.ajaxUpdateStatus');
});

// Định nghĩa route DELETE để xóa biến thể
Route::post('/admin/products/{product_id}/variants/{variant_id}/delete', [ProductController::class, 'deleteVariant'])
    ->name('products.variants.delete');
 
  

// routes/web.php hoặc routes/api.php
Route::post('/coupons/validate', [CouponController::class, 'validateCoupons'])->name('coupon.check');
// Trong routes/web.php
Route::patch('/order/{orderId}/refund', [OrderController::class, 'requestRefund'])->name('order.requestRefund');


Route::post('/update-shipping-fee', [ShippingFeeController::class, 'updateShippingFee']);

// routes/web.php
