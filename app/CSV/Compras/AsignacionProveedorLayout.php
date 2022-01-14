<?php

namespace App\CSV\Compras;

use App\Facades\Context;
use function Complex\cot;
use App\Utils\ValidacionSistema;
use Exception;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class AsignacionProveedorLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $cotizaciones;
    protected $id_solicitud_c;
    protected $verifica;

    public function __construct($cotizaciones, $id_solicitud_c)
    {
        $this->verifica = new ValidacionSistema();
        $this->cotizaciones = $cotizaciones;
        $this->id_solicitud_c = $id_solicitud_c;
    }

    /**
     * @return array
     */

    public function registerEvents(): array{
        return [AfterSheet::class    => function(AfterSheet $event) {
            $event->sheet->getProtection()->setSheet(true);
            $col_rs = 7;
            $row = 1;

            // Validación de archivo xls
            $verificacion_estimacion = $this->verifica->encripta(Context::getDatabase()."|".Context::getIdObra()."|".$this->id_solicitud_c);
            $event->sheet->setCellValue("A1", $verificacion_estimacion);

            // Encabezado con Razón Social y id cotizacion
            foreach($this->cotizaciones['cotizaciones'] as $id_cot => $cotizacion){
                $event->sheet->mergeCellsByColumnAndRow($col_rs, $row,$col_rs+5,$row);
                $event->sheet->setCellValueByColumnAndRow($col_rs, $row,$cotizacion['razon_social']);
                $event->sheet->mergeCellsByColumnAndRow($col_rs, $row+1,$col_rs+5,$row+1);
                $event->sheet->setCellValueByColumnAndRow($col_rs, $row+1,$cotizacion['sucursal']);
                $event->sheet->mergeCellsByColumnAndRow($col_rs, $row+2,$col_rs+5,$row+2);
                $event->sheet->setCellValueByColumnAndRow($col_rs, $row+2,$this->verifica->encripta($id_cot));
                $col_rs += 6;
            }

            $letra_fin = $this->getLetter($col_rs-1);
            $range_rs = "G1:".$letra_fin."3";
            
            $event->sheet->getDelegate()->getStyle($range_rs)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ]
                ]
            ]);
            $event->sheet->getStyle($range_rs)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DFDFDF');

            // Encabezado de columnas
            $row = 4;            
            $event->sheet->setCellValue("A".$row, 'IdPartida');
            $event->sheet->setCellValue("B".$row, 'Descripción');
            $event->sheet->setCellValue("C".$row, 'Unidad');
            $event->sheet->setCellValue("D".$row, 'Cantidad Solicitada');
            $event->sheet->setCellValue("E".$row, 'Cantidad Asignada Previamente');
            $event->sheet->setCellValue("F".$row, 'Cantidad Pendiente Asignar');

            $col_titles = 7;
            for($i = 0; $i < count($this->cotizaciones['cotizaciones']); $i++){
                $event->sheet->setCellValueByColumnAndRow($col_titles,   $row,'Precio Unitario');
                $event->sheet->setCellValueByColumnAndRow($col_titles+1, $row,'% Descuento');
                $event->sheet->setCellValueByColumnAndRow($col_titles+2, $row,'Importe');
                $event->sheet->setCellValueByColumnAndRow($col_titles+3, $row,'Moneda');
                $event->sheet->setCellValueByColumnAndRow($col_titles+4, $row,'Importe Pesos (MXN)');
                $event->sheet->setCellValueByColumnAndRow($col_titles+5, $row,'Cantidad Asignada');
                $col_titles += 6;
            }

            $letra_fin = $this->getLetter($col_titles-1);
            $range_titles = "A4:".$letra_fin."4";

            $event->sheet->getDelegate()->getStyle($range_titles)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ]
                ]
            ]);
            $event->sheet->getStyle($range_titles)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('B9B9B9');

            // Partidas
            $event->sheet->getColumnDimension('A')->setAutoSize(false);
            $event->sheet->getColumnDimension('A')->setWidth(10);
            $event->sheet->getColumnDimension('B')->setAutoSize(false);
            $event->sheet->getColumnDimension('B')->setWidth(30);
            $event->sheet->getColumnDimension('C')->setAutoSize(false);
            $event->sheet->getColumnDimension('C')->setWidth(20);
            $row = 5;
            $index_p = 0;

            foreach($this->cotizaciones['items'] as $key_item => $item){
                $event->sheet->setCellValue("A".$row, $this->verifica->encripta($item['id_item']));
                $event->sheet->setCellValue("B".$row, $item['descripcion']);
                $event->sheet->setCellValue("C".$row, $item['unidad']);
                $event->sheet->setCellValue("D".$row, $item['cantidad_solicitada']);
                $event->sheet->setCellValue("E".$row, $item['cantidad_asignada']);
                $event->sheet->setCellValue("F".$row, $item['cantidad_disponible']);
                $event->sheet->getStyle("A".$row.":F".$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DFDFDF');

                $f_suma = "=".$item['cantidad_base']."-(";
                $col_partida = 7;
                foreach($this->cotizaciones['cotizaciones'] as $id_cot => $cotizacion){
                    if($cotizacion['partidas'][$key_item] != null){
                        $range_p = $this->getLetter(($col_partida)).$row.":".$this->getLetter($col_partida+4).$row;
                        $event->sheet->setCellValueByColumnAndRow($col_partida,  $row, $cotizacion['partidas'][$key_item]['precio_unitario_format']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+1,$row, $cotizacion['partidas'][$key_item]['descuento']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+2,$row, '$'.$cotizacion['partidas'][$key_item]['importe']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+3,$row, $cotizacion['partidas'][$key_item]['moneda']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+4,$row, '$'.$cotizacion['partidas'][$key_item]['importe_moneda_conversion']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+5,$row, 0);
                        // $event->sheet->getStyle($range_p)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BEF6FC');

                        $id_item_partida = $cotizacion['partidas'][$key_item]['id_material'];
                        if(number_format($this->cotizaciones['precios_menores'][$id_item_partida],2) == number_format($cotizacion['partidas'][$key_item]['precio_unitario_compuesto'],2)){
                            $event->sheet->getStyle($range_p)->getFont()->getColor()->setRGB ('32B051');
                        }
                       
                        $event->sheet->getStyle($this->getLetter($col_partida+5).$row)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                        $event->sheet->getStyle($this->getLetter($col_partida+5).$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C6C6C6');

                        $f_suma .= $this->getLetter($col_partida+5).$row.'+';
                    }else{
                        $range_p = $this->getLetter(($col_partida)).$row.":".$this->getLetter($col_partida+5).$row;
                        $event->sheet->getStyle($range_p)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D0D0D0');
                    }
                    $col_partida +=6;
                }
                $event->sheet->setCellValue("F".$row, $f_suma."0)");
                $row++;
                $index_p++;
            }

            $cant_cotizaciones = count($this->cotizaciones['cotizaciones']);
            $range_partidas = "A5:".$this->getLetter(($cant_cotizaciones * 6) + 6).($row-1);
            $event->sheet->getDelegate()->getStyle($range_partidas)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ]
                ]
            ]);
        }];
    }

    public function headings(): array
    {
        return [];
    }

    public function getLetter($num){
        try{
            $alphabet = range('A', 'Z');
            if($num > 26){
                $x = $num / 26;
                $x1 = (int)explode('.', $x)[0];
                $num2 = $num - ($x1 * 26);
                return $alphabet[$x1-1].$alphabet[$num2-1];
            }
            return $alphabet[$num-1];
        }catch(Exception $e){
            dd( $num);
        }
        

    }
}
