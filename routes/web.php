<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AdminAccessController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\RefundRequestController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WithdrawRequestController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ForgetPassWordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderCouponController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShippingFeeController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VnPayController;
use App\Http\Controllers\WalletController;
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
Route::get('/password/change', [ForgetPasswordController::class, 'showChangePasswordForm'])->name('password.change.form');
Route::post('/password/change', [ForgetPasswordController::class, 'change'])->name('password.change');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update-address', [UserController::class, 'updateAddress'])->name('update-address');
    Route::post('/profile/update-avatar', [UserController::class, 'updateAvatar'])->name('update-avatar');

});
Route::middleware('auth')->group(function () {
    // Giỏ hàng


    Route::put('/cart/{cartDetailId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartDetailId}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::post('/cart/checkout-selected', [CartController::class, 'checkoutSelected'])->name('cart.checkoutSelected');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

    Route::get('/', [WalletController::class, 'index']);
    Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
    Route::post('/wallet/link-bank', [WalletController::class, 'linkBank'])->name('wallet.link-bank');
    Route::delete('/wallet/unlink-bank/{bank}', [WalletController::class, 'unlinkBank'])->name('wallet.unlink-bank');
    Route::post('wallet/withdraw', [WithdrawRequestController::class, 'store'])->name('wallet.withdraw');

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
    // Mô phỏng chuyển hướng đến cổng thanh toán


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

    Route::get('/admin/request-access', [App\Http\Controllers\Admin\UserController::class, 'requestAccess'])->name('admin.request-access');
    Route::post('/admin/send-request', [App\Http\Controllers\Admin\UserController::class, 'sendRequest'])->name('admin.send-request');

});

    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/products/{id}/variant-details', [HomeController::class, 'getVariantDetails'])->name('products.variantDetails');
    Route::get('/products', [HomeController::class, 'getall'])->name('products.all');
    Route::get('/products/{id}', [HomeController::class, 'showdetail'])->name('products.detail');
    Route::get('/vouchers', [App\Http\Controllers\HomeController::class, 'indexVoucher'])->name('vouchers.index');
    Route::post('/check-coupon', [CouponController::class, 'check'])->name('coupon.check');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    
Route::prefix('admin')->middleware(['auth', 'admin.access'])->group(function () {
    Route::get('/admin-users', [App\Http\Controllers\Admin\AdminAccessController::class, 'index'])
        ->name('admin.users.index');
    Route::get('/admin-users/create', [App\Http\Controllers\Admin\AdminAccessController::class, 'create'])
        ->name('admin.users.create')
        ->middleware('check_permission:admin_create');
    Route::post('/admin-users/store', [App\Http\Controllers\Admin\AdminAccessController::class, 'store'])
        ->name('admin.users.store')
        ->middleware('check_permission:admin_create');
    Route::post('/admin-users/{user}/block', [App\Http\Controllers\Admin\AdminAccessController::class, 'block'])
        ->name('admin.users.block');

    Route::get('/admin-users/{user}/change-password', [App\Http\Controllers\Admin\AdminAccessController::class, 'changePasswordForm'])
        ->name('admin.users.change-password.form');
// Trả về quyền hiện tại của user dạng JSON
Route::get('/admin-users/{user}/permissions/json', [AdminAccessController::class, 'getPermissionsJson'])
    ->name('admin.users.permissions.json');

// Cập nhật quyền
Route::post('/admin-users/{user}/permissions', [AdminAccessController::class, 'updatePermissions'])
    ->name('admin.users.permissions.update');

    Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/requests', [App\Http\Controllers\Admin\UserController::class, 'viewRequests'])
        ->middleware('check_permission:access_request.view')
        ->name('admin.view-requests');
Route::post('/reviews/{review}/toggle-hidden', [ReviewController::class, 'toggleHidden'])->name('admin.reviews.toggleHidden');

    Route::post('/requests/{id}/approve', [App\Http\Controllers\Admin\UserController::class, 'approveRequest'])

        ->name('admin.approve-request');

    Route::post('/requests/{id}/reject', [App\Http\Controllers\Admin\UserController::class, 'rejectRequest'])

        ->name('admin.reject-request');

    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])
        ->middleware('check_permission:review.view')
        ->name('admin.reviews.index');
    Route::get('admin/reviews/product/{productId}', [ReviewController::class, 'show'])->name('admin.reviews.productReviews');

    Route::delete('/reviews/{id}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])

        ->name('admin.reviews.destroy');

    Route::get('reviews/{id}/reply', [\App\Http\Controllers\Admin\ReviewController::class, 'reply'])
        ->middleware('check_permission:review.respond')
        ->name('admin.reviews.reply');

    Route::post('reviews/{id}/reply', [\App\Http\Controllers\Admin\ReviewController::class, 'storeReply'])
        ->middleware('check_permission:review.respond')
        ->name('admin.reviews.reply.store');

    // Refund Requests
    Route::get('/refund-requests', [RefundRequestController::class, 'index'])
        ->middleware('check_permission:view_refunds')
        ->name('admin.refunds.index');
    Route::post('/refund-requests/{id}/approve', [RefundRequestController::class, 'approve'])->name('admin.refunds.approve');
    Route::post('/refund-requests/{id}/reject', [RefundRequestController::class, 'reject'])->name('admin.refunds.reject');

    // Withdraw Requests
    Route::get('withdraw', [WithdrawRequestController::class, 'index'])->name('admin.withdraw.index');
    Route::patch('withdraw/{withdraw}', [WithdrawRequestController::class, 'update'])->name('admin.withdraw.update');

    // Quản lý sản phẩm, danh mục, size, màu sắc, v.v...
    Route::resource('products', ProductController::class)->middleware('check_permission:view_products');
    Route::resource('categories', CategoryController::class)->middleware('check_permission:view_categories');
    Route::resource('sizes', SizeController::class)->middleware('check_permission:view_sizes');
    Route::resource('colors', ColorController::class)->middleware('check_permission:view_colors');
    Route::resource('order-coupons', OrderCouponController::class)->middleware('check_permission:view_order_statuses');
    Route::resource('order-statuses', OrderStatusController::class)->middleware('check_permission:view_order_statuses');
    Route::resource('sliders', SliderController::class)->middleware('check_permission:view_sliders');
    Route::resource('shipping-fees', ShippingFeeController::class)->middleware('check_permission:view_shipping_fees');
    Route::resource('coupons', CouponController::class)->middleware('check_permission:view_coupons');
    Route::resource('users', AdminUserController::class)->middleware('check_permission:view_users');
Route::post('/users/{user}/block', [AdminUserController::class, 'blockUser']);
Route::post('/users/{user}/unblock', [AdminUserController::class, 'unblockUser']);


    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->middleware('check_permission:view_orders')->name('admin.orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->middleware('check_permission:view_order_details')->name('admin.orders.show');
    Route::put('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('admin.orders.cancel');
    Route::put('/orders/{order}/details/{detail}/cancel', [AdminOrderController::class, 'cancelOrderDetail'])->name('admin.orders.details.cancel');
    Route::put('/orders/{order}/ajax-update-status', [AdminOrderController::class, 'ajaxUpdateStatus'])->middleware('check_permission:update_order_status')->name('admin.orders.ajaxUpdateStatus');

    // Xóa biến thể
    Route::post('/products/{product_id}/variants/{variant_id}/delete', [ProductController::class, 'deleteVariant'])->name('products.variants.delete');
});

// routes/web.php hoặc routes/api.php
Route::post('/coupons/validate', [CouponController::class, 'validateCoupons'])->name('coupon.check');
// Trong routes/web.php
Route::patch('/order/{orderId}/refund', [OrderController::class, 'requestRefund'])->name('order.requestRefund');

Route::post('/update-shipping-fee', [ShippingFeeController::class, 'updateShippingFee']);

// routes/web.php
Route::post('/orders/review', [OrderController::class, 'submitReview'])->name('orders.review.submit');
// web.php
Route::get('/payment/vnpay/redirect', [VnPayController::class, 'createPayment'])->name('payment.vnpay.redirect');
Route::get('/payment/vnpay/return', [VnPayController::class, 'vnpayReturn'])->name('payment.vnpay.return');


Route::get('/check-ip', [VnPayController::class, 'checkIp']);

