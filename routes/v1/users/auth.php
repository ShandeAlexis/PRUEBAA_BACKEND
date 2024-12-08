<?php

use App\Http\Controllers\User\MonedaMontoController;

use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(
    function () {
        Route::get('/total-amounts', [MonedaMontoController::class, 'getTotalAmountsByCurrency']);
    }
);
