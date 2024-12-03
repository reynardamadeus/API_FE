<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;;
use App\Http\Middleware\isLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [UserController::class, 'login']);
Route::get('/product', [ProductController::class, 'getProducts']);
Route::post('/product', [ProductController::class, 'createProduct'])->middleware('auth:sanctum', isLogin::class);
Route::put('/product/{id}', [ProductController::class, 'updateProduct'])->middleware('auth:sanctum', isLogin::class);
Route::delete('/product/{id}', [ProductController::class, 'deleteProduct'])->middleware('auth:sanctum', isLogin::class);
