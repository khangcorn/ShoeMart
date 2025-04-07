<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ShippingFeeController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgetPassWordController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\VariantAttributeController;
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



Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/products/{id}', [HomeController::class, 'showdetail'])->name('products.detail');
Route::get('/products/{id}/variant-details', [HomeController::class, 'getVariantDetails'])->name('products.variantDetails');
Route::get('/products', [HomeController::class, 'getall'])->name('products.all');



Route::prefix('admin')->group(function() {
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('users', AdminUserController::class);
    Route::resource('sizes', SizeController::class);
    Route::resource('colors', ColorController::class);

    Route::resource('sliders', SliderController::class);
    Route::resource('shipping-fees', ShippingFeeController::class);
    Route::resource('coupons', CouponController::class);

});
// Định nghĩa route DELETE để xóa biến thể
Route::post('/admin/products/{product_id}/variants/{variant_id}/delete', [ProductController::class, 'deleteVariant'])
    ->name('products.variants.delete');















