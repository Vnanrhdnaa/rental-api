<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RentalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//ambil semua data
Route::get('/rentals', [RentalController::class, 'index'])->name('index');
// tambah data baru
Route::post('/rentals/tambah-data', [RentalController::class, 'store'])->name('store');
//generate token csrf
Route::get('/generate-token', [RentalController::class, 'createToken'])->name('createToken');
Route::get('/rentals/show/trash',[RentalController::class,'trash'])->name('trash');
//ambil satu data spesifik
Route::get('/rentals/{id}', [RentalController::class, 'show'])->name('show');
// mengubah data tertentu
Route::patch('/rentals/update/{id}', [RentalController::class, 'update'])->name('update');
// menghapus data tertentu
Route::delete('/rentals/delete/{id}', [RentalController::class, 'destroy'])->name('destroy');
//mengembalikan data yang sudah dihapus
Route::get('/rentals/trash/restore/{id}', [RentalController::class, 'restore'])->name('restore');
//menghapus permanen data spesifik
Route::get('/rentals/trash/delete/permanent/{id}',[RentalController::class, 'permanentDelete'])->name('permanentDelete');
