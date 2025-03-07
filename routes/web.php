<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgetPassWordController;




// Authentication
Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register.form');

Route::get('/forget-password', [ForgetPassWordController::class, 'showLinkRequest'])->name('forget-password.form');
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login.form');


// Xử lý đăng ký, đăng nhập, đăng xuất

Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/register', [UserController::class, 'register'])->name('register');


Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Hiển thị trang thông tin người dùng
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update-address', [UserController::class, 'updateAddress'])->name('update-address');

});
Route::get('/users', [AdminUserController::class, 'index'])->name('user.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



