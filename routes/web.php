<?php


use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;

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

Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
Route::resource('product_variants', ProductVariantController::class);
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

// Authentication
Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register.form');
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login.form');

// Xử lý đăng ký, đăng nhập, đăng xuất
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Hiển thị trang thông tin người dùng
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update-address', [UserController::class, 'updateAddress'])->name('update-address');
});