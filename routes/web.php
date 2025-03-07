<?php


use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
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
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/products/{id}', [HomeController::class, 'show'])->name('products.detail');

Route::prefix('admin')->group(function() {
    Route::resource('products', ProductController::class);
});





Route::resource('categories', CategoryController::class);



