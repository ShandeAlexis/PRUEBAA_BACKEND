<?php

namespace App\Events\Vouchers;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Jobs\ProcesarVouchers;

class VouchersCreated
{
    use Dispatchable, SerializesModels;

    public readonly array $vouchers;
    public readonly User $user;

    public function __construct(array $vouchers, User $user)
    {
        $this->vouchers = $vouchers;
        $this->user = $user;
    }

   
}