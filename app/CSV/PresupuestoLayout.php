<?php

namespace App\CSV;

use App\Facades\Context;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Moneda;
use App\Models\CADECO\PresupuestoContratista;
use App\Utils\ValidacionSistema;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Protection;

use function Complex\cot;

class PresupuestoLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $presupuesto;
    protected $moneda;
    protected $evento;
    protected $tipo_cambio;
    protected $verifica;
    protected $tc_partida_euro;
    protected $tc_partida_dlls;
    protected $tc_partida_libra;

    public function __construct(PresupuestoContratista $presupuesto)
    {
        $this->verifica = new ValidacionSistema();
        $this->presupuesto = $presupuesto;

        $moneda = Moneda::orderBy('id_moneda', 'ASC')->get();
        $this->tc_partida_dlls  = ($presupuesto->TcUSD>0) ? $presupuesto->TcUSD : $moneda[1]->cambio->cambio;
        $this->tc_partida_euro  = ($presupuesto->TcEuro>0) ? $presupuesto->TcEuro : $moneda[2]->cambio->cambio;
        $this->tc_partida_libra = ($presupuesto->TcLibra>0) ? $presupuesto->TcLibra : $moneda[3]->cambio->cambio;
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:O2'; // All headers

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name'      =>  'arial',
                        'bold' => true
                    ],
                    ]);
                $event->sheet->getDelegate()->getStyle('A2:O2')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ]
                    ]
                    ]);
                $event->sheet->getProtection()->setSheet(true);

                $event->sheet->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getColumnDimension('A')->setWidth(10);
                $event->sheet->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getColumnDimension('B')->setWidth(60);
                $event->sheet->getColumnDimension('C')->setAutoSize(false);
                $event->sheet->getColumnDimension('C')->setWidth(15);
                $event->sheet->getColumnDimension('J')->setAutoSize(false);
                $event->sheet->getColumnDimension('J')->setWidth(17.50);
                $event->sheet->getColumnDimension('E')->setAutoSize(false);
                $event->sheet->getColumnDimension('E')->setWidth(21.15);
                $event->sheet->getColumnDimension('F')->setAutoSize(false);
                $event->sheet->getColumnDimension('F')->setWidth(27.50);
                $event->sheet->getColumnDimension('H')->setAutoSize(false);
                $event->sheet->getColumnDimension('H')->setWidth(28.50);
                $event->sheet->getColumnDimension('G')->setAutoSize(false);
                $event->sheet->getColumnDimension('G')->setWidth(17);
                $event->sheet->getColumnDimension('K')->setAutoSize(false);
                $event->sheet->getColumnDimension('K')->setWidth(14);
                $event->sheet->getColumnDimension('M')->setAutoSize(false);
                $event->sheet->getColumnDimension('M')->setWidth(39);
                $event->sheet->getColumnDimension('N')->setAutoSize(false);
                $event->sheet->getColumnDimension('N')->setWidth(36);
                $event->sheet->getColumnDimension('L')->setAutoSize(false);
                $event->sheet->getColumnDimension('L')->setWidth(11);
                $event->sheet->getColumnDimension('O')->setAutoSize(true);

                $i=2;
                if(Context::getDatabase() && Context::getIdObra()){
                    $verificacion_cotizacion = $this->verifica->encripta(Context::getDatabase()."|".Context::getIdObra()."|".$this->presupuesto->id_transaccion);
                }else{
                    $verificacion_cotizacion = $this->verifica->encripta($this->presupuesto->invitacion->base_datos."|".$this->presupuesto->invitacion->id_obra."|".$this->presupuesto->id_transaccion);
                }

                $event->sheet->setCellValue("A1", $verificacion_cotizacion);
                $t_part = count($this->presupuesto->partidas);
                foreach ($this->presupuesto->partidas as $cot){
                    $item = Contrato::where('id_transaccion', '=', $this->presupuesto->id_antecedente)->where('id_concepto', '=', $cot->id_concepto)->first();
                    $id_moneda = '';
                    switch ((int)$cot->IdMoneda){
                        case 1:
                            $id_moneda = 'PESO MXN';
                        break;
                        case 2:
                            $id_moneda = 'DOLAR USD';
                        break;
                        case 3:
                            $id_moneda = 'EURO';
                        break;
                        case 4:
                            $id_moneda = 'LIBRA';
                        break;
                    }
                    $datos = $cot->id_concepto;
                    $cadena_json_id = json_encode($datos);
                    $cadena_encriptar = $cadena_json_id . ">";
                    $firmada = $this->verifica->encripta($cadena_encriptar);
                    $i++;
                    $event->sheet->setCellValue("A".$i, ($i-2));
                    $event->sheet->setCellValue("G".$i, $cot->precio_unitario_convert);
                    $event->sheet->setCellValue("I".$i, $cot->PorcentajeDescuento);
                    $event->sheet->setCellValue("E".$i, $item->cantidad_original);
                    $event->sheet->setCellValue("F".$i, $item->cantidad_presupuestada);
                    $event->sheet->setCellValue("B".$i, $item->descripcion_guion_nivel_format);
                    $event->sheet->setCellValue("D".$i, $item->unidad);
                    $event->sheet->setCellValue("C".$i, $firmada);
                    $event->sheet->setCellValue("L".$i, $id_moneda);
                    $event->sheet->setCellValue("O".$i, $cot->Observaciones);

                    //MONEDAS
                    $objValidation = $event->sheet->getCell('L'.$i)->getDataValidation();
                    $objValidation->setType(DataValidation::TYPE_LIST);
                    $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $objValidation->setAllowBlank(false);
                    $objValidation->setShowInputMessage(true);
                    $objValidation->setShowErrorMessage(true);
                    $objValidation->setShowDropDown(true);
                    $objValidation->setErrorTitle('Input error');
                    $objValidation->setError('Value is not in list.');
                    $objValidation->setPromptTitle('Choose from list');
                    $objValidation->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation->setFormula1('"LIBRA, EURO, DOLAR USD, PESO MXN"');

                    $event->sheet->setCellValue('N'.$i,'=IF(L'.$i.'="EURO",K'.$i.'*G'.($t_part+9).'/1,IF(L'.$i.'="LIBRA",K'.$i.'*G'.($t_part+10).'/1,IF(L'.$i.'="DOLAR USD",K'.$i.'*G'.($t_part+8).'/1, IF(L'.$i.'="PESO MXN",K'.$i.',0))))');
                    $event->sheet->setCellValue('M'.$i,'=IF(L'.$i.'="EURO",J'.$i.'*G'.($t_part+9).'/1,IF(L'.$i.'="LIBRA",J'.$i.'*G'.($t_part+10).'/1,IF(L'.$i.'="DOLAR USD",J'.$i.'*G'.($t_part+8).'/1, IF(L'.$i.'="PESO MXN",J'.$i.',0))))');
                    $event->sheet->setCellValue("K".$i, '=G'.$i.'*F'.$i.'-((G'.$i.'*F'.$i.'*I'.$i.')/100)');
                    $event->sheet->setCellValue("J".$i, '=G'.$i.'-((G'.$i.'*I'.$i.')/100)');
                    $event->sheet->setCellValue("H".$i, '=G'.$i.'*F'.$i);

                    $event->sheet->getStyle('G'.$i)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                    $event->sheet->getStyle('I'.$i)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                    $event->sheet->getStyle('L'.$i)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                }
                $event->sheet->getStyle('G'.($i+1))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+6))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+7))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+8))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+11))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+13))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+14))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+15))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+16))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getDelegate()->getStyle('F'.($i+1).':F'.($i+15))->applyFromArray([
                    'font' => [
                        'bold' => true
                    ],
                    ]);

                $event->sheet->setCellValue("F".($i+1), '% Descuento');
                $event->sheet->setCellValue("G".($i+1), ($this->presupuesto->PorcentajeDescuento) ? $this->presupuesto->PorcentajeDescuento : 0);

                $event->sheet->setCellValue("F".($i+2), 'Subtotal Precios Peso (MXN)');
                $event->sheet->setCellValue("G".($i+2), '=SUMIF(L3:L'.$i.',"PESO MXN",K3:K'.$i.')-(SUMIF(L3:L'.$i.',"PESO MXN",K3:K'.$i.')*G'.($i+1).'/100)');

                $event->sheet->setCellValue("F".($i+3), '%Subtotal Precios Dolar (USD)');
                $event->sheet->setCellValue("G".($i+3), '=SUMIF(L3:L'.$i.',"DOLAR USD",K3:K'.$i.')-(SUMIF(L3:L'.$i.',"DOLAR USD",K3:K'.$i.')*G'.($i+1).'/100)');

                $event->sheet->setCellValue("F".($i+4), 'Subtotal Precios EURO');
                $event->sheet->setCellValue("G".($i+4), '=SUMIF(L3:L'.$i.',"EURO",K3:K'.$i.')-(SUMIF(L3:L'.$i.',"EURO",K3:K'.$i.')*G'.($i+1).'/100)');

                $event->sheet->setCellValue("F".($i+5), 'Subtotal Precios LIBRA');
                $event->sheet->setCellValue("G".($i+5), '=SUMIF(L3:L'.$i.',"LIBRA",K3:K'.$i.')-(SUMIF(L3:L'.$i.',"LIBRA",K3:K'.$i.')*G'.($i+1).'/100)');

                $event->sheet->setCellValue("F".($i+6), 'TC USD');
                $event->sheet->setCellValue("G".($i+6), $this->tc_partida_dlls);

                $event->sheet->setCellValue("F".($i+7), 'TC EURO');
                $event->sheet->setCellValue("G".($i+7), $this->tc_partida_euro);

                $event->sheet->setCellValue("F".($i+8), 'TC LIBRA');
                $event->sheet->setCellValue("G".($i+8), $this->tc_partida_libra);

                $event->sheet->setCellValue("F".($i+9), 'Moneda de Conv.');
                $event->sheet->setCellValue("G".($i+9), "PESO MX");

                $event->sheet->setCellValue("F".($i+10), 'Subtotal Moneda Conv.');
                $event->sheet->setCellValue("G".($i+10), '=SUM(N3:N'.$i.')-(SUM(N3:N'.$i.')*G'.($i+1).'/100)');

                $event->sheet->setCellValue("F".($i+11), 'Tasa de IVA');
                $event->sheet->setCellValue("G".($i+11), $this->presupuesto->tasa_iva);

                $event->sheet->setCellValue("F".($i+12), 'IVA');
                $event->sheet->setCellValue("G".($i+12), '=G'.($i+10).'*(G'.($i+11).'/100)');

                $event->sheet->setCellValue("F".($i+13), 'TOTAL');
                $event->sheet->setCellValue("G".($i+13), '=G'.($i+10).'+G'.($i+12));

                $event->sheet->setCellValue("F".($i+14), 'Fecha de Presupuesto');
                $event->sheet->setCellValue("G".($i+14), date("d/m/Y"));

                $event->sheet->setCellValue("F".($i+15), '% Anticipo');
                $event->sheet->setCellValue("G".($i+15), ($this->presupuesto->anticipo) ? $this->presupuesto->anticipo : 0);

                $event->sheet->setCellValue("F".($i+16), 'Credito (dias)');
                $event->sheet->setCellValue("G".($i+16), ($this->presupuesto->DiasCredito) ? $this->presupuesto->DiasCredito : 0);

                $event->sheet->setCellValue("F".($i+17), 'Vigencia (dias)');
                $event->sheet->setCellValue("G".($i+17), ($this->presupuesto->DiasVigencia) ? $this->presupuesto->DiasVigencia : 0);

                $event->sheet->setCellValue("F".($i+18), 'Observaciones Generales');
                $event->sheet->setCellValue("G".($i+18), $this->presupuesto->observaciones);

                //PESOS
                $objValidation = $event->sheet->getCell('G'.($i+9))->getDataValidation();
                $objValidation->setType(DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setFormula1('"PESO MX"');
            },
        ];
    }

    public function headings(): array
    {
        return array([' ',' ',' ',' ',' ',' ',($this->presupuesto->empresa) ? $this->presupuesto->empresa->razon_social : '----- Proveedor Desconocido ----- '],
        ['#',' Descripción ',' Identificador ',' Unidad ',' Cantidad Solicitada ','Cantidad Aprobada',' Precio Unitario ',' Precio Total Antes Descto. ',' % Descuento ',' Precio Unitario ',' Precio Total ',' Moneda ',
            ' Precio Unitario Moneda Conversión ',' Precio Total Moneda Conversión ',' Observaciones ']);
    }
}
