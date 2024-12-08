<?php

namespace App\Jobs;
use App\Mail\VouchersCreatedMail;
use App\Models\Voucher;
use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class ProcesarVouchers implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected array $xmlContents;
    protected User $user;

    public function __construct(array $xmlContents, User $user)
    {
        $this->xmlContents = $xmlContents;
        $this->user = $user;
    }

    public function handle(VoucherService $voucherService)
    {
        $vouchersExitosos = [];
        $vouchersFallidos = [];

        // Usar el VoucherService para procesar los comprobantes
        $voucherService = new VoucherService();

        foreach ($this->xmlContents as $xmlContent) {
            try {
                $voucher = $voucherService->storeVoucherFromXmlContent($xmlContent, $this->user);
                $vouchersExitosos[] = $voucher;
            } catch (\Exception $e) {
                $vouchersFallidos[] = [
                    'xmlContent' => $xmlContent,
                    'error' => $e->getMessage(),
                ];
            }
        }

        Mail::to($this->user->email)->send(new VouchersCreatedMail($vouchersExitosos, $vouchersFallidos, $this->user));
    }
}