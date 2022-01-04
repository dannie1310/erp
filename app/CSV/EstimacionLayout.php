<?php


namespace App\CSV;


use App\Facades\Context;
use App\Utils\ValidacionSistema;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Subcontrato;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class EstimacionLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $subcontrato;
    protected $moneda;
    protected $tipo_cambio;
    protected $verifica;
    protected $tc_partida_euro;
    protected $tc_partida_dlls;

    public function __construct(Subcontrato $subcontrato)
    {
        $this->verifica = new ValidacionSistema();
        $this->subcontrato = $subcontrato;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:N2'; // All headers

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name'      =>  'arial',
                        'bold' => true
                    ]]);
                $event->sheet->getDelegate()->getStyle('A8:N8')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ]
                    ]
                ]);
                $event->sheet->getProtection()->setSheet(true);

                $event->sheet->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getColumnDimension('A')->setWidth(5);
                $event->sheet->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getColumnDimension('B')->setWidth(30);
                $event->sheet->getColumnDimension('C')->setAutoSize(false);
                $event->sheet->getColumnDimension('C')->setWidth(18);
                $event->sheet->getColumnDimension('H')->setAutoSize(false);
                $event->sheet->getColumnDimension('H')->setWidth(15);

                $event->sheet->setCellValue("B3", 'Fecha de Estimación');
                $event->sheet->getStyle('C3')->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $fecha = date("d/m/Y");
                $event->sheet->setCellValue("C3", $fecha);
                $event->sheet->getStyle('C3')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getDelegate()->getStyle('B3:C3')->applyFromArray([
                    'font' => ['bold' => true]
                ]);
                $event->sheet->setCellValue("B4", 'Fecha Inicio de Estimación');
                $event->sheet->getStyle('C4')->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $fecha = date("d/m/Y");
                $event->sheet->setCellValue("C4", $fecha);
                $event->sheet->getStyle('C4')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getDelegate()->getStyle('B4:C4')->applyFromArray([
                    'font' => ['bold' => true]
                ]);
                $event->sheet->setCellValue("B5", 'Fecha Fin de Estimación');
                $event->sheet->getStyle('C5')->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $fecha = date("d/m/Y");
                $event->sheet->setCellValue("C5", $fecha);
                $event->sheet->getStyle('C5')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getDelegate()->getStyle('B5:C5')->applyFromArray([
                    'font' => ['bold' => true]
                ]);
                $event->sheet->getStyle('C3:C5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E2E2');
                $i = 8;
                $verificacion_estimacion = $this->verifica->encripta(Context::getDatabase()."|".Context::getIdObra()."|".$this->subcontrato->id_transaccion);
                $event->sheet->setCellValue("A1", $verificacion_estimacion);
                $datos_subcontrato = $this->subcontrato->subcontratoParaEstimar(null);

                $event->sheet->getColumnDimension('D')->setAutoSize(false);
                $event->sheet->getColumnDimension('D')->setWidth(90);
                $event->sheet->getColumnDimension('K')->setAutoSize(false);
                $event->sheet->getColumnDimension('K')->setWidth(4);
                foreach ($datos_subcontrato['partidas'] as $key => $item) {
                    if (array_key_exists('id', $item)) {
                        $datos = $item['id'];
                    } else {
                        $datos = $key;
                    }
                    $cadena_json_id = json_encode($datos);
                    $cadena_encriptar = $cadena_json_id . ">";
                    $firmada = $this->verifica->encripta($cadena_encriptar);
                    $i++;
                    $event->sheet->setCellValue("A" . $i, ($i - 8));
                    if (array_key_exists('id', $item)) {
                        $event->sheet->setCellValue("B" . $i, $key);
                        $event->sheet->setCellValue("C" . $i, $item['clave']);
                        $event->sheet->setCellValue("D" . $i, $item['descripcion_concepto']);
                        $event->sheet->setCellValue("E" . $i, $item['unidad']);
                        $event->sheet->setCellValue("F" . $i, $item['cantidad_subcontrato']);
                        $event->sheet->setCellValue("G" . $i, $item['precio_unitario_subcontrato']);
                        $event->sheet->setCellValue("H" . $i, number_format($item['cantidad_por_estimar'],3));
                        $event->sheet->setCellValue("I" . $i, number_format($item['importe_por_estimar'],2));
                        $event->sheet->setCellValue("J" . $i, 0);
                        $event->sheet->setCellValue("K" . $i, 0);
                        $event->sheet->setCellValue("L" . $i, $item['precio_unitario_subcontrato_format']);
                        $event->sheet->setCellValue("M" . $i, 0);
                        $event->sheet->setCellValue("N" . $i, $item['destino_path']);
                        $event->sheet->setCellValue("O" . $i, $firmada);
                        $event->sheet->getStyle('J'.$i)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                        $event->sheet->getStyle('J'.$i)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E2E2');
                        $event->sheet->setCellValue("K" . $i, '=J'.$i.'*100/F'.$i);
                        $event->sheet->setCellValue("M" . $i, '=J'.$i.'*G'.$i);
                        $event->sheet->getDelegate()->getStyle('J'.$i.':M'.$i)->applyFromArray(['font' => ['bold' => true]]);
                    } else {
                        $event->sheet->setCellValue("B" . $i, $key);
                        $event->sheet->setCellValue("C" . $i, $item['clave']);
                        $event->sheet->getDelegate()->getStyle('D'.$i)->applyFromArray(['font' => ['bold' => true]]);
                        $event->sheet->setCellValue("D" . $i,  $item['descripcion']);
                    }
                }

                $event->sheet->getDelegate()->getStyle('C'.($i+3).':D'.($i+3))->applyFromArray([
                    'font' => ['bold' => true]
                ]);
                $event->sheet->setCellValue("C" . ($i+3), 'OBSERVACIONES');
                $event->sheet->setCellValue("D" . ($i+3), $this->subcontrato->presupuesto->observaciones);
            },
        ];

    }

    public function headings(): array
    {
        return array([' ','',' ',' ',' ',' ',' ',($this->subcontrato->empresa) ? $this->subcontrato->empresa->razon_social : '----- Proveedor Desconocido ----- '],[],[],[],[],[],[],
            ['#','','CLAVE','CONCEPTO','UM','VOLUMEN CONTRATADO','P.U. CONTRATADO','VOLUMEN SALDO','IMPORTE SALDO','VOLUMEN','%','PRECIO UNITARIO','IMPORTE',
                'DISTRIBUCIÓN DESTINO']);
    }
}
