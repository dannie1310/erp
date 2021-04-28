<?php


namespace App\CSV\Acarreos\Catalogos;


use App\Models\ACARREOS\Origen;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class OrigenLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $origenes;

    public function __construct()
    {
       $this->origenes = Origen::all();
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:J1';

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name' =>  'arial',
                        'bold' => true
                    ]]);

                $i=2;
                foreach ($this->origenes as $origen)
                {
                    $event->sheet->setCellValue("A" . $i, ($i - 1));
                    $event->sheet->setCellValue("B" . $i, $origen->clave_format);
                    $event->sheet->setCellValue("C" . $i, $origen->tipo_origen_descripcion);
                    $event->sheet->setCellValue("D" . $i, $origen->Descripcion);
                    $event->sheet->setCellValue("E" . $i, $origen->estado_format);
                    $event->sheet->setCellValue("F" . $i, $origen->nombre_usuario);
                    $event->sheet->setCellValue("G" . $i, $origen->fecha_registro_completa_format);
                    $event->sheet->setCellValue("H" . $i, $origen->nombre_usuario_desactivo);
                    $event->sheet->setCellValue("I" . $i, $origen->fecha_desactivo_format);
                    $event->sheet->setCellValue("J" . $i, $origen->motivo);
                    $i++;
                }
            },
        ];
    }

    public function headings(): array
    {
        return array(['#','CLAVE', 'TIPO', 'DESCRIPCION', 'ESTADO', 'REGISTRO','FECHA Y HORA REGISTRO','DESACTIVO','FECHA Y HORA DE DESACTIVACION','MOTIVO DE DESACTIVACION']);
    }
}
