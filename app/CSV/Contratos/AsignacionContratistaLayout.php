<?php

namespace App\CSV\Contratos;

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

class AsignacionContratistaLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $contratos;
    protected $id_contrato_p;
    protected $verifica;

    public function __construct($contratos, $id_contrato_p)
    {
        $this->verifica = new ValidacionSistema();
        $this->contratos = $contratos;
        $this->id_contrato_p = $id_contrato_p;
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        
        return [AfterSheet::class    => function(AfterSheet $event) {
            $event->sheet->getProtection()->setSheet(true);
            $col_rs = 7;
            $row = 1;

            // Validación de archivo xls
            $verificacion_estimacion = $this->verifica->encripta(Context::getDatabase()."|".Context::getIdObra()."|".$this->id_contrato_p);
            $event->sheet->setCellValue("A1", $verificacion_estimacion);

            // Encabezado con Razón Social y id presupuesto
            foreach($this->contratos['presupuestos'] as $id_pres => $presupuesto){
                $event->sheet->mergeCellsByColumnAndRow($col_rs, $row,$col_rs+8,$row);
                $event->sheet->setCellValueByColumnAndRow($col_rs, $row,$presupuesto['razon_social']);
                $event->sheet->mergeCellsByColumnAndRow($col_rs, $row+1,$col_rs+8,$row+1);
                $event->sheet->setCellValueByColumnAndRow($col_rs, $row+1,$this->verifica->encripta($id_pres));
                $col_rs += 9;
            }
            $letra_fin = $this->getLetter($col_rs-1);
            $range_rs = "G1:".$letra_fin."2";
            
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
            $row = 3;            
            $event->sheet->setCellValue("A".$row, 'IdPartida');
            $event->sheet->setCellValue("B".$row, 'Descripción');
            $event->sheet->setCellValue("C".$row, 'Destino');
            $event->sheet->setCellValue("D".$row, 'Unidad');
            $event->sheet->setCellValue("E".$row, 'Cantidad Solicitada');
            $event->sheet->setCellValue("F".$row, 'Cantidad Pendiente Asignar');

            $col_titles = 7;
            for($i = 0; $i < count($this->contratos['presupuestos']); $i++){
                $event->sheet->setCellValueByColumnAndRow($col_titles,   $row,'Precio Unitario Antes Descto.');
                $event->sheet->setCellValueByColumnAndRow($col_titles+1, $row,'Precio Total Antes Descto.');
                $event->sheet->setCellValueByColumnAndRow($col_titles+2, $row,'% Descuento');
                $event->sheet->setCellValueByColumnAndRow($col_titles+3, $row,'Precio Unitario');
                $event->sheet->setCellValueByColumnAndRow($col_titles+4, $row,'Precio Total');
                $event->sheet->setCellValueByColumnAndRow($col_titles+5, $row,'Moneda');
                $event->sheet->setCellValueByColumnAndRow($col_titles+6, $row,'Precio Total Moneda Conversión');
                $event->sheet->setCellValueByColumnAndRow($col_titles+7, $row,'Observaciones');
                $event->sheet->setCellValueByColumnAndRow($col_titles+8, $row,'Cantidad Asignada');
                $col_titles += 9;
            }

            $letra_fin = $this->getLetter($col_titles-1);
            $range_titles = "A3:".$letra_fin."3";

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
            $row = 4;
            $index_p = 0;
            foreach($this->contratos['items'] as $key_item => $item){
                $event->sheet->setCellValue("A".$row, $this->verifica->encripta($item['id_concepto']));
                $event->sheet->setCellValue("B".$row, $item['descripcion']);
                $event->sheet->setCellValue("C".$row, $item['destino_corto']);
                $event->sheet->setCellValue("D".$row, $item['unidad']);
                $event->sheet->setCellValue("E".$row, $item['cantidad_solicitada']);
                $event->sheet->getStyle("A".$row.":F".$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DFDFDF');

                $f_suma = "=".$item['cantidad_disponible']."-(";
                $col_partida = 7;
                foreach($this->contratos['presupuestos'] as $id_pres => $presupuesto){
                    if($presupuesto['partidas'][$key_item] != null){
                        $range_p = $this->getLetter(($col_partida)).$row.":".$this->getLetter($col_partida+7).$row;
                        $event->sheet->setCellValueByColumnAndRow($col_partida,  $row, $presupuesto['partidas'][$key_item]['precio_unitario']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+1,$row, $presupuesto['partidas'][$key_item]['precio_total_antes_desc']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+2,$row, $presupuesto['partidas'][$key_item]['descuento']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+3,$row, $presupuesto['partidas'][$key_item]['precio_unitario_con_desc']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+4,$row, $presupuesto['partidas'][$key_item]['precio_total_con_desc']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+5,$row, $presupuesto['partidas'][$key_item]['moneda']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+6,$row, $presupuesto['partidas'][$key_item]['importe_moneda_conversion']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+7,$row, $presupuesto['partidas'][$key_item]['observaciones']);
                        $event->sheet->setCellValueByColumnAndRow($col_partida+8,$row, 0);
                        // $event->sheet->getStyle($range_p)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BEF6FC');

                        $id_concepto_partida = $presupuesto['partidas'][$key_item]['id_concepto'];
                        if(number_format($this->contratos['precios_menores'][$id_concepto_partida],2) == number_format($presupuesto['partidas'][$key_item]['precio_unitario_con_desc_sf'],2)){
                            $event->sheet->getStyle($range_p)->getFont()->getColor()->setRGB ('32B051');
                        }
                       
                        $event->sheet->getStyle($this->getLetter($col_partida+8).$row)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                        $event->sheet->getStyle($this->getLetter($col_partida+8).$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C6C6C6');

                        $f_suma .= $this->getLetter($col_partida+8).$row.'+';
                    }else{
                        $range_p = $this->getLetter(($col_partida)).$row.":".$this->getLetter($col_partida+8).$row;
                        $event->sheet->getStyle($range_p)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D0D0D0');
                    }
                    $col_partida +=9;
                }
                $event->sheet->setCellValue("F".$row, $f_suma."0)");
                $row++;
                $index_p++;
            }

            $cant_presupuestos = count($this->contratos['presupuestos']);
            $range_partidas = "A4:".$this->getLetter(($cant_presupuestos * 9) + 6).($row-1);
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