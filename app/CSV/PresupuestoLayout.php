<?php

namespace App\CSV;

use App\Models\CADECO\Cambio;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\Item;
use App\Models\CADECO\Moneda;
use App\Models\CADECO\PresupuestoContratista;
use App\Utils\ValidacionSistema;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
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

    public function __construct(PresupuestoContratista $presupuesto)
    {
        $this->verifica = new ValidacionSistema();
        $this->presupuesto = $presupuesto;
        $moneda = Moneda::get();
        
        $this->tc_partida_dlls = ($presupuesto) ? $presupuesto->TcUSD : $moneda[0]->cambioIgh->tipo_cambio;
        $this->tc_partida_euro = ($presupuesto) ? $presupuesto->TcEuro : $moneda[1]->cambioIgh->tipo_cambio;
        // dd($this);
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:L2'; // All headers

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name'      =>  'arial',
                        'bold' => true
                    ]]);
                $event->sheet->getProtection()->setSheet(true);

                $event->sheet->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getColumnDimension('B')->setWidth(60);
                $event->sheet->getColumnDimension('C')->setAutoSize(false);
                $event->sheet->getColumnDimension('C')->setWidth(15);
                $event->sheet->getColumnDimension('J')->setAutoSize(false);
                $event->sheet->getColumnDimension('J')->setWidth(12.5);
                $event->sheet->getColumnDimension('G')->setAutoSize(false);
                $event->sheet->getColumnDimension('G')->setWidth(20);
                $event->sheet->getColumnDimension('L')->setAutoSize(true);

                // dd('para');

                $i=2;
                foreach ($this->presupuesto->partidas as $cot){
                    // dd($cot->presupuesto->contratoProyectado->id_transaccion, $cot->id_concepto);
                    $item = Contrato::where('id_transaccion', '=', $cot->presupuesto->contratoProyectado->id_transaccion)->where('id_concepto', '=', $cot->id_concepto)->first();
                    // dd($item, $cot);
                    $id_moneda = ($cot->IdMoneda > 1) ? (($cot->IdMoneda == 2) ? "DOLAR USD" : "EURO") : "PESO MXP";
                    // dd($id_moneda);
                    $datos = $cot->id_concepto;
                    $cadena_json_id = json_encode($datos);
                    $cadena_encriptar = $cadena_json_id . ">";
                    $firmada = $this->verifica->encripta($cadena_encriptar);
                    // dd('Cot', $cot->id_transaccion, $cot);
                    $i++;
                    $event->sheet->setCellValue("A".$i, ($i-2));
                    $event->sheet->setCellValue("G".$i, $cot->precio_unitario_convert);
                    $event->sheet->setCellValue("H".$i, $cot->PorcentajeDescuento);
                    $event->sheet->setCellValue("E".$i, $item->cantidad_original_format);
                    $event->sheet->setCellValue("F".$i, $item->cantidad_presupuestada_format);
                    $event->sheet->setCellValue("B".$i, $item->descripcion_guion_nivel_format);
                    $event->sheet->setCellValue("D".$i, $item->unidad);
                    $event->sheet->setCellValue("C".$i, $firmada);
                    $event->sheet->setCellValue("J".$i, $id_moneda);
                    $event->sheet->setCellValue("L".$i, $cot->Observaciones);

                    //MONEDAS
                    $objValidation = $event->sheet->getCell('J'.$i)->getDataValidation();
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
                    $objValidation->setFormula1('"EURO, DOLAR USD, PESO MXP"');
                    $event->sheet->setCellValue('K'.$i,'=IF(J'.$i.'="EURO",I'.$i.'*'.$this->tc_partida_euro.'/1,IF(J'.$i.'="DOLAR USD",I'.$i.'*'.$this->tc_partida_dlls.'/1, IF(J'.$i.'="PESO MXP",I'.$i.',0)))');
                    $event->sheet->setCellValue("I".$i, '=G'.$i.'*E'.$i.'-((G'.$i.'*E'.$i.'*H'.$i.')/100)');

                    $event->sheet->getStyle('G'.$i.':H'.$i)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                    $event->sheet->getStyle('J'.$i)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                    $event->sheet->getStyle('L'.$i)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                }
                // $event->sheet->getStyle('G'.($i+1))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                // $event->sheet->getStyle('G'.($i+7))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                // $event->sheet->getStyle('G'.($i+11))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                // $event->sheet->getStyle('G'.($i+12))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                // $event->sheet->getStyle('G'.($i+13))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                // $event->sheet->getStyle('G'.($i+14))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                // $event->sheet->getStyle('G'.($i+15))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                // $event->sheet->getStyle('G'.($i+16))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                // $event->sheet->getStyle('G'.($i+17))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

                // $event->sheet->setCellValue("G".($i+2), '=SUMIF(J3:J'.$i.',"PESO MXP",I3:I'.$i.')-(SUMIF(J3:J'.$i.',"PESO MXP",I3:I'.$i.')*G'.($i+1).'/100)');
                // $event->sheet->setCellValue("G".($i+3), '=SUMIF(J3:J'.$i.',"DOLAR USD",I3:I'.$i.')-(SUMIF(J3:J'.$i.',"DOLAR USD",I3:I'.$i.')*G'.($i+1).'/100)');
                // $event->sheet->setCellValue("G".($i+4), '=SUMIF(J3:J'.$i.',"EURO",I3:I'.$i.')-(SUMIF(J3:J'.$i.',"EURO",I3:I'.$i.')*G'.($i+1).'/100)');
                // $event->sheet->setCellValue("G".($i+8), '=SUM(K3:K'.$i.')-(SUM(K3:K'.$i.')*G'.($i+1).'/100)');
                // $event->sheet->setCellValue("G".($i+9), '=G'.($i+8).'*0.16');
                // $event->sheet->setCellValue("G".($i+10), '=G'.($i+8).'+G'.($i+9));
                // $event->sheet->setCellValue("F".($i+1), '%Descuento');
                // $event->sheet->setCellValue("G".($i+1), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->descuento : 0);
                // $event->sheet->setCellValue("F".($i+2), 'Subtotal Precios Peso (MXP)');
                // $event->sheet->setCellValue("F".($i+3), '%Subtotal Precios Dolar (USD)');
                // $event->sheet->setCellValue("F".($i+4), 'Subtotal Precios EURO');
                // $event->sheet->setCellValue("F".($i+5), 'TC USD');
                // $event->sheet->setCellValue("F".($i+6), 'TC EURO');
                // $event->sheet->setCellValue("F".($i+7), 'Moneda de Conv.');
                // $event->sheet->setCellValue("F".($i+8), 'Subtotal Moneda Conv.');
                // $event->sheet->setCellValue("F".($i+9), 'IVA');
                // $event->sheet->setCellValue("F".($i+10), 'TOTAL');
                // $event->sheet->setCellValue("F".($i+11), 'Fecha de Cotizacion');
                // $event->sheet->setCellValue("G".($i+11), date("d/m/Y"));
                // $event->sheet->setCellValue("F".($i+12), 'Pago en Parcialdades (%)');
                // $event->sheet->setCellValue("G".($i+12), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->parcialidades : 0);
                // $event->sheet->setCellValue("F".($i+13), '% Anticipo');
                // $event->sheet->setCellValue("G".($i+13), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->anticipo : 0);
                // $event->sheet->setCellValue("F".($i+14), 'Credito (dias)');
                // $event->sheet->setCellValue("G".($i+14), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->dias_credito : 0);
                // $event->sheet->setCellValue("F".($i+15), 'Tiempo de Entraga (dias)');
                // $event->sheet->setCellValue("G".($i+15), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->plazo_entrega : 0);
                // $event->sheet->setCellValue("F".($i+16), 'Vigencia (dias)');
                // $event->sheet->setCellValue("G".($i+16), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->vigencia : 0);
                // $event->sheet->setCellValue("F".($i+17), 'Observaciones Generales');
                // $event->sheet->setCellValue("G".($i+17), $this->cotizacion->observaciones);
                // $event->sheet->setCellValue("G".($i+5), $this->tc_partida_dlls);
                // $event->sheet->setCellValue("G".($i+6), $this->tc_partida_euro);
                // $event->sheet->setCellValue("G".($i+7), "PESO MX");

                // //PESOS
                // $objValidation = $event->sheet->getCell('G'.($i+7))->getDataValidation();
                // $objValidation->setType(DataValidation::TYPE_LIST);
                // $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                // $objValidation->setAllowBlank(false);
                // $objValidation->setShowInputMessage(true);
                // $objValidation->setShowErrorMessage(true);
                // $objValidation->setShowDropDown(true);
                // $objValidation->setFormula1('"PESO MX"');
            },
        ];
    }

    public function headings(): array
    {
        return array([' ',' ',' ',' ',' ',' ',($this->presupuesto->empresa) ? $this->presupuesto->empresa->razon_social : '----- Proveedor Desconocido ----- '],
        ['#','DESCRIPCION','IDENTIFICADOR','UNIDAD','CANTIDAD_SOLICITADA','CANTIDAD_APROBADA','Precio Unitario','% Descuento','Precio Total','Moneda',
            'Precio Total Moneda Conversión','Observaciones']);
    }
}
