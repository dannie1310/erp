<?php

namespace App\Exports\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LayoutPasivosIFSExport implements FromCollection, WithHeadings
{
    protected $pasivos;

    public function __construct($pasivos)
    {
        $this->pasivos = $pasivos;
    }

    public function collection()
    {
        $lista_pasivos = [];
        $i = 1;
        foreach ($this->pasivos as $pasivo) {
            if ($pasivo->cfdi) {
                $lista_pasivos[] = array(
                    'LINE_TYPE' => "A",
                    'IDENTITY' => 1380,
                    'INVOICE_NO' => $pasivo->CFDI->folio,
                    'TRANSACTION_DATE' => $pasivo->CFDI->fecha_format,
                    'INVOICE_TYPE' => $pasivo->CFDI->tipo_cfdi,
                    'INVOICE_DATE' => $pasivo->CFDI->fecha_format,
                    'ARRIVAL_DATE' => $pasivo->CFDI->fecha_format,
                    'DUE_DATE' => $pasivo->CFDI->fecha_format,
                    'PAY_TERM_ID' => 0,
                    'CURRENCY' => $pasivo->CFDI->moneda,
                    'CURR_RATE' => $pasivo->CFDI->tipo_cambio_format,
                    'DIV_FACTOR' => $pasivo->CFDI->tipo_cambio_format,
                    'ITEM_ID' => 1,
                    'VAT_CODE' => "IVA" . ($pasivo->CFDI->tasa_iva * 100),
                    'NET_CURR_AMOUNT' => $pasivo->CFDI->total,
                    'NET_DOM_AMOUNT' => $pasivo->CFDI->total_mxn,
                    'VAT_CURR_AMOUNT' => $pasivo->CFDI->importe_iva,
                    'VAT_DOM_AMOUNT' => $pasivo->CFDI->importe_iva_mxn,
                    'TAX_ID' => ($pasivo->CFDI->tasa_iva * 100),
                    'TAX_CURR_AMOUNT' => $pasivo->CFDI->importe_iva,
                    'TAX_DOM_AMOUNT' => $pasivo->CFDI->importe_iva_mxn,
                    'FEE_CODE' => "IVA" . ($pasivo->CFDI->tasa_iva * 100),
                    'ROW_ID' => "0",
                    'CODE_A' => "0",
                    'CURR_AMOUNT' => $pasivo->saldo,
                    'DOM_AMOUNT' => $pasivo->saldo_mxn,
                    'TAX_NUMBER' => $pasivo->CFDI->rfc_emisor,
                    'UUID' => $pasivo->CFDI->uuid,
                );
                $i++;
            }
        }
        return collect($lista_pasivos);
    }


    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'LINE_TYPE',
            'IDENTITY',
            'INVOICE_NO',
            'TRANSACTION_DATE',
            'INVOICE_TYPE',
            'INVOICE_DATE',
            'ARRIVAL_DATE',
            'DUE_DATE',
            'PAY_TERM_ID',
            "CURRENCY",
            "CURR_RATE",
            'DIV_FACTOR',
            "ITEM_ID",
            "VAT_CODE",
            "NET_CURR_AMOUNT",
            "NET_DOM_AMOUNT",
            'VAT_CURR_AMOUNT',
            'VAT_DOM_AMOUNT',
            'TAX_ID',
            'TAX_CURR_AMOUNT',
            "TAX_DOM_AMOUNT",
            "FEE_CODE",
            'ROW_ID',
            "CODE_A",
            "CURR_AMOUNT",
            "DOM_AMOUNT",
            "TAX_NUMBER",
            "UUID"
        ];
    }
}
