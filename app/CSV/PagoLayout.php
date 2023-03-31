<?php

namespace App\CSV;

use App\Models\CADECO\Solicitud;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\CADECO\Factura;

class PagoLayout implements FromCollection, WithHeadings
{
    use Exportable;

    public function __construct()
    {
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function collection()
    {
        $transacciones_pagables = array();
        $facturas = Factura::pendientePago()->orderBy('id_empresa', 'monto')->get();
        $solicitudes = Solicitud::pendientePago()->orderBy('numero_folio')->get();

        foreach ($facturas as $factura) {
            $fecha = new DateTime($factura->fecha);
            $transacciones_pagables[] = array(
                'id_transaccion' => $factura->id_transaccion,
                'fecha' => date_format($fecha, 'd/m/Y'),
                'referencia' => str_replace(',', ' ', $factura->referencia),
                'razon_social' => str_replace(',', ' ', $factura->empresa->razon_social),
                'monto' => $factura->monto,
                'saldo' => $factura->saldo,
                'moneda' => $factura->moneda->nombre,
                '   ',
                '   ',
                '   ',
                '   ',
                '   '
            );
        }
        foreach ($solicitudes as $solicitud) {
            $fecha = new DateTime($solicitud->fecha);
            $proveedor = null;
            if ($solicitud->fondo) {
                $proveedor = $solicitud->fondo->descripcion;
            }
            if ($solicitud->empresa) {
                $proveedor = $solicitud->empresa->razon_social;
            }
            $transacciones_pagables[] = array(
                'id_transaccion' => $solicitud->id_transaccion,
                'fecha' => date_format($fecha, 'd/m/Y'),
                'referencia' => str_replace(',', ' ', 'S/P ' . $solicitud->numero_folio_format),
                'razon_social' => str_replace(',', ' ', $proveedor),
                'monto' => $solicitud->monto,
                'saldo' => $solicitud->monto,
                'moneda' => $solicitud->moneda->nombre,
                '   ',
                '   ',
                '   ',
                '   ',
                '   '
            );
        }
        return collect($transacciones_pagables);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return array([
            'Identificador Factura',
            'Fecha Factura',
            'Referencia de Factura',
            'Proveedor',
            'Monto de Factura',
            'Saldo de Factura',
            'Moneda de Factura',
            'Cuenta Cargo',
            'Fecha de Pago',
            'Referencia de Pago',
            'Tipo de Cambio (de acuerdo a moneda de factura y moneda de cuenta pagadora)',
            'Monto Pagado (en moneda de cuenta pagadora)']);
    }
}
