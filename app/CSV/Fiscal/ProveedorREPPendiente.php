<?php
namespace App\CSV\Fiscal;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ProveedorREPPendiente implements  FromQuery, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function query()
    {
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;

        $query = CFDSAT::join('Fiscal.etl_cfdi_sat_rep_pendientes', 'cfd_sat.id', 'etl_cfdi_sat_rep_pendientes.id_cfdi')
            ->join('Contabilidad.ListaEmpresasSAT', 'cfd_sat.id_empresa_sat', 'ListaEmpresasSAT.id')
            ->join('Fiscal.etl_proveedores_rep', 'cfd_sat.id_proveedor_sat', 'etl_proveedores_rep.id');;

        $query->whereRaw("cfd_sat.id_proveedor_sat =".$this->id);

        $query->orderBy("etl_cfdi_sat_rep_pendientes.pendiente_pago", "DESC");

        $query->selectRaw("ROW_NUMBER() OVER(ORDER BY etl_cfdi_sat_rep_pendientes.pendiente_pago DESC) as no_fila, fecha,serie,folio,tipo_comprobante, uuid, rfc_receptor
        , ListaEmpresasSAT.razon_social as empresa, rfc_proveedor, proveedor, subtotal, descuento, total_impuestos_retenidos, total_impuestos_trasladados,
        total, moneda, tipo_cambio, conceptos_txt, cfd_sat.ubicacion_sao,  cfd_sat.ubicacion_contabilidad,
        etl_cfdi_sat_rep_pendientes.total_cfdi,
        etl_cfdi_sat_rep_pendientes.cantidad_pagos, etl_cfdi_sat_rep_pendientes.total_pagado, etl_cfdi_sat_rep_pendientes.total_nc
              , etl_cfdi_sat_rep_pendientes.pendiente_pago");

        return $query;
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $cellRange = 'A1:Y1';

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name' => 'arial',
                        'bold' => true
                    ]]);

                $event->sheet->getStyle('k2:O1000000')->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD);
                $event->sheet->getStyle('u2:u1000000')->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD);
                $event->sheet->getStyle('w2:y1000000')->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD);

            },
        ];
    }

    public function headings(): array
    {
        return array(['#', 'FECHA', 'SERIE', 'FOLIO', 'TIPO', 'UUID', 'RFC RECEPTOR', 'RECEPTOR', 'RFC EMISOR', 'EMISOR', 'SUBTOTAL'
            , 'DESCUENTO', 'IMPUESTOS RETENIDOS', 'IMPUESTOS TRASLADADOS', 'TOTAL', 'MONEDA', 'TC', 'CONCEPTOS','UBICACIÓN SAO'
            ,'UBICACIÓN CONTABILIDAD','TOTAL CFDI', '# PAGOS', 'TOTAL PAGOS', 'TOTAL NC', 'MONTO PENDIENTE REP']);
    }
}
