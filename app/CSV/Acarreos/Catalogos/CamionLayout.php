<?php


namespace App\CSV\Acarreos\Catalogos;


use App\Models\ACARREOS\Camion;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class CamionLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $camiones;

    public function __construct()
    {
        $this->camiones = Camion::all();
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:T1';

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name' =>  'arial',
                        'bold' => true
                    ]]);

                $i=2;
                foreach ($this->camiones as $camion)
                {
                    $event->sheet->setCellValue("A" . $i, $camion->Economico);
                    $event->sheet->setCellValue("B" . $i, $camion->descripcion_sindicato);
                    $event->sheet->setCellValue("C" . $i, $camion->razon_social_empresa);
                    $event->sheet->setCellValue("D" . $i, $camion->Placas);
                    $event->sheet->setCellValue("E" . $i, $camion->PlacasCaja);
                    $event->sheet->setCellValue("F" . $i, $camion->descripcion_marca);
                    $event->sheet->setCellValue("G" . $i, $camion->Modelo);
                    $event->sheet->setCellValue("H" . $i, $camion->Propietario);
                    $event->sheet->setCellValue("I" . $i, $camion->nombre_operador);
                    $event->sheet->setCellValue("J" . $i, $camion->Aseguradora);
                    $event->sheet->setCellValue("K" . $i, $camion->PolizaSeguro);
                    $event->sheet->setCellValue("L" . $i, $camion->VigenciaPolizaSeguro);
                    $event->sheet->setCellValue("M" . $i, $camion->CubicacionReal);
                    $event->sheet->setCellValue("N" . $i, $camion->CubicacionParaPago);
                    $event->sheet->setCellValue("O" . $i, $camion->estado_format);
                    $event->sheet->setCellValue("P" . $i, $camion->nombre_registro);
                    $event->sheet->setCellValue("Q" . $i, $camion->fecha_registro);
                    $event->sheet->setCellValue("R" . $i, $camion->nombre_desactivo);
                    $event->sheet->setCellValue("S" . $i, $camion->fecha_desactivacion_format);
                    $event->sheet->setCellValue("T" . $i, $camion->motivo);
                    $i++;
                }
            },
        ];
    }

    public function headings(): array
    {
        return array(['ECONOMICO','SINDICATO', 'EMPRESA', 'PLACAS DEL CAMIÓN','PLACAS DE LA CAJA', 'MARCA','MODELO','PROPIETARIO','OPERADOR','ASEGURADOR','POLIZA DE SEGURO','VIGENCIA SEGURO','CUBICACION REAL','CUBICACION PARA PAGO','ESTATUS','REGISTRO','FECHA Y HORA DE REGISTRO', 'DESACTIVO','FECHA Y HORA DE DESACTIVACION','MOTIVO DE DESACTIVACION']);
    }

}
