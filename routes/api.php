<?php

use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);
Route::middleware('throttle:10,1')->post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);

Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::patch('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
});

Route::get('/ping', function () {
    return response()->json([
        'message' => 'pong',
    ]);
});
