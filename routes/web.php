<?php

use App\Http\Controllers\UploadImagController;
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
    return view('Images.create');
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
/*Images routes*/
Route::get('/upload', [UploadImagController::class, 'index']);
Route::post('/upload', [UploadImagController::class, 'store'])->name('upload.store');

Route::get('/upload/progress',  [UploadImagController::class, 'progress'])->name('upload.progress');
