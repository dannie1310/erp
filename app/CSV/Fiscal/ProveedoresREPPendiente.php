<?php


namespace App\CSV\Fiscal;


use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProveedoresREPPendiente implements WithHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    protected $proveedores;

    public function __construct($proveedores)
    {
        $this->proveedores = $proveedores;
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:J1';

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name' => 'arial',
                        'bold' => true
                    ]]);

                //$event->sheet->getProtection()->setSheet(true);

                $event->sheet->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getColumnDimension('A')->setWidth(3);
                $event->sheet->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getColumnDimension('B')->setWidth(18);
                $event->sheet->getColumnDimension('C')->setAutoSize(false);
                $event->sheet->getColumnDimension('C')->setWidth(18);
                $event->sheet->getColumnDimension('D')->setAutoSize(false);
                $event->sheet->getColumnDimension('D')->setWidth(18);
                $event->sheet->getColumnDimension('E')->setAutoSize(false);
                $event->sheet->getColumnDimension('E')->setWidth(18);
                $event->sheet->getColumnDimension('F')->setAutoSize(false);
                $event->sheet->getColumnDimension('F')->setWidth(18);
                $event->sheet->getColumnDimension('G')->setAutoSize(false);
                $event->sheet->getColumnDimension('G')->setWidth(18);
                $event->sheet->getColumnDimension('H')->setAutoSize(false);
                $event->sheet->getColumnDimension('H')->setWidth(18);
                $event->sheet->getColumnDimension('I')->setAutoSize(false);
                $event->sheet->getColumnDimension('I')->setWidth(18);
                $event->sheet->getColumnDimension('J')->setAutoSize(false);
                $event->sheet->getColumnDimension('J')->setWidth(18);

                $i = 2;
                foreach ($this->proveedores as $key => $proveedor) {
                    $event->sheet->getStyle('E'. $i)->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD);
                    $event->sheet->getStyle('F'. $i)->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD);
                    $event->sheet->getStyle('G'. $i)->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD);

                    $event->sheet->setCellValue("A" . $i, $key + 1);
                    $event->sheet->setCellValue("B" . $i, $proveedor->rfc_proveedor);
                    $event->sheet->setCellValue("C" . $i, $proveedor->proveedor);
                    $event->sheet->setCellValue("D" . $i, $proveedor->cantidad_cfdi);
                    $event->sheet->setCellValue("E" . $i, $proveedor->total_cfdi);
                    $event->sheet->setCellValue("F" . $i, $proveedor->total_rep);
                    $event->sheet->setCellValue("G" . $i, $proveedor->pendiente_rep);
                    $event->sheet->setCellValue("H" . $i, $proveedor->ultima_ubicacion_sao);
                    $event->sheet->setCellValue("I" . $i, $proveedor->ultima_ubicacion_contabilidad);
                    $event->sheet->setCellValue("J" . $i, $proveedor->fecha_ultimo_cfdi_con_ubicacion);

                    $i++;
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }

    public function headings(): array
    {
        return array(['#', 'RFC Proveedor', 'Proveedor', '# CFDI Emitidos', 'Monto CFDI', 'Monto REP', 'Pendiente REP'
            , 'Último Proyecto SAO', 'Último Proyecto Contabilidad', 'Fecha Último CFDI Con Ubicación'
        ]);
    }

}
