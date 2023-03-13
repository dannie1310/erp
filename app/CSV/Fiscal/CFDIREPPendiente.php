<?php


namespace App\CSV\Fiscal;


use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CFDIREPPendiente implements  FromQuery, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function query()
    {
        $query = CFDSAT::join('Fiscal.vw_cfd_sat_rep_pendiente', 'cfd_sat.id', 'vw_cfd_sat_rep_pendiente.id_cfdi')
            ->join('Contabilidad.ListaEmpresasSAT', 'cfd_sat.id_empresa_sat', 'ListaEmpresasSAT.id')
            ->join('Fiscal.vw_proveedores_rep', 'cfd_sat.id_proveedor_sat', 'vw_proveedores_rep.id');;

        if (key_exists("startDate", $this->data) && key_exists("endDate", $this->data)) {
            $query->whereBetween('cfd_sat.fecha', [$this->data["startDate"], $this->data["endDate"]]);
        }

        if ($this->data['no_hermes'] === "false" && $this->data['es_hermes'] === "true") {
            $query->where("es_empresa_hermes", "=", "1");
        } else if ($this->data['no_hermes'] === "true" && $this->data['es_hermes'] === "false") {
            $query->where("es_empresa_hermes", "=", "0");
        }

        if ($this->data['con_contactos'] === "false" && $this->data['sin_contactos'] === "true") {
            $query->where("cantidad_contactos", "=", "0");
        } else if ($this->data['con_contactos'] === "true" && $this->data['sin_contactos'] === "false") {
            $query->where("cantidad_contactos", ">=", "0");
        }

        if (isset($this->data['rfc_emisor'])) {
            $query->where('rfc_emisor', 'LIKE', '%' . $this->data['rfc_emisor'] . '%');
        }

        if (isset($this->data['emisor'])) {
            $query->where(['proveedor', 'LIKE', '%' . $this->data['emisor'] . '%']);
        }

        if (isset($this->data['rfc_receptor'])) {
            $query->where('rfc_receptor', 'LIKE', '%' . $this->data['rfc_receptor'] . '%');
        }

        if (isset($this->data['receptor'])) {
            $query->where('razon_social', 'LIKE', '%' . $this->data['receptor'] . '%');
        }

        if (isset($this->data['uuid'])) {
            $query->where('cfd_sat.uuid', 'LIKE', '%' . $this->data['uuid'] . '%');
        }

        if (isset($this->data['moneda'])) {
            $query->where('moneda', 'LIKE', '%' . $this->data['moneda'] . '%');
        }

        if (isset($this->data['fecha'])) {
            $query->whereBetween( 'cfd_sat.fecha', [ $this->data['fecha'] ." 00:00:00", $this->data['fecha']." 23:59:59"] );
        }

        if (isset($this->data['tipo_comprobante'])) {
            $query->where('cfd_sat.tipo_comprobante', 'LIKE', '%' .$this->data['tipo_comprobante']. '%' );
        }

        if (isset($this->data['serie'])) {
            $query->where('cfd_sat.serie', '=', $this->data['serie'] );
        }

        if (isset($this->data['folio'])) {
            $query->where('cfd_sat.folio', '=', $this->data['folio'] );
        }

        if (isset($this->data['total'])) {

            if (strpos($this->data['total'], ">=") !== false) {
                $total = str_replace(">=", "", $this->data['total']);
                $query->where('total', ">=", $total);
            } else if (strpos($this->data['total'], ">") !== false) {
                $total = str_replace(">", "", $this->data['total']);
                $query->where('total', ">", $total);
            } else if (strpos($this->data['total'], "<=") !== false) {
                $total = str_replace("<=", "", $this->data['total']);
                $query->where('total', "<=", $total);
            } else if (strpos($this->data['total'], "<") !== false) {
                $total = str_replace("<", "", $this->data['total']);
                $query->where('total', "<", $total);
            } else if (strpos($this->data['total'], "=") !== false) {
                $total = str_replace("=", "", $this->data['total']);
                $query->where('total', "=", $total);
            } else {
                $query->where('total', "=", $this->data['total']);
            }
        }

        if (isset($this->data['tipo_cambio'])) {
            $query->where('tipo_cambio', '=', $this->data['tipo_cambio'] );
        }
        if (isset($this->data['subtotal'])) {
            $query->where('subtotal', '=', $this->data['subtotal'] );
        }
        if (isset($this->data['descuento'])) {
            $query->where('descuento', '=', $this->data['descuento'] );
        }
        if (isset($this->data['impuestos_retenidos'])) {
            $query->where('total_impuestos_retenidos', '=', $this->data['impuestos_retenidos'] );
        }
        if (isset($this->data['impuestos_trasladados'])) {
            $query->where('total_impuestos_trasladados', '=', $this->data['impuestos_trasladados'] );
        }

        if (isset($this->data['obra'])) {
            $query->where('ubicacion_sao', 'like', "%".$this->data['obra']."%" );
        }

        if (isset($this->data['base_datos_ctpq'])) {
            $query->where('ubicacion_contabilidad', 'like', "%".$this->data['base_datos_ctpq']."%" );
        }


        $query->orderBy("vw_cfd_sat_rep_pendiente.pendiente_pago", "DESC");

        $query->selectRaw("ROW_NUMBER() OVER(ORDER BY vw_cfd_sat_rep_pendiente.pendiente_pago DESC) as no_fila, fecha,serie,folio,tipo_comprobante, uuid, rfc_receptor
        , ListaEmpresasSAT.razon_social as empresa, rfc_proveedor, proveedor, subtotal, descuento, total_impuestos_retenidos, total_impuestos_trasladados,
        total, moneda, tipo_cambio, conceptos_txt, cfd_sat.ubicacion_sao,  cfd_sat.ubicacion_contabilidad,
        vw_cfd_sat_rep_pendiente.total_cfdi,
        vw_cfd_sat_rep_pendiente.cantidad_pagos, vw_cfd_sat_rep_pendiente.total_pagado, vw_cfd_sat_rep_pendiente.total_nc
              , vw_cfd_sat_rep_pendiente.pendiente_pago");

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
