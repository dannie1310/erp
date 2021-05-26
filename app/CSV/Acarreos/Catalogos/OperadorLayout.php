<?php


namespace App\CSV\Acarreos\Catalogos;


use App\Models\ACARREOS\Operador;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OperadorLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $operadores;

    public function __construct()
    {
       $this->operadores = Operador::all();
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
                foreach ($this->operadores as $operador)
                {
                    $event->sheet->setCellValue("A" . $i, ($i - 1));
                    $event->sheet->setCellValue("B" . $i, $operador->Nombre);
                    $event->sheet->setCellValue("C" . $i, $operador->Direccion);
                    $event->sheet->setCellValue("D" . $i, $operador->NoLicencia);
                    $event->sheet->setCellValue("E" . $i, $operador->licencia_vigencia_format);
                    $event->sheet->setCellValue("F" . $i, $operador->fecha_alta_format);
                    $event->sheet->setCellValue("G" . $i, $operador->fecha_baja_format);
                    $event->sheet->setCellValue("H" . $i, $operador->Estatus);
                    $event->sheet->setCellValue("I" . $i, $operador->nombre_usuario);
                    $event->sheet->setCellValue("J" . $i, $operador->fecha_registro_completa_format);
                    $event->sheet->setCellValue("K" . $i, $operador->nombre_usuario_desactivo);
                    $event->sheet->setCellValue("L" . $i, $operador->fecha_desactivo_format);
                    $event->sheet->setCellValue("M" . $i, $operador->motivo);
                    $i++;
                }
            },
        ];
    }

    public function headings(): array
    {
        return array(['ID','NOMBRE', 'DIRECCION', 'NUMERO DE LICENCIA', 'VIGENCIA DE LICENCIA', 'FECHA REGISTRO','FECHA BAJA','ESTATUS','REGISTRO','FECHA Y HORA REGISTRO','DESACTIVO','FECHA Y HORA DE DESACTIVACION','MOTIVO DE DESACTIVACION']);
    }
}
