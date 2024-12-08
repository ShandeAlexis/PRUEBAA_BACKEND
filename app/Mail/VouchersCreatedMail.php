<?php

namespace App\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VouchersCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $vouchers;
    public array $vouchersFallidos;
    public User $user;

    public function __construct(array $vouchers, array $vouchersFallidos, User $user)
    {
        $this->vouchers = $vouchers;
        $this->vouchersFallidos = $vouchersFallidos;
        $this->user = $user;
    }

    public function build(): self
    {
        return $this->view('emails.vouchers')
            ->subject('Resumen de Comprobantes Subidos')
            ->with([
                'vouchers' => $this->vouchers,
                'vouchersFallidos' => $this->vouchersFallidos,
                'user' => $this->user,
            ]);
    }
}