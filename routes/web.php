<?php

use App\Http\Controllers\SliderController;
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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/sliders', [SliderController::class, 'index'])->name('sliders.index'); // Danh sách sliders
    Route::get('/sliders/create', [SliderController::class, 'create'])->name('sliders.create'); // Form thêm mới
    Route::post('/sliders', [SliderController::class, 'store'])->name('sliders.store'); // Xử lý thêm mới
    Route::get('/sliders/{slider}', [SliderController::class, 'show'])->name('sliders.show'); // Xem chi tiết
    Route::get('/sliders/{slider}/edit', [SliderController::class, 'edit'])->name('sliders.edit'); // Form sửa
    Route::put('/sliders/{slider}', [SliderController::class, 'update'])->name('sliders.update'); // Cập nhật
    Route::delete('/sliders/{slider}', [SliderController::class, 'destroy'])->name('sliders.destroy'); // Xóa slider
});
