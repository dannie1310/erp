<?php


namespace App\CSV\Acarreos\Catalogos;


use App\Models\ACARREOS\Material;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MaterialLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $materiales;

    public function __construct()
    {
       $this->materiales = Material::all();
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
                foreach ($this->materiales as $material)
                {
                    $event->sheet->setCellValue("A" . $i, ($i - 1));
                    $event->sheet->setCellValue("B" . $i, $material->Descripcion);
                    $event->sheet->setCellValue("C" . $i, $material->Estatus);
                    $event->sheet->setCellValue("D" . $i, $material->nombre_usuario);
                    $event->sheet->setCellValue("E" . $i, $material->fecha_registro_completa_format);
                    $event->sheet->setCellValue("F" . $i, $material->nombre_usuario_desactivo);
                    $event->sheet->setCellValue("G" . $i, $material->fecha_desactivo_format);
                    $event->sheet->setCellValue("H" . $i, $material->motivo);
                    $i++;
                }
            },
        ];
    }

    public function headings(): array
    {
        return array(['ID', 'DESCRIPCION', 'ESTATUS', 'REGISTRO','FECHA Y HORA REGISTRO','DESACTIVO','FECHA Y HORA DE DESACTIVACION','MOTIVO DE DESACTIVACION']);
    }
}
