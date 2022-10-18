<?php


namespace App\CSV\Finanzas;


use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class CFDILayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $cfdi;

    public function __construct($cfdi)
    {
        $this->cfdi = $cfdi;
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:Z1';

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name' =>  'arial',
                        'bold' => true
                    ]]);
                $event->sheet->getProtection()->setSheet(true);

                $event->sheet->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getColumnDimension('A')->setWidth(8);
                $event->sheet->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getColumnDimension('B')->setWidth(18);
                $event->sheet->getColumnDimension('C')->setAutoSize(false);
                $event->sheet->getColumnDimension('C')->setWidth(18);
                $event->sheet->getColumnDimension('D')->setAutoSize(false);
                $event->sheet->getColumnDimension('D')->setWidth(12);
                $event->sheet->getColumnDimension('F')->setAutoSize(false);
                $event->sheet->getColumnDimension('F')->setWidth(45);
                $event->sheet->getColumnDimension('G')->setAutoSize(false);
                $event->sheet->getColumnDimension('G')->setWidth(16);
                $event->sheet->getColumnDimension('H')->setAutoSize(false);
                $event->sheet->getColumnDimension('H')->setWidth(70);
                $event->sheet->getColumnDimension('I')->setAutoSize(false);
                $event->sheet->getColumnDimension('I')->setWidth(16);
                $event->sheet->getColumnDimension('J')->setAutoSize(false);
                $event->sheet->getColumnDimension('J')->setWidth(70);
                $event->sheet->getColumnDimension('K')->setAutoSize(false);
                $event->sheet->getColumnDimension('K')->setWidth(25);
                $event->sheet->getColumnDimension('L')->setAutoSize(false);
                $event->sheet->getColumnDimension('L')->setWidth(10);
                $event->sheet->getColumnDimension('M')->setAutoSize(false);
                $event->sheet->getColumnDimension('M')->setWidth(20);
                $event->sheet->getColumnDimension('N')->setAutoSize(false);
                $event->sheet->getColumnDimension('N')->setWidth(22);
                $event->sheet->getColumnDimension('O')->setAutoSize(false);
                $event->sheet->getColumnDimension('O')->setWidth(16);
                $event->sheet->getColumnDimension('P')->setAutoSize(false);
                $event->sheet->getColumnDimension('P')->setWidth(10);
                $event->sheet->getColumnDimension('Q')->setAutoSize(false);
                $event->sheet->getColumnDimension('Q')->setWidth(10);
                $event->sheet->getColumnDimension('R')->setAutoSize(false);
                $event->sheet->getColumnDimension('R')->setWidth(22);
                $event->sheet->getColumnDimension('S')->setAutoSize(false);
                $event->sheet->getColumnDimension('S')->setWidth(16);
                $event->sheet->getColumnDimension('T')->setAutoSize(false);
                $event->sheet->getColumnDimension('T')->setWidth(25);
                $event->sheet->getColumnDimension('U')->setAutoSize(false);
                $event->sheet->getColumnDimension('U')->setWidth(20);
                $event->sheet->getColumnDimension('V')->setAutoSize(false);
                $event->sheet->getColumnDimension('V')->setWidth(20);
                $event->sheet->getColumnDimension('W')->setAutoSize(false);
                $event->sheet->getColumnDimension('W')->setWidth(18);
                $event->sheet->getColumnDimension('X')->setAutoSize(false);
                $event->sheet->getColumnDimension('X')->setWidth(15);
                $event->sheet->getColumnDimension('Y')->setAutoSize(false);
                $event->sheet->getColumnDimension('Y')->setWidth(15);
                $event->sheet->getColumnDimension('Z')->setAutoSize(false);
                $event->sheet->getColumnDimension('Z')->setWidth(15);

                $i=2;
                foreach ($this->cfdi as $key => $cfd)
                {
                    $event->sheet->setCellValue("A" . $i, $key + 1);
                    $event->sheet->setCellValue("B" . $i, $cfd->fecha_format);
                    $event->sheet->setCellValue("C" . $i, $cfd->serie);
                    $event->sheet->setCellValue("D" . $i, $cfd->folio);
                    $event->sheet->setCellValue("E" . $i, $cfd->tipo_comprobante);
                    $event->sheet->setCellValue("F" . $i, $cfd->uuid);
                    $event->sheet->setCellValue("G" . $i, $cfd->rfc_receptor);
                    $event->sheet->setCellValue("H" . $i, $cfd->empresa->razon_social);
                    $event->sheet->setCellValue("I" . $i, $cfd->rfc_emisor);
                    $event->sheet->setCellValue("J" . $i, $cfd->proveedor->razon_social);
                    $event->sheet->setCellValue("K" . $i, $cfd->subtotal_format);
                    $event->sheet->setCellValue("L" . $i, $cfd->descuento_format);
                    $event->sheet->setCellValue("M" . $i, $cfd->impuestos_retenidos_format);
                    $event->sheet->setCellValue("N" . $i, $cfd->impuestos_trasladados_format);
                    $event->sheet->setCellValue("O" . $i, $cfd->total_format);
                    $event->sheet->setCellValue("P" . $i, $cfd->moneda);
                    $event->sheet->setCellValue("Q" . $i, $cfd->tipo_cambio);
                    $event->sheet->setCellValue("R" . $i, $cfd->conceptos_txt);
                    $event->sheet->setCellValue("S" . $i, $cfd->facturaRepositorio && $cfd->facturaRepositorio->proyecto ? $cfd->facturaRepositorio->proyecto->base_datos:'');
                    $event->sheet->setCellValue("T" . $i, $cfd->facturaRepositorio ? $cfd->facturaRepositorio->obra:'');
                    $event->sheet->setCellValue("U" . $i, $cfd->facturaRepositorio ? $cfd->facturaRepositorio->fecha_hora_registro_format:'');
                    $event->sheet->setCellValue("V" . $i, $cfd->polizaCFDI ? $cfd->polizaCFDI->base_datos_contpaq : '');
                    $event->sheet->setCellValue("W" . $i, $cfd->polizaCFDI ? $cfd->polizaCFDI->ejercicio : '');
                    $event->sheet->setCellValue("X" . $i, $cfd->polizaCFDI ? $cfd->polizaCFDI->periodo : '');
                    $event->sheet->setCellValue("Y" . $i, $cfd->polizaCFDI ? $cfd->polizaCFDI->tipo : '');
                    $event->sheet->setCellValue("Z" . $i, $cfd->polizaCFDI ? $cfd->polizaCFDI->folio : '');
                    $event->sheet->setCellValue("AA" . $i, $cfd->polizaCFDI ? $cfd->polizaCFDI->fecha_format : '');

                    $i++;
                }
            },
        ];
    }

    public function headings(): array
    {
        return array(['#','FECHA', 'SERIE', 'FOLIO','TIPO', 'UUID','RFC RECEPTOR','RECEPTOR','RFC EMISOR','EMISOR','SUBTOTAL','DESCUENTO','IMPUESTOS RETENIDOS','IMPUESTOS TRASLADADOS','TOTAL','MONEDA','TC', 'CONCEPTOS','BD SAO','OBRA SAO','FECHA CARGA PROYECTO', 'BD CTPQ', 'EJERCICIO', 'PERIODO', 'TIPO POLIZA', 'FOLIO POLIZA', 'FECHA POLIZA']);
    }
}
