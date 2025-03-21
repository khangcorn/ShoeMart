<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgetPassWordController;
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

Route::get('/', function () {
    return view('welcome');
});

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
Route::get('/products/{id}', [HomeController::class, 'show'])->name('products.detail');

Route::middleware('auth')->group(function () {

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('users', AdminUserController::class);

});









