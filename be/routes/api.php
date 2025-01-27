<?php

use App\Http\Controllers\BooksController;
use App\Http\Controllers\SyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/pinjam', [BooksController::class, 'pinjam']);
Route::get('/books/details/{book}', [BooksController::class, 'details'])->name('books.details');
Route::post('/books/return/{book}', [BooksController::class, 'returnBook'])->name('books.return');
Route::post('/books/favorit/{book}', [BooksController::class, 'updateFavorit'])->name('books.updateFavorit');
Route::get('/books/statistics', [BooksController::class, 'getStatistics']);
Route::post('/sync', [SyncController::class, 'syncData']); // Untuk mengirim data dari aplikasi ke server
Route::get('/sync', [SyncController::class, 'getData']); // Untuk mengambil data terbaru dari server




Route::resource('/books', BooksController::class);