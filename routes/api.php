<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\IncomingController;
use App\Http\Controllers\SupplierController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('customers', CustomerController::class);
});

Route::middleware(['auth:sanctum', 'role:admin|employee'])->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('incoming', IncomingController::class);
    Route::apiResource('expenses', ExpenseController::class);
});
