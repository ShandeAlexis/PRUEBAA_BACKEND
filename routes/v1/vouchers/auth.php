<?php

use App\Http\Controllers\User\MonedaMontoController;
use App\Http\Controllers\Vouchers\DeleteVoucherHandler;
use App\Http\Controllers\Vouchers\GetVouchersHandler;
use App\Http\Controllers\Vouchers\StoreVouchersHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('vouchers')->group(
    function () {
        Route::get('/', GetVouchersHandler::class);
        Route::post('/', StoreVouchersHandler::class);
        Route::delete('/{id}',[DeleteVoucherHandler::class, 'deleteVoucher']);
    }
);
