<?php

namespace App\CSV;

use App\Facades\Context;
use App\Models\CADECO\SolicitudCompra;
use function Complex\cot;
use App\Models\CADECO\Item;
use App\Models\CADECO\Cambio;
use App\Models\CADECO\Moneda;
use App\Utils\ValidacionSistema;
use App\Models\CADECO\CotizacionCompra;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\CADECO\CotizacionCompraPartida;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use PhpOffice\PhpSpreadsheet\Style\Protection;
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

        $moneda = Moneda::orderBy('id_moneda', 'ASC')->get();
        $this->tc_partida_dlls  = ($cotizacion->complemento) ? $cotizacion->complemento->tc_usd : $moneda[1]->cambio->cambio;
        $this->tc_partida_euro  = ($cotizacion->complemento) ? $cotizacion->complemento->tc_eur : $moneda[2]->cambio->cambio;
        $this->tc_partida_libra = ($cotizacion->complemento) ? $cotizacion->complemento->tc_libra : $moneda[3]->cambio->cambio;
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

                $event->sheet->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getColumnDimension('A')->setWidth(10);
                $event->sheet->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getColumnDimension('B')->setWidth(60);
                $event->sheet->getColumnDimension('C')->setAutoSize(false);
                $event->sheet->getColumnDimension('C')->setWidth(15);
                $event->sheet->getColumnDimension('J')->setAutoSize(false);
                $event->sheet->getColumnDimension('J')->setWidth(12.5);
                $event->sheet->getColumnDimension('G')->setAutoSize(false);
                $event->sheet->getColumnDimension('G')->setWidth(20);
                $event->sheet->getColumnDimension('L')->setAutoSize(true);

                $i=2;
                $solicitud = $this->cotizacion->solicitud;
                if(is_null($solicitud))
                {
                    $solicitud = SolicitudCompra::where('id_transaccion', $this->cotizacion->id_antecedente)->withoutGlobalScopes()->first();
                    $verificacion_cotizacion = $this->verifica->encripta($this->cotizacion->invitacion->base_datos."|".$this->cotizacion->invitacion->id_obra."|".$this->cotizacion->id_transaccion);
                }else{
                    $verificacion_cotizacion = $this->verifica->encripta(Context::getDatabase()."|".Context::getIdObra()."|".$this->cotizacion->id_transaccion);
                }
                $event->sheet->setCellValue("A1", $verificacion_cotizacion);

                foreach ($solicitud->partidas as $item){
                    $cot = CotizacionCompraPartida::where('id_transaccion', '=', $this->cotizacion->id_transaccion)->where('id_material', '=', $item->id_material)->first();
                    if(!$cot){continue;}
                    $id_moneda = '';
                    switch ((int)$cot->id_moneda){
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
                    $datos = $cot->id_material;
                    $cadena_json_id = json_encode($datos);
                    $cadena_encriptar = $cadena_json_id . ">";
                    $firmada = $this->verifica->encripta($cadena_encriptar);
                    $i++;
                    $event->sheet->setCellValue("A".$i, ($i-2));
                    $event->sheet->setCellValue("G".$i, $cot->precio_unitario);
                    $event->sheet->setCellValue("H".$i, ($cot->partida) ? $cot->partida->descuento_partida : 0);
                    $event->sheet->setCellValue("E".$i, ($item->cantidad_original1 > 0) ? $item->cantidad_original1 : $cot['cantidad']);
                    $event->sheet->setCellValue("F".$i, $cot['cantidad']);
                    $event->sheet->setCellValue("B".$i, '['.$cot->material->numero_parte.'] '.$cot->material->descripcion);
                    $event->sheet->setCellValue("D".$i, $cot->material->unidad);
                    $event->sheet->setCellValue("C".$i, $firmada);
                    $event->sheet->setCellValue("J".$i, $id_moneda);
                    if($cot->partida)
                    {
                        $event->sheet->setCellValue("L".$i, $cot->partida->observaciones);
                    }
                    //MONEDAS
                    $objValidation = $event->sheet->getCell('J'.$i)->getDataValidation();
                    $objValidation->setType(DataValidation::TYPE_LIST);
                    $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $objValidation->setAllowBlank(false);
                    $objValidation->setShowInputMessage(true);
                    $objValidation->setShowErrorMessage(true);
                    $objValidation->setShowDropDown(true);
                    $objValidation->setErrorTitle('Error de entrada');
                    $objValidation->setError('El valor no esta en la lista');
                    $objValidation->setPromptTitle('Seleccione de la lista');
                    $objValidation->setPrompt('Por favor seleccione un valor de la lista');
                    $objValidation->setFormula1('"LIBRA, EURO, DOLAR USD, PESO MXN"');
                    $event->sheet->setCellValue('K'.$i,'=IF(J'.$i.'="LIBRA",I'.$i.'*'.$this->tc_partida_libra.'/1,IF(J'.$i.'="EURO",I'.$i.'*'.$this->tc_partida_euro.'/1,IF(J'.$i.'="DOLAR USD",I'.$i.'*'.$this->tc_partida_dlls.'/1, IF(J'.$i.'="PESO MXN",I'.$i.',0))))');
                    $event->sheet->setCellValue("I".$i, '=G'.$i.'*E'.$i.'-((G'.$i.'*E'.$i.'*H'.$i.')/100)');

                    $event->sheet->getStyle('G'.$i.':H'.$i)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                    $event->sheet->getStyle('J'.$i)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                    $event->sheet->getStyle('L'.$i)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                }
                $event->sheet->getStyle('G'.($i+1))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+6))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+7))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+8))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+11))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+14))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+15))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+16))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+17))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->getStyle('G'.($i+18))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $event->sheet->setCellValue("G".($i+2), '=SUMIF(J3:J'.$i.',"PESO MXN",I3:I'.$i.')-(SUMIF(J3:J'.$i.',"PESO MXN",I3:I'.$i.')*G'.($i+1).'/100)');
                $event->sheet->setCellValue("G".($i+3), '=SUMIF(J3:J'.$i.',"DOLAR USD",I3:I'.$i.')-(SUMIF(J3:J'.$i.',"DOLAR USD",I3:I'.$i.')*G'.($i+1).'/100)');
                $event->sheet->setCellValue("G".($i+4), '=SUMIF(J3:J'.$i.',"EURO",I3:I'.$i.')-(SUMIF(J3:J'.$i.',"EURO",I3:I'.$i.')*G'.($i+1).'/100)');
                $event->sheet->setCellValue("G".($i+5), '=SUMIF(J3:J'.$i.',"LIBRA",I3:I'.$i.')-(SUMIF(J3:J'.$i.',"LIBRA",I3:I'.$i.')*G'.($i+1).'/100)');
                $event->sheet->setCellValue("G".($i+10), '=SUM(K3:K'.$i.')-(SUM(K3:K'.$i.')*G'.($i+1).'/100)');
                $event->sheet->setCellValue("G".($i+11), $this->cotizacion->tasa_iva_format);
                $event->sheet->setCellValue("G".($i+12), '=G'.($i+10).'*(G'.($i+11).'/100)');
                $event->sheet->setCellValue("G".($i+13), '=G'.($i+10).'+G'.($i+12));
                $event->sheet->setCellValue("F".($i+1), '%Descuento');
                $event->sheet->setCellValue("G".($i+1), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->descuento : 0);
                $event->sheet->setCellValue("F".($i+2), 'Subtotal Precios Peso (MXN)');
                $event->sheet->setCellValue("F".($i+3), '%Subtotal Precios Dolar (USD)');
                $event->sheet->setCellValue("F".($i+4), 'Subtotal Precios EURO');
                $event->sheet->setCellValue("F".($i+5), 'Subtotal Precios LIBRA');
                $event->sheet->setCellValue("F".($i+6), 'TC USD');
                $event->sheet->setCellValue("F".($i+7), 'TC EURO');
                $event->sheet->setCellValue("F".($i+8), 'TC LIBRA');
                $event->sheet->setCellValue("F".($i+9), 'Moneda de Conv.');
                $event->sheet->setCellValue("F".($i+10), 'Subtotal Moneda Conv.');
                $event->sheet->setCellValue("F".($i+11), 'Tasa de IVA');
                $event->sheet->setCellValue("F".($i+12), 'IVA');
                $event->sheet->setCellValue("F".($i+13), 'TOTAL');
                $event->sheet->setCellValue("F".($i+14), 'Fecha de Cotizacion');

                $event->sheet
                    ->getStyle('G'.($i+14).":G".($i+14))
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $fechaCotizacion = date_create($this->cotizacion->fecha);
                $event->sheet->setCellValue("G".($i+14), \PhpOffice\PhpSpreadsheet\Shared\Date::dateTimeToExcel($fechaCotizacion));
                $event->sheet->setCellValue("F".($i+15), 'Pago en Parcialdades (%)');
                $event->sheet->setCellValue("G".($i+15), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->parcialidades : 0);
                $event->sheet->setCellValue("F".($i+16), 'Anticipo (%)');
                $event->sheet->setCellValue("G".($i+16), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->anticipo : 0);
                $event->sheet->setCellValue("F".($i+17), 'Credito (días)');
                $event->sheet->setCellValue("G".($i+17), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->dias_credito : 0);
                $event->sheet->setCellValue("F".($i+18), 'Tiempo de Entraga (días)');
                $event->sheet->setCellValue("G".($i+18), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->plazo_entrega : 0);
                $event->sheet->setCellValue("F".($i+19), 'Vigencia (días)');
                $event->sheet->setCellValue("G".($i+19), ($this->cotizacion->complemento) ? $this->cotizacion->complemento->vigencia : 0);
                $event->sheet->setCellValue("F".($i+20), 'Observaciones Generales');
                $event->sheet->setCellValue("G".($i+20), $this->cotizacion->observaciones);
                $event->sheet->setCellValue("G".($i+6), $this->tc_partida_dlls);
                $event->sheet->setCellValue("G".($i+7), $this->tc_partida_euro);
                $event->sheet->setCellValue("G".($i+8), $this->tc_partida_libra);
                $event->sheet->setCellValue("G".($i+9), "PESO MX");

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
        return array([' ',' ',' ',' ',' ',' ',($this->cotizacion->empresa) ? $this->cotizacion->empresa->razon_social : '----- Proveedor Desconocido ----- '],
        ['#','DESCRIPCION','IDENTIFICADOR','UNIDAD','CANTIDAD_SOLICITADA','CANTIDAD_APROBADA','PRECIO_UNITARIO','%_DESCUENTO','PRECIO_TOTAL','MONEDA',
            'PRECIO_TOTAL_MONEDA_CONVERSION','OBSERVACIONES']);
    }
}
