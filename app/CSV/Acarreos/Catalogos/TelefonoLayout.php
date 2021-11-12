<?php


namespace App\CSV\Acarreos\Catalogos;

use App\Models\ACARREOS\Telefono;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TelefonoLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct()
    {
       $this->telefonos = Telefono::all();
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:L1';

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name' =>  'arial',
                        'bold' => true
                    ]]);

                $i=2;
                foreach ($this->telefonos as $telefono)
                {
                    $event->sheet->setCellValue("A" . $i, $telefono->id);
                    $event->sheet->setCellValue("B" . $i, $telefono->imei);
                    $event->sheet->setCellValue("C" . $i, $telefono->device_id);
                    $event->sheet->setCellValue("D" . $i, $telefono->linea);
                    $event->sheet->setCellValue("E" . $i, $telefono->marca);
                    $event->sheet->setCellValue("F" . $i, $telefono->modelo);
                    $event->sheet->setCellValue("G" . $i, $telefono->estado_format);
                    $event->sheet->setCellValue("H" . $i, $telefono->nombre_registro);
                    $event->sheet->setCellValue("I" . $i, $telefono->fecha_registro_completa_format);
                    $event->sheet->setCellValue("J" . $i, $telefono->nombre_desactivo);
                    $event->sheet->setCellValue("K" . $i, $telefono->fecha_desactivo_format);
                    $event->sheet->setCellValue("L" . $i, $telefono->motivo);
                    $i++;
                }
            },
        ];
    }

    public function headings(): array
    {
        return array(['ID','IMEI TELEFONO','ID. DISPOSITIVO','LINEA TELEFONICA','MARCA','MODELO','ESTATUS','REGISTRO','FECHA Y HORA REGISTRO','DESACTIVO','FECHA Y HORA DE DESACTIVACION','MOTIVO DE DESACTIVACION']);
    }
}
