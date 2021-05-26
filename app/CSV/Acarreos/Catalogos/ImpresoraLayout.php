<?php


namespace App\CSV\Acarreos\Catalogos;

use App\Models\ACARREOS\Impresora;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ImpresoraLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $impresoras;

    public function __construct()
    {
        $this->impresoras = Impresora::all();
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
                foreach ($this->impresoras as $impresora)
                {
                    $event->sheet->setCellValue("A" . $i, ($i - 1));
                    $event->sheet->setCellValue("B" . $i, $impresora->mac);
                    $event->sheet->setCellValue("C" . $i, $impresora->marca);
                    $event->sheet->setCellValue("D" . $i, $impresora->modelo);
                    $event->sheet->setCellValue("E" . $i, $impresora->estado_format);
                    $event->sheet->setCellValue("F" . $i, $impresora->nombre_usuario);
                    $event->sheet->setCellValue("G" . $i, $impresora->fecha_registro_completa_format);
                    $event->sheet->setCellValue("H" . $i, $impresora->nombre_usuario_desactivo);
                    $event->sheet->setCellValue("I" . $i, $impresora->fecha_desactivo_format);
                    $event->sheet->setCellValue("J" . $i, $impresora->motivo);
                    $i++;
                }
            },
        ];
    }

    public function headings(): array
    {
        return array(['#','MAC ADDRESS', 'MARCA', 'MODELO', 'ESTADO', 'REGISTRO','FECHA Y HORA REGISTRO','DESACTIVO','FECHA Y HORA DE DESACTIVACION','MOTIVO DE DESACTIVACION']);
    }
}
