<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataTrainingController;
use App\Http\Controllers\PrediksiController;
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
    return view('layout.main');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::controller(DataTrainingController::class)->prefix('data-training')->group(function () {
    Route::get('/', 'index')->name('data-training');
    Route::post('/import', 'import')->name('data-training-import');
    Route::get('/proses', 'showProses')->name('data-training-proses');
    Route::get('/create', 'create')->name('data-training-create');
    Route::post('/store', 'store')->name('data-training-store');
    Route::get('/edit/{training}', 'edit')->name('data-training-edit');
    Route::put('/update/{training}', 'update')->name('data-training-update');
    Route::delete('/destroy/{training}', 'destroy')->name('data-training-destroy');
});

Route::controller(PrediksiController::class)->prefix('prediksi')->group(function () {
    Route::get('/', 'index')->name('prediksi');
    Route::post('/import', 'import')->name('prediksi-import');
    Route::get('/proses', 'proses')->name('prediksi-proses');
    Route::get('/create', 'create')->name('prediksi-create');
    Route::post('/store', 'store')->name('prediksi-store');
    Route::get('/edit/{prediksi}', 'edit')->name('prediksi-edit');
    Route::put('/update/{prediksi}', 'update')->name('prediksi-update');
    Route::delete('/destroy/{prediksi}', 'destroy')->name('prediksi-destroy');
});
