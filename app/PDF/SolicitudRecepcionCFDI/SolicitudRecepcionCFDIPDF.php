<?php

namespace App\PDF\SolicitudRecepcionCFDI;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\Factura ;
use Illuminate\Support\Facades\App;

class SolicitudRecepcionCFDIPDF extends Rotation
{
    protected $solicitud;
    private $encabezado_pdf = '';
    var $encola = '';


    public function __construct($solicitud)
    {
        parent::__construct('P', 'cm', 'Letter');
        $this->solicitud = $solicitud;
        $this->SetFillColor(213,213,213);
        $this->SetMargins(1, 1, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,3.75);
        $this->cfdi();
    }
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(13.5);
        $this->Cell(3, .7, 'FOLIO: ', 'LT', 0, 'L');
        $this->Cell(3, .7, $this->solicitud->numero_folio_format . ' ', 'RT', 0, 'L');
        $this->Ln(.4);
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(13.5, .7, utf8_decode('SOLICITUD DE RECEPCIÓN DE CFDI'), 0, 0, 'C', 0);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(3, .7, 'FECHA: ', 'LB', 0, 'L');
        $this->Cell(3, .7, $this->solicitud->fecha_hora_registro_format . ' ', 'RB', 0, 'L');
        $this->Ln(1);


        $this->SetFont('Arial', 'B', 13);
        $this->Cell(19.5, 0.7, 'Proyecto: '.$this->solicitud->obra->nombre . ' ', 1, 1, 'C');
        $this->Ln(0.5);

        $this->SetFont('Arial', '', 10);
        $this->Cell(19.5, .5, 'Recibimos de:', 0, 0, 'L');
        $this->Ln(0.5);
        $this->MultiCell(19.5, .5, $this->solicitud->proveedor->razon_social , 1, 'J',  0);
        $this->Ln(0.5);

        $this->Cell(19.5, .5, utf8_decode('El siguiente CFDI para validar que esta relacionado con el proyecto indicado: '), 0, 0);
        $this->Ln(0.5);


    }
    function cfdi(){

        /*$this->SetFont('Arial', '', 9);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths(array(0.60,  4.45, 3.15, 3.15, 5, 3.15));
        $this->SetStyles(array('DF', 'DF', 'DF', 'FD', 'DF'));
        $this->SetRounds(array('1', '', '', '', '', '2'));
        $this->SetRadius(array(0.2, 0, 0,  0, 0, 0.2));
        $this->SetFills(array('180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
        $this->Row(array("#",  utf8_decode("Número"), "Fecha", "Vencimiento", "Importe", "Moneda"));

        $this->SetWidths(array(0.60, 4.45, 3.15, 3.15, 5, 3.15));
        $this->SetRounds(array('4', '', '', '', '', '3'));
        $this->SetRadius(array(0.2, 0, 0, 0, 0, 0.2));
        $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
        $this->SetAligns(array('C', 'L', 'C', 'C', 'R', 'C'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));

        $this->Row(array(1, $this->factura->referencia, $this->factura->fecha_format, $this->factura->vencimiento_format, $this->factura->monto_format, $this->factura->moneda->nombre));
*/
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(5, 0.7, utf8_decode('Emisión:'), 1, 0, 'C',true);
        $this->Cell(3, 0.7, 'Serie:', 1, 0, 'C',true);
        $this->Cell(3, 0.7, 'Folio:', 1, 0, 'C',true);
        $this->Cell(1, 0.7, 'Tipo:', 1, 0, 'C',true);
        $this->Cell(7.5, 0.7, 'UUID:', 1, 1, 'C',true);

        $this->SetFont('Arial', '', 10);
        $this->Cell(5, 0.7, $this->solicitud->cfdi->fecha_format . ' ', 1, 0, 'L');
        $this->Cell(3, 0.7, $this->solicitud->cfdi->serie . ' ', 1, 0, 'L');
        $this->Cell(3, 0.7, $this->solicitud->cfdi->folio . ' ', 1, 0, 'L');
        $this->Cell(1, 0.7, $this->solicitud->cfdi->tipo_comprobante . ' ', 1, 0, 'L');
        $this->Cell(7.5,0.7, $this->solicitud->cfdi->uuid . ' ', 1, 1, 'L');
        $this->Ln(.5);

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(3, 0.7, utf8_decode('Descuento'), 1, 0, 'C',true);
        $this->Cell(3, 0.7, 'Impuestos Ret.', 1, 0, 'C',true);
        $this->Cell(3, 0.7, 'Impuestos Tras.', 1, 0, 'C',true);
        $this->Cell(4.5, 0.7, 'Total', 1, 0, 'C',true);
        $this->Cell(3, 0.7, 'Moneda', 1, 0, 'C',true);
        $this->Cell(3, 0.7, 'Tipo de Cambio', 1, 1, 'C',true);

        $this->SetFont('Arial', '', 10);
        $this->Cell(3, 0.7, $this->solicitud->cfdi->descuento_format . ' ', 1, 0, 'R');
        $this->Cell(3, 0.7, $this->solicitud->cfdi->total_impuestos_retenidos_format . ' ', 1, 0, 'R');
        $this->Cell(3, 0.7, $this->solicitud->cfdi->total_impuestos_trasladados_format . ' ', 1, 0, 'R');
        $this->Cell(4.5, 0.7, $this->solicitud->cfdi->total_format . ' ', 1, 0, 'R');
        $this->Cell(3, 0.7, $this->solicitud->cfdi->moneda . ' ', 1, 0, 'L');
        $this->Cell(3,0.7, $this->solicitud->cfdi->tipo_cambio . ' ', 1, 1, 'R');
        $this->Ln(.5);

        $this->SetFont('Arial', 'B', 10);

        $this->Cell(9.75, 0.7, 'Contacto HI: ', 1, 0, 'C',true);
        $this->Cell(9.75, 0.7, 'Correo para recibir notificaciones: ', 1, 1, 'C', true);
        $this->Cell(9.75, 0.7, $this->solicitud->contacto . ' ', 1, 0, 'L');
        $this->Cell(9.75, 0.7, $this->solicitud->correo_notificaciones . ' ', 1, 1, 'L');

        $this->Ln(0.5);

        $this->SetFont('Arial', '', 10);
        $this->CellFitScale(19.5, .5, 'Observaciones:', 0, 0, 'L');
        $this->Ln(.5);
        $this->MultiCell(19.5, .5, $this->solicitud->comentario . ' ', 1, 'J',  0);
        $this->Ln(.5);
        $this->SetFont('Arial', 'B', 15);
        $this->CellFitScale(19.5, .5, utf8_decode('El presente CFDI se toma a validación SIN originar obligación alguna para pago'), 0, 0, 'L');
    }

    function Footer(){
        if (!App::environment('production')) {
            $this->SetFont('Arial','B',80);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(5,20,utf8_decode("MUESTRA"),45);
            $this->RotatedText(6,26,utf8_decode("SIN VALOR"),45);
            $this->SetTextColor('0,0,0');
        }
        $this->SetY(-3.8);
        $this->SetY(-1);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(15, .4, utf8_decode('Formato generado desde el sistema de entrega de CFDI. Fecha de registro: '
            .$this->solicitud->fecha_hora_registro_format)
            .' Fecha de consulta: '.date("d-m-Y h:i:s")
            , 0, 0, 'L');

        $this->SetFont('Arial', 'B', 6);
        $this->SetTextColor('100,100,100');
        $this->Cell(4.5, .4, utf8_decode('Sistema de Entrega de CFDI'), 0, 0, 'R');

        $this->SetY(-0.7);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(19.5, .5, utf8_decode('Página') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create() {
        try {
            $this->Output('I', 'Formato - Solicitud Recepción CFDI.pdf', 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }
}
