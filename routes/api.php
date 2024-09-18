<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DiscountController;
use App\Http\Controllers\API\AuthController;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

Route::post('/getCustomerToken/{id}', [AuthController::class, 'getCustomerToken']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/applyDiscount', [DiscountController::class, 'applyDiscount']);
});

