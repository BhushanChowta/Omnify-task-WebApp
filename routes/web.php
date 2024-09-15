<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscountController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
Route::get('/discounts/create', [DiscountController::class, 'create'])->name('discounts.create');
Route::post('/discounts/store', [DiscountController::class, 'store'])->name('discounts.store');
Route::get('/discounts/{id}/show', [DiscountController::class, 'show'])->name('discounts.show');
Route::get('/discounts/{id}/edit', [DiscountController::class, 'edit'])->name('discounts.edit');
Route::put('/discounts/{id}/update', [DiscountController::class, 'update'])->name('discounts.update');
Route::delete('/discounts/{id}/delete', [DiscountController::class, 'delete'])->name('discounts.delete');
Route::get('/discounts/apply-discount/', [DiscountController::class, 'applyDiscount']);
