<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonedaMontoController extends Controller
{
    protected VoucherService $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }
    
    /**
     * @return JsonResponse
     */
    public function getTotalAmountsByCurrency(): JsonResponse
    {
        $user = auth()->user(); 
        $totals = $this->voucherService->getTotalAmountsByCurrency($user);

        return response()->json([
            'total_amounts' => $totals,
        ]);
    }
}
