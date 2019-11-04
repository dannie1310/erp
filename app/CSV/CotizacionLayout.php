<?php

namespace App\CSV;

use App\Models\CADECO\CotizacionCompra;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class CotizacionLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $cotizacion;

    public function __construct(CotizacionCompra $cotizacion)
    {
        $this->cotizacion = $cotizacion;
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
                $event->sheet->protectCells('G3:H3', $this->cotizacion->numero_folio);
                $event->sheet->protectCells('G4', $this->cotizacion->numero_folio);
                $event->sheet->protectCells('G14:G20', $this->cotizacion->numero_folio);
                $event->sheet->protectCells('L3', $this->cotizacion->numero_folio);
                $objValidation = $event->sheet->getCell('J3')->getDataValidation();
                $objValidation->setFormula1('php');

            },
        ];
    }

    public function getFile(){
        $user = array();
        foreach ($this->cotizacion->cotizaciones as $cot){
            $folio = str_pad($cot->folio,6,0,0);
            $user[]=array(
                $this->cotizacion->folio_format. ' '.chunk_split($folio, 3, ' '),
                $cot->id_transaccion,
            );

        }

        return $user;
    }

    public function headings(): array
    {
        $event = new AfterSheet();
        $objValidation = $event->sheet->getCell('J3')->getDataValidation();
        $objValidation->setFormula1('php');

        return array([' ',' ',' ',' ',' ',' ',$this->cotizacion->empresa->razon_social],
        ['#','DESCRIPCION','IDENTIFICADOR','UNIDAD','CANTIDAD_SOLICITADA','CANTIDAD_APROBADA','Precio Unitario','% Descuento','Precio Total','Moneda',
            'Precion Total Moneda Conversión','Observaciones'],
         ['1','1','1','1','1','1','1','1','=G3*E3-((G3*H3*H3)/100)',$objValidation],
        [' ',' ',' ',' ',' ','%Descuento'],[' ',' ',' ',' ',' ','Subtotal Precios Peso (MXP)'],[' ',' ',' ',' ',' ','%Subtotal Precios Dolar (USD)'],[' ',' ',' ',' ',' ','Subtotal Precios EURO'],[' ',' ',' ',' ',' ','TC USD'],
        [' ',' ',' ',' ',' ','TC EURO'],[' ',' ',' ',' ',' ','Moneda de Conv.'],[' ',' ',' ',' ',' ','%Subtotal Moneda Conv.'],[' ',' ',' ',' ',' ','IVA'],[' ',' ',' ',' ',' ','Total'],
        [' ',' ',' ',' ',' ','Fecha de Cotizacion',date("d/m/Y")],[' ',' ',' ',' ',' ','Pago en Parcialdades (%)'],[' ',' ',' ',' ',' ','% Anticipo'],[' ',' ',' ',' ',' ','Credito (dias)'],[' ',' ',' ',' ',' ','Tiempo de Entraga (dias)'],
        [' ',' ',' ',' ',' ','Vigencia (dias)'],[' ',' ',' ',' ',' ','Observaciones Generales']

        );
//        [$this->panda()['Precio Unitario'],$this->panda()['% Descuento']],[$this->getFile()]);
    }
}