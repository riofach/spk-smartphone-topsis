<?php

use App\Http\Controllers\SmartphoneController;
use App\Http\Controllers\ListHpController;
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

// Route untuk melihat daftar smartphone
Route::get('/smartphones-icibos', [SmartphoneController::class, 'index'])->name('smartphones.index');
Route::get('/smartphones-icibos/{smartphone}/edit', [SmartphoneController::class, 'edit'])->name('smartphones.edit');
Route::put('/smartphones-icibos/{smartphone}', [SmartphoneController::class, 'update'])->name('smartphones.update');
Route::delete('/smartphones-icibos/{smartphone}', [SmartphoneController::class, 'destroy'])->name('smartphones.destroy');

// Route untuk rekomendasi SPK TOPSIS
Route::get('/recommendation', [SmartphoneController::class, 'recommendationForm'])->name('recommendation.form');
Route::post('/recommendation', [SmartphoneController::class, 'getRecommendation'])->name('recommendation.result');

// Route tersembunyi untuk tambah smartphone - hanya yang tahu URL yang bisa akses
Route::get('/admin-add-smartphone', [SmartphoneController::class, 'create'])->name('smartphones.create');
Route::post('/admin-add-smartphone', [SmartphoneController::class, 'store'])->name('smartphones.store');

Route::get('/list-hp', [ListHpController::class, 'index'])->name('list-hp.index');