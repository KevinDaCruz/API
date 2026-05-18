<?php

use App\Http\Controllers\API\BookController;
use Illuminate\Support\Facades\Route;

Route::apiResource('books', BookController::class);

Route::get('/ping', function () {
    return response()->json([
        'message' => 'pong',
    ]);
});
