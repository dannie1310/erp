<?php


namespace App\CSV\Acarreos\Catalogos;


use App\Models\ACARREOS\Empresa;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class EmpresaLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $empresas;

    public function __construct()
    {
        $this->empresas = Empresa::all();
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:I1';

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name' =>  'arial',
                        'bold' => true
                    ]]);

                $i=2;
                foreach ($this->empresas as $empresa)
                {
                    $event->sheet->setCellValue("A" . $i, ($i - 1));
                    $event->sheet->setCellValue("B" . $i, $empresa->razonSocial);
                    $event->sheet->setCellValue("C" . $i, $empresa->RFC);
                    $event->sheet->setCellValue("D" . $i, $empresa->estado_format);
                    $event->sheet->setCellValue("E" . $i, $empresa->nombre_registro);
                    $event->sheet->setCellValue("F" . $i, $empresa->fecha_registro);
                    $event->sheet->setCellValue("G" . $i, $empresa->nombre_desactivo);
                    $event->sheet->setCellValue("H" . $i, $empresa->fecha_desactivacion_format);
                    $event->sheet->setCellValue("I" . $i, $empresa->motivo);
                    $i++;
                }
            },
        ];
    }

    public function headings(): array
    {
        return array(['#','RAZON SOCIAL', 'RFC', 'ESTADO', 'REGISTRO','FECHA Y HORA REGISTRO','DESACTIVO','FECHA Y HORA DE DESACTIVACION','MOTIVO DE DESACTIVACION']);
    }
}
