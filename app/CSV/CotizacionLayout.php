<?php

namespace App\CSV;

use App\Models\CADECO\CotizacionCompra;
use App\Utils\ValidacionSistema;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class CotizacionLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $cotizacion;
    protected $moneda;
    protected $evento;
    protected $tipo_cambio;
    protected $verifica;
    protected $tc_partida_euro;
    protected $tc_partida_dlls;

    public function __construct(CotizacionCompra $cotizacion)
    {
        $this->verifica = new ValidacionSistema();
        $this->cotizacion = $cotizacion;
        $this->tc_partida_euro = 22.8097;
        $this->tc_partida_dlls = 18.6361;

//        $this->complemento = $cotizacion->complemento;

//        $tipo_cambio = Cambio::orderBy('fecha', 'desc')->first();
//        dd($tipo_cambio);
//        foreach ($tipo_cambio as $k => $v)
//            $this->tipo_cambio[$v['id_moneda']] = $v;
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
                        'bold' => true
                    ]]);
                $event->sheet->getProtection()->setSheet(true);

                $i=2;
                foreach ($this->cotizacion->cotizaciones as $cot){
                    $i++;
                    $event->sheet->setCellValue("A".$i, ($i-2));
                    $event->sheet->setCellValue("G".$i, 0);
                    $event->sheet->setCellValue("H".$i, 0);
                    $event->sheet->setCellValue("E".$i, $cot['cantidad']);
                    $event->sheet->setCellValue("F".$i, $cot['cantidad']);
                    $event->sheet->setCellValue("B".$i, '['.$cot->material->numero_parte.'] '.$cot->material->descripcion);
                    $event->sheet->setCellValue("D".$i, $cot->material->unidad);
                    $event->sheet->setCellValue("C".$i, $cot->material->id_material);

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

                    $event->sheet->protectCells('G'.$i.':J'.$i, $this->cotizacion->numero_folio);
                    $event->sheet->protectCells('G'.$i, $this->cotizacion->numero_folio);
                    $event->sheet->protectCells('L'.$i, $this->cotizacion->numero_folio);
                    $event->sheet->protectCells('K'.$i, $this->cotizacion->numero_folio);
                }
                $event->sheet->protectCells('G'.($i+1), $this->cotizacion->numero_folio);
                $event->sheet->protectCells('G'.($i+7), $this->cotizacion->numero_folio);
                $event->sheet->protectCells('G'.($i+11).':G'.($i+17), $this->cotizacion->numero_folio);

//                $tc_partida_euro = $this->complemento->tc_eur > 0?$this->complemento->tc_eur:$this->tipo_cambio[3]['cambio'];
//                $tc_partida_dlls = $this->complemento->tc_usd > 0?$this->complemento->tc_usd:$this->tipo_cambio[2]['cambio'];

                $event->sheet->setCellValue("G".($i+2), '=SUMIF(J3:J'.$i.',"PESO MXP",I3:I'.$i.')-(SUMIF(J3:J'.$i.',"PESO MXP",I3:I'.$i.')*G'.($i+1).'/100)');
                $event->sheet->setCellValue("G".($i+3), '=SUMIF(J3:J'.$i.',"DOLAR USD",I3:I'.$i.')-(SUMIF(J3:J'.$i.',"DOLAR USD",I3:I'.$i.')*G'.($i+1).'/100)');
                $event->sheet->setCellValue("G".($i+4), '=SUMIF(J3:J'.$i.',"EURO",I3:I'.$i.')-(SUMIF(J3:J'.$i.',"EURO",I3:I'.$i.')*G'.($i+1).'/100)');
                $event->sheet->setCellValue("G".($i+8), '=SUM(K3:K'.$i.')-(SUM(K3:K'.$i.')*G'.($i+1).'/100)');
                $event->sheet->setCellValue("G".($i+9), '=G'.($i+8).'*0.16');
                $event->sheet->setCellValue("G".($i+10), '=G'.($i+8).'+G'.($i+9));
                $event->sheet->setCellValue("F".($i+1), '%Descuento');
                $event->sheet->setCellValue("F".($i+2), 'Subtotal Precios Peso (MXP)');
                $event->sheet->setCellValue("F".($i+3), '%Subtotal Precios Dolar (USD)');
                $event->sheet->setCellValue("F".($i+4), 'Subtotal Precios EURO');
                $event->sheet->setCellValue("F".($i+5), 'TC USD');
                $event->sheet->setCellValue("F".($i+6), 'TC EURO');
                $event->sheet->setCellValue("F".($i+7), 'Moneda de Conv.');
                $event->sheet->setCellValue("F".($i+8), 'Subtotal Moneda Conv.');
                $event->sheet->setCellValue("F".($i+9), 'IVA');
                $event->sheet->setCellValue("F".($i+10), 'TOTAL');
                $event->sheet->setCellValue("F".($i+11), 'Fecha de Cotizacion');
                $event->sheet->setCellValue("G".($i+11), date("d/m/Y"));
                $event->sheet->setCellValue("F".($i+12), 'Pago en Parcialdades (%)');
                $event->sheet->setCellValue("F".($i+13), '% Anticipo');
                $event->sheet->setCellValue("F".($i+14), 'Credito (dias)');
                $event->sheet->setCellValue("F".($i+15), 'Tiempo de Entraga (dias)');
                $event->sheet->setCellValue("F".($i+16), 'Vigencia (dias)');
                $event->sheet->setCellValue("F".($i+17), 'Observaciones Generales');
                $event->sheet->setCellValue("G".($i+5), $this->tc_partida_dlls);
                $event->sheet->setCellValue("G".($i+6), $this->tc_partida_euro);

                //PESOS
                $objValidation = $event->sheet->getCell('G'.($i+7))->getDataValidation();
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
        return array([' ',' ',' ',' ',$this->cotizacion->id_transaccion,'#'.$this->cotizacion->numero_folio,$this->cotizacion->empresa->razon_social],
        ['#','DESCRIPCION','IDENTIFICADOR','UNIDAD','CANTIDAD_SOLICITADA','CANTIDAD_APROBADA','Precio Unitario','% Descuento','Precio Total','Moneda',
            'Precio Total Moneda Conversión','Observaciones']);
    }
}