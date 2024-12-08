<?php

namespace App\Http\Controllers\Vouchers;


use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

   
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['serie', 'numero', 'tipo_comprobante', 'moneda', 'start_date', 'end_date']);
            $page = $request->input('page', 1);
            $paginate = $request->input('paginate', 10);

            $user = $request->user(); 
            $vouchers = $this->voucherService->getFilteredVouchers($filters, $page, $paginate, $user);

            return response()->json($vouchers, 200);
        } catch (\InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 400);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar filtrar los comprobantes.',
            ], 500);
        }
    }
}
