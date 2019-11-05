<?php

namespace App\CSV;

use App\Models\CADECO\CotizacionCompra;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class CotizacionLayout implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $cotizacion;
    protected $evento;

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
                $event->sheet->protectCells('G3:J3', $this->cotizacion->numero_folio);
                $event->sheet->protectCells('G4', $this->cotizacion->numero_folio);
                $event->sheet->protectCells('G14:G20', $this->cotizacion->numero_folio);
                $event->sheet->protectCells('L3', $this->cotizacion->numero_folio);
                $event->sheet->protectCells('G10', $this->cotizacion->numero_folio);
                $event->sheet->protectCells('K3', $this->cotizacion->numero_folio);
                $event->sheet->SetCellValue("A1", "Size");

                //MONEDAS
                $objValidation = $event->sheet->getCell('J3')->getDataValidation();
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

                //PESOS
                $objValidation = $event->sheet->getCell('G10')->getDataValidation();
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

    public function getFormula(){
        return $formula = 'SI(J3="EURO";I3*22.8097/1;SI(J3="DOLAR USD";I3*18.6361/1;SI(J3="PESO MXP";I3/1;SI(J3="Moneda";0))))';
    }

    public function headings(): array
    {
//        $objValidation = $this->evento->getCell('J3')->getDataValidation();
//        $objValidation =['php','u'];

        return array([' ',' ',' ',' ',' ',' ',$this->cotizacion->empresa->razon_social],
        ['#','DESCRIPCION','IDENTIFICADOR','UNIDAD','CANTIDAD_SOLICITADA','CANTIDAD_APROBADA','Precio Unitario','% Descuento','Precio Total','Moneda',
            'Precio Total Moneda Conversión','Observaciones'],
         ['1','1','1','1','1','1','1','1','=G3*E3-((G3*H3*H3)/100)','Moneda',$this->getFormula()],
        [' ',' ',' ',' ',' ','%Descuento'],[' ',' ',' ',' ',' ','Subtotal Precios Peso (MXP)'],[' ',' ',' ',' ',' ','%Subtotal Precios Dolar (USD)'],[' ',' ',' ',' ',' ','Subtotal Precios EURO'],[' ',' ',' ',' ',' ','TC USD'],
        [' ',' ',' ',' ',' ','TC EURO'],[' ',' ',' ',' ',' ','Moneda de Conv.'],[' ',' ',' ',' ',' ','%Subtotal Moneda Conv.'],[' ',' ',' ',' ',' ','IVA'],[' ',' ',' ',' ',' ','Total'],
        [' ',' ',' ',' ',' ','Fecha de Cotizacion',date("d/m/Y")],[' ',' ',' ',' ',' ','Pago en Parcialdades (%)'],[' ',' ',' ',' ',' ','% Anticipo'],[' ',' ',' ',' ',' ','Credito (dias)'],[' ',' ',' ',' ',' ','Tiempo de Entraga (dias)'],
        [' ',' ',' ',' ',' ','Vigencia (dias)'],[' ',' ',' ',' ',' ','Observaciones Generales']

        );
//        [$this->panda()['Precio Unitario'],$this->panda()['% Descuento']],[$this->getFile()]);
    }
}