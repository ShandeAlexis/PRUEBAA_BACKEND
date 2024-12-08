<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Controllers\Controller;
use App\Services\VoucherService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteVoucherHandler extends Controller
{

    protected VoucherService $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * @param string $id
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteVoucher(string $id, Request $request): JsonResponse
    {
        try {
            $user = $request->user(); 
            $this->voucherService->deleteVoucherById($id, $user);

            return response()->json([
                'message' => 'El comprobante ha sido eliminado exitosamente.',
            ], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el comprobante.',
            ], 500);
        }
    }
}
