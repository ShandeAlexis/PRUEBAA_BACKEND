<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Jobs\ProcesarVouchers;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(int $page, int $paginate): LengthAwarePaginator
    {
        return Voucher::with(['lines', 'user'])->paginate(perPage: $paginate, page: $page);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        dispatch(new ProcesarVouchers($xmlContents, $user));
        foreach ($xmlContents as $xmlContent) {
            $voucher = $this->storeVoucherFromXmlContent($xmlContent, $user);
            $vouchers[] = $voucher;
        }
        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

      
        $serie = (string) $xml->xpath('//cbc:ID')[0]; 
        $numero = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyLegalEntity/cac:RegistrationAddress/cbc:ID')[0]; 
        $tipo_comprobante = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];  
        $moneda = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0]; 


        $voucher = new Voucher([
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            'xml_content' => $xmlContent,
            'user_id' => $user->id,

            'serie' => $serie,
            'numero' => $numero,
            'tipo_comprobante' => $tipo_comprobante,
            'moneda' => $moneda,
        ]);
        $voucher->save();

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }
    /**
     * @param User $user
     * @return array
     */
    public function getTotalAmountsByCurrency(User $user): array
    {
        $totals = Voucher::where('user_id', $user->id)
            ->select(DB::raw('moneda, SUM(total_amount) as total_amount'))
            ->groupBy('moneda')
            ->get();

        $result = [
            'PEN' => 0,
            'USD' => 0,
        ];

        foreach ($totals as $total) {
            if ($total->moneda === 'PEN') {
                $result['PEN'] = (float) $total->total_amount;
            } elseif ($total->moneda === 'USD') {
                $result['USD'] = (float) $total->total_amount;
            }
        }

        return $result;
    }


    /**
     * @param string $id
     * @param User $user
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deleteVoucherById(string $id, User $user): bool
    {
        $voucher = Voucher::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$voucher) {
            throw new ModelNotFoundException('El comprobante no existe o no pertenece al usuario.');
        }

        return $voucher->forceDelete();
    }


    /**
     * @param array $filters
     * @param int $page
     * @param int $paginate
     * @param User $user
     * @return LengthAwarePaginator
     */
    public function getFilteredVouchers(array $filters, int $page, int $paginate, $user): LengthAwarePaginator
    {
        $query = Voucher::query();

        $query->where('user_id', $user->id);

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            throw new \InvalidArgumentException('El rango de fechas es obligatorio.');
        }

        if (!empty($filters['serie'])) {
            $query->where('serie', $filters['serie']);
        }

        if (!empty($filters['numero'])) {
            $query->where('numero', $filters['numero']);
        }

        if (!empty($filters['tipo_comprobante'])) {
            $query->where('tipo_comprobante', $filters['tipo_comprobante']);
        }

        if (!empty($filters['moneda'])) {
            $query->where('moneda', $filters['moneda']);
        }

        return $query->paginate(perPage: $paginate, page: $page);
    }
}
