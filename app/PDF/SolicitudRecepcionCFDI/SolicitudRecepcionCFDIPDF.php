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
        $this->SetAutoPageBreak(true,1);
        $this->cfdi();
    }
    function Header()
    {

        $this->setY(1.2);
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(13, .7, utf8_decode('SOLICITUD DE RECEPCIÓN DE CFDI'), 0, 1, 'C', 0);

        $this->setY(1);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(13);
        $this->Cell(3.5, .5, 'FOLIO: ', 'LT', 0, 'L');
        $this->Cell(3, .5, $this->solicitud->numero_folio_format . ' ', 'RT', 1, 'L');

        $this->Cell(13);
        $this->Cell(3.5, .5, 'FECHA: ', 'LB', 0, 'L');
        $this->Cell(3, .5, $this->solicitud->fecha_hora_registro_format . ' ', 'RB', 0, 'L');
        $this->Ln(1);

        $this->SetFont('Arial', 'B', 13);
        $this->Cell(19.5, 0.7, $this->solicitud->obra->descripcion . ' ', 1, 1, 'C');
        $this->Ln(0.5);
    }
    function cfdi(){

        $this->SetFont('Arial', '', 10);
        $this->Cell(19.5, .5, 'Recibimos de:', 0, 0, 'L');
        $this->Ln(0.5);
        $this->MultiCell(19.5, .5, $this->solicitud->proveedor->razon_social , 1, 'J',  0);
        $this->Ln(0.5);

        $this->Cell(19.5, .5, utf8_decode('El siguiente CFDI para revisar que esta relacionado con una transacción del proyecto indicado: '), 0, 0);
        $this->Ln(1);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(5, 0.5, utf8_decode('Emisión'), 1, 0, 'C',true);
        $this->Cell(3, 0.5, 'Serie', 1, 0, 'C',true);
        $this->Cell(3, 0.5, 'Folio', 1, 0, 'C',true);
        $this->Cell(1, 0.5, 'Tipo', 1, 0, 'C',true);
        $this->Cell(7.5, 0.5, 'UUID', 1, 1, 'C',true);

        $this->SetFont('Arial', '', 8);
        $this->Cell(5, 0.5, $this->solicitud->cfdi->fecha_format . ' ', 1, 0, 'L');
        $this->Cell(3, 0.5, $this->solicitud->cfdi->serie . ' ', 1, 0, 'L');
        $this->Cell(3, 0.5, $this->solicitud->cfdi->folio . ' ', 1, 0, 'L');
        $this->Cell(1, 0.5, $this->solicitud->cfdi->tipo_comprobante . ' ', 1, 0, 'L');
        $this->Cell(7.5,0.5, $this->solicitud->cfdi->uuid . ' ', 1, 1, 'L');
        $this->Ln(.5);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(16.5, 0.5, 'Empresa', 1, 0, 'C',true);
        $this->Cell(3, 0.5, 'RFC', 1, 1, 'C',true);

        $this->SetFont('Arial', '', 8);
        $this->CellFitScale(16.5, 0.5, utf8_decode($this->solicitud->cfdi->empresa->razon_social) . ' ', 1, 0, 'L');
        $this->Cell(3, 0.5, $this->solicitud->cfdi->empresa->rfc . ' ', 1, 1, 'L');
        $this->Ln(.5);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(3, 0.5, 'Descuento', 1, 0, 'C',true);
        $this->Cell(3, 0.5, 'Impuestos Ret.', 1, 0, 'C',true);
        $this->Cell(3, 0.5, 'Impuestos Tras.', 1, 0, 'C',true);
        $this->Cell(4.5, 0.5, 'Total', 1, 0, 'C',true);
        $this->Cell(3, 0.5, 'Moneda', 1, 0, 'C',true);
        $this->Cell(3, 0.5, 'Tipo de Cambio', 1, 1, 'C',true);

        $this->SetFont('Arial', '', 8);
        $this->Cell(3, 0.5, $this->solicitud->cfdi->descuento_format . ' ', 1, 0, 'R');
        $this->Cell(3, 0.5, $this->solicitud->cfdi->total_impuestos_retenidos_format . ' ', 1, 0, 'R');
        $this->Cell(3, 0.5, $this->solicitud->cfdi->total_impuestos_trasladados_format . ' ', 1, 0, 'R');
        $this->Cell(4.5, 0.5, $this->solicitud->cfdi->total_format . ' ', 1, 0, 'R');
        $this->Cell(3, 0.5, $this->solicitud->cfdi->moneda . ' ', 1, 0, 'L');
        $this->Cell(3,0.5, $this->solicitud->cfdi->tipo_cambio . ' ', 1, 1, 'R');
        $this->Ln(.5);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(9.75, 0.5, 'Contacto HI: ', 1, 0, 'C',true);
        $this->Cell(9.75, 0.5, 'Correo para recibir notificaciones: ', 1, 1, 'C', true);
        $this->SetFont('Arial', '', 8);
        $this->Cell(9.75, 0.5, $this->solicitud->contacto . ' ', 1, 0, 'L');
        $this->Cell(9.75, 0.5, $this->solicitud->correo_notificaciones . ' ', 1, 1, 'L');
        $this->Ln(0.5);

        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(213, 213, 213);
        $this->SetWidths(array(0.60,  1.5, 6.5, 1.5,1.5, 2,2, 2, 2));
        $this->SetStyles(array('DF', 'DF', 'DF', 'FD','DF', 'DF', 'FD', 'DF'));
        $this->SetRounds(array('1', '', '', '', '','', '', '', '2'));
        $this->SetRadius(array(0.2, 0, 0,  0, 0,0,  0, 0, 0.2));
        $this->SetFills(array('180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180','180,180,180', '180,180,180', '180,180,180'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetAligns(array('C','C','C','C','C','C','C','C','C'));
        $this->Row(array("#",  "Clave Producto / Servicio", utf8_decode("Descripción"), "Clave Unidad", "Unidad", "Cantidad", "Valor Unitario", "Descuento", "Importe"));

        $this->SetFont('Arial', '', 8);
        $this->SetWidths(array(0.60, 1.5, 6.5, 1.5, 1.5, 2, 2, 2, 2));
        $this->SetRounds(array('4', '', '', '', '','', '', '', '3'));
        $this->SetRadius(array(0.2, 0, 0, 0, 0,0, 0, 0, 0.2));
        $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
        $this->SetAligns(array('C', 'L', 'L', 'L', 'L','R', 'R', 'R', 'R'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0','0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));

        $i = 1;
        foreach($this->solicitud->cfdi->conceptos as $partida)
        {
            $this->SetRounds(array('', '', '', '', '','', '', '', ''));
            if($i == count($this->solicitud->cfdi->conceptos))
            {
                $this->SetRounds(array('4', '', '', '', '','', '', '', '3'));
            }
            $this->Row(
                array($i, $partida->clave_prod_serv, $partida->descripcion, $partida->clave_unidad, $partida->unidad, $partida->cantidad_format,
                    $partida->valor_unitario_format, $partida->descuento_format, $partida->importe_format)
            );

            $i++;
        }

        $this->Ln(0.5);
        $this->SetFont('Arial', '', 8);
        $this->CellFitScale(19.5, .5, 'Observaciones:', 0, 0, 'L');
        $this->Ln(.5);
        $this->MultiCell(19.5, .5, utf8_decode($this->solicitud->comentario) . ' ', 1, 'J',  0);
        $this->Ln(.5);
        $this->SetFont('Arial', 'B', 15);
        $this->CellFitScale(19.5, .5, utf8_decode('El presente CFDI se toma a revisión SIN originar obligación alguna para pago'), 0, 0, 'L');
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
