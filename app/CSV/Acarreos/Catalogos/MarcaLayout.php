<?php


namespace App\CSV\Acarreos\Catalogos;

use App\Models\ACARREOS\Marca;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class MarcaLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $marcas;

    public function __construct()
    {
        $this->marcas = Marca::all();
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:H1';

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name' =>  'arial',
                        'bold' => true
                    ]]);

                $i=2;
                foreach ($this->marcas as $marca)
                {
                    $event->sheet->setCellValue("A" . $i, ($i - 1));
                    $event->sheet->setCellValue("B" . $i, $marca->Descripcion);
                    $event->sheet->setCellValue("C" . $i, $marca->estado_format);
                    $event->sheet->setCellValue("D" . $i, $marca->nombre_registro);
                    $event->sheet->setCellValue("E" . $i, $marca->fecha_registro_completa_format);
                    $event->sheet->setCellValue("F" . $i, $marca->nombre_desactivo);
                    $event->sheet->setCellValue("G" . $i, $marca->fecha_desactivo_format);
                    $event->sheet->setCellValue("H" . $i, $marca->motivo);
                    $i++;
                }
            },
        ];
    }

    public function headings(): array
    {
        return array(['#','DESCRIPCION', 'ESTADO', 'REGISTRO','FECHA Y HORA REGISTRO','DESACTIVO','FECHA Y HORA DE DESACTIVACION','MOTIVO DE DESACTIVACION']);
    }
}
