<?php

namespace App\Exports\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class LayoutPasivosErroresExport implements  WithHeadings, WithEvents
{
    protected $pasivos;

    public function __construct($pasivos)
    {
        $this->pasivos = $pasivos;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                foreach ($this->pasivos as $key => $item) {
                    if($key>0)
                    {
                        $error_str='';
                        $errors = [];
                        $event->sheet->setCellValue("A".($key+1), $item[0]["valor"]);

                        $event->sheet->setCellValue("B".($key+1), $item[1]["valor"]);
                        if(key_exists('error',$item[1])){
                            $errors[] = $item[1]["error"];
                            $event->sheet->getStyle("B".($key+1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
                        }
                        $event->sheet->setCellValue("C".($key+1), $item[2]["valor"]);
                        $event->sheet->setCellValue("D".($key+1), $item[3]["valor"]);
                        $event->sheet->setCellValue("E".($key+1), $item[4]["valor"]);
                        $event->sheet->setCellValue("F".($key+1), $item[5]["valor"]);
                        $event->sheet->setCellValue("G".($key+1), $item[6]["valor"]);
                        $event->sheet->setCellValue("H".($key+1), $item[7]["valor"]);

                        $event->sheet->getStyle("I".($key+1))->getNumberFormat()
                            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

                        $event->sheet->setCellValue("I".($key+1), $item[8]["valor"]);
                        if(key_exists('error',$item[8])){
                            $errors[] = $item[8]["error"];
                            $event->sheet->getStyle("I".($key+1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
                        }
                        $event->sheet->setCellValue("J".($key+1), $item[9]["valor"]);
                        if(key_exists('error',$item[9])){
                            $errors[] = $item[9]["error"];

                            $event->sheet->getStyle("J".($key+1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
                        }
                        $event->sheet->setCellValue("K".($key+1), $item[10]["valor"]);
                        $event->sheet->setCellValue("L".($key+1), $item[11]["valor"]);
                        if(key_exists('error',$item[11])){
                            $errors[] = $item[11]["error"];
                            $event->sheet->getStyle("L".($key+1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
                        }
                        $event->sheet->setCellValue("M".($key+1), $item[12]["valor"]);
                        if(key_exists('error',$item[12])){
                            $errors[] = $item[12]["error"];
                            $event->sheet->getStyle("M".($key+1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
                        }
                        $event->sheet->setCellValue("N".($key+1), $item[13]["valor"]);
                        if(key_exists('error',$item[13])){
                            $errors[] = $item[13]["error"];
                            $event->sheet->getStyle("N".($key+1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
                        }
                        $event->sheet->setCellValue("O".($key+1), $item[14]["valor"]);
                        if(key_exists('error',$item[14])){
                            $errors[] = $item[14]["error"];
                            $event->sheet->getStyle("O".($key+1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
                        }

                        $event->sheet->setCellValue("P".($key+1), $item[15]["valor"]);
                        if(key_exists('error',$item[15])){
                            $errors[] = $item[15]["error"];
                            $event->sheet->getStyle("P".($key+1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
                        }
                        $error_str = implode(" / ",$errors);
                        if($error_str!='')
                        {
                            $event->sheet->setCellValue("Q".($key+1), $error_str);

                        }
                       // $event->sheet->setCellValue("Q".($key+1), $item[2]["valor"]);



                    }


                        //$event->sheet->getStyle('A')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
                    }

            }
        ];
    }

    /*public function collection()
    {
        $lista_pasivos = [];
        $i = 1;
        foreach ($this->pasivos as $pasivo) {
            if ($pasivo->cfdi) {
                $lista_pasivos[] = array(
                    'OBRA' => "A",
                    'BBDD_CONTPAQ' => 1380,
                    'RFC_EMPRESA' => $pasivo->CFDI->folio,
                    'EMPRESA' => $pasivo->CFDI->fecha_format,
                    'RFC_PROVEEDOR' => $pasivo->CFDI->tipo_cfdi,
                    'PROVEEDOR' => $pasivo->CFDI->fecha_format,
                    'CONCEPTO' => $pasivo->CFDI->fecha_format,
                    'FOLIO_FACTURA' => $pasivo->CFDI->fecha_format,
                    'FECHA' => 0,
                    'IMPORTE' => $pasivo->CFDI->moneda,
                    'MONEDA' => $pasivo->CFDI->tipo_cambio_format,
                    'TC_FACTURA' => $pasivo->CFDI->tipo_cambio_format,
                    'IMPORTE_MXN' => 1,
                    'SALDO' => "IVA" . ($pasivo->CFDI->tasa_iva * 100),
                    'TC_SALDO' => $pasivo->CFDI->total,
                    'SALDO_MXN' => $pasivo->CFDI->total_mxn,
                    'ERRORES' => $pasivo->CFDI->total_mxn
                );
                $i++;
            }
        }
        return collect($lista_pasivos);
    }*/


    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'OBRA',
            'BBDD_CONTPAQ',
            'RFC_EMPRESA',
            'EMPRESA',
            'RFC_PROVEEDOR',
            'PROVEEDOR',
            'CONCEPTO',
            'FOLIO_FACTURA',
            'FECHA',
            'IMPORTE',
            "MONEDA",
            "TC_FACTURA",
            'IMPORTE_MXN',
            "SALDO",
            "TC_SALDO",
            "SALDO_MXN"
        ];
    }
}
