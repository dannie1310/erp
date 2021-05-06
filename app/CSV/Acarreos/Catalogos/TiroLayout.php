<?php


namespace App\CSV\Acarreos\Catalogos;


use App\Models\ACARREOS\Tiro;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TiroLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $origenes;

    public function __construct()
    {
       $this->tiros = Tiro::all();
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
                foreach ($this->tiros as $tiro)
                {
                    $event->sheet->setCellValue("A" . $i, $tiro->clave_format);
                    $event->sheet->setCellValue("B" . $i, $tiro->Descripcion);
                    $event->sheet->setCellValue("C" . $i, $tiro->estado_format);
                    $event->sheet->setCellValue("D" . $i, $tiro->nombre_usuario);
                    $event->sheet->setCellValue("E" . $i, $tiro->fecha_registro_completa_format);
                    $event->sheet->setCellValue("F" . $i, $tiro->nombre_usuario_desactivo);
                    $event->sheet->setCellValue("G" . $i, $tiro->fecha_desactivo_format);
                    $event->sheet->setCellValue("H" . $i, $tiro->motivo);
                    $i++;
                }
            },
        ];
    }

    public function headings(): array
    {
        return array(['CLAVE','DESCRIPCION', 'ESTATUS', 'REGISTRO','FECHA Y HORA REGISTRO','DESACTIVO','FECHA Y HORA DE DESACTIVACION','MOTIVO DE DESACTIVACION']);
    }
}
