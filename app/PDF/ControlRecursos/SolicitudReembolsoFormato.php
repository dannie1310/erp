<?php

namespace App\PDF\ControlRecursos;

use App\Models\CONTROL_RECURSOS\SolCheque;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;

class SolicitudReembolsoFormato extends Rotation
{
    private $encabezado_pdf = '';
    private $sol = null;
    private $deducibles = null;
    private $no_deducibles = null;
    var $encola = "";
    const vy = 0;

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(SolCheque $solCheque)
    {
        parent::__construct('P', 'cm', 'Letter');
        $this->sol = $solCheque;
        $this->WidthTotal = $this->GetPageWidth() - 0.82;
        $this->encola = '';
    }

    function Header()
    {
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetFillColor(204, 204, 204);

        $this->Image(public_path('/img/logo_hc.png'), 1, .5, 2.7, 1.5);

        $this->Cell(9.5, .25, ' ', 0, 0, 'C');
        $this->SetTextColor(90, 90, 90);
        $this->SetFont('Arial', 'I', 7);

        $this->Cell(10, .25, '', 0, 1, 'C', 0);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(13.5, .25, ' ', 0, 0, 'C');
        $this->Cell(3.5, .25, 'SERIE', 1, 0, 'C', 1);
        $this->Cell(3.5, .25, 'FOLIO', 1, 1, 'C', 1);

        $this->Cell(3, .5, ' ', 0, 0, 'C');

        $this->SetFont('Arial', 'BU', 12);

        if ($this->sol->Estatus == 10 && $this->sol->IdTipoPago == 73)
            $this->Cell(10.5, .5, ' SOLICITUD DE PAGO A PROVEEDOR ', 0, 0, 'C');

        else if (($this->sol->Estatus == 30) && $this->sol->IdTipoPago == 6)
           $this->Cell(10.5, .5, ' SOLICITUD DE REEMBOLSO DE GASTOS', 0, 0, 'C');

        else
            $this->Cell(10.5, .5, utf8_decode(' SOLICITUD DE REPOSICIÓN CAJA CHICA'), 0, 0, 'C');

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(3.5, .6, $this->sol->Serie, 1, 0, 'C');
        $this->Cell(3.5, .6, $this->sol->Folio, 1, 0, 'C');
        $this->Cell(20.5, .7, ' ', 0, 1, 'C');

        $this->tableHeader();
         if($this->encola == "documentos")
         {
             $this->tableHeader();
         }

         $currentPage = $this->PageNo();
         if($currentPage>1){
             $this->Ln();
         }
    }

    public function tableHeader()
    {

        $this->separacion = 10.4;
        /* if ($v["IVA"] == 0) $this->separacion += 2;
         if ($v["Retenciones"] == 0) $this->separacion += 2;
         if ($v["OtrosImpuestos"] == 0) $this->separacion += 2;
         if ($v["Total"] == 0) $this->separacion += 2.5;
         if ($v["Importe"] == 0) $this->separacion += 2.5;*/

        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor(204, 204, 204);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(20.5, .5, $this->sol->empresa->RazonSocial, 1, 1, 'C', 1);

        $this->Cell(20.5, .2, ' ', 0, 1, 'C');

        $this->SetFont('Arial', 'B', 7);

        $this->Cell(1.8, .3, 'FECHA', 1, 0, 'C', 1);
        $this->CellFitScale(2, .3, 'FORMA DE PAGO', 1, 0, 'C', 1);
        $this->CellFitScale(1.8, .3, 'VENCIMIENTO', 1, 0, 'C', 1);
        if (($this->sol->IdTipoPago == 73 || $this->sol->IdTipoPago == 6 || $this->sol->IdTipoPago == 5) && $this->sol != "") {
            $this->Cell(1.5, .3, utf8_decode('RELACIÓN'), 1, 0, 'C', 1);
            $this->Cell($this->separacion - 1.5, .3, ' ', 0, 0, 'C');
        } else {
            $this->Cell($this->separacion, .3, ' ', 0, 0, 'C');
        }

        /*
        if ($this->sol->Importe != '0.00') {
            $this->Cell(2.7, .3, 'IMPORTE', 1, 0, 'C', 1);
        }

        if ($this->sol->IVA != '0.00') {
            $this->Cell(2, .3, 'IVA', 1, 0, 'C', 1);
        }

        if ($this->sol->Retenciones != '0.00') {
            $this->Cell(2, .3, 'RETENCIONES', 1, 0, 'C', 1);
        }

        if ($this->sol->OtrosImpuestos != '0.00') {
            $this->Cell(2, .3, 'OTROS IMP.', 1, 0, 'C', 1);
        }
        */
        if ($this->sol->Total != '0.00') {
            $this->Cell(2.7, .3, 'TOTAL', 1, 0, 'C', 1);
        }

        $this->Cell(1.8, .3, 'MONEDA', 1, 1, 'C', 1);

        $this->SetFont('Arial', '', 7);
        $this->Cell(1.8, .3, $this->sol->fecha_format, 1, 0, 'C');
        $this->Cell(2, .3, $this->sol->formaPago->Nombre, 1, 0, 'C');
        $this->Cell(1.8, .3, $this->sol->fecha_vencimiento_format, 1, 0, 'C');

        if (($this->sol->IdTipoPago == 73 || $this->sol->IdTipoPago == 6 || $this->sol->IdTipoPago == 5) && $this->sol != "") {
            $this->Cell(1.5, .3, $this->sol->relacion->folio, 1, 0, 'C');
            $this->Cell($this->separacion - 1.5, .3, ' ', 0, 0, 'C');
        } else {
            $this->Cell($this->separacion, .3, ' ', 0, 0, 'C');
        }

        /*
        if ($this->sol->Importe != '0.00') {
            $this->Cell(2.7, .3, $this->sol->importe_format, 1, 0, 'C');
        }

        if ($this->sol->IVA != '0.00') {
            $this->Cell(2, .3, $this->sol->iva_format, 1, 0, 'C');
        }

        if ($this->sol->Retenciones != '0.00') {
            $this->Cell(2, .3, $this->sol->retenciones_format, 1, 0, 'C');
        }

        if ($this->sol->OtrosImpuestos != '0.00') {
            $this->Cell(2, .3, $this->sol->otros_format, 1, 0, 'C');
        }
        */

        if ($this->sol->Total != '0.00') {
            $this->Cell(2.7, .3, $this->sol->total_format, 1, 0, 'C');
        }

        $this->CellFitScale(1.8, .3, $this->sol->moneda->moneda, 1, 1, 'C');

        $this->SetFont('Arial', '', 9);

        $this->Cell(20.5, .2, ' ', 0, 1, 'C');
        $this->Cell(20.5, .4, 'A FAVOR DE', 1, 1, 'C', 1);

        $this->SetFont('Arial', 'B', 9);
        $this->MultiCell(20.5, .4, utf8_decode($this->sol->proveedor->RazonSocial), 1, 'C', 0);

        $this->SetFont('Arial', '', 7);

        $this->Cell(20.5, .3, ' ', 0, 1, 'C');
        $this->Cell(20.5, .3, 'CONCEPTO DEL PAGO', 1, 1, 'C', 1);

        $this->SetFont('Arial', 'B', 8);

        $this->MultiCell(20.5, .4, utf8_decode($this->sol->Concepto), 1, 'J');

        $this->SetFont('Arial', 'B', 8);

        $this->Cell(20.5, .4, ' ', 0, 1, 'C');
        $this->Cell(7, .4, 'TIPO DE PAGO', 1, 0, 'C', 1);
        $this->Cell(5.5, .4, 'INTRUCCIONES DE ENTREGA', 1, 0, 'C', 1);
        $this->Cell(8, .4, 'CUENTA BANCARIA', 1, 1, 'C', 1);

        $this->SetFont('Arial', '', 8);

        $this->Cell(7, .4, utf8_decode($this->sol->tipoPago->Descripcion), 1, 0, 'C');
        $this->Cell(5.5, .4,  utf8_decode($this->sol->entrega->Descripcion), 1, 0, 'C');
        $this->Cell(8, .4, $this->sol->cuentaProveedor != null ? $this->sol->cuentaProveedor->descripcion_banco : 'NO REGISTRADA', 1, 1, 'C');


        $this->SetFont('Arial', 'B', 10);

        $this->Cell(20.5, .4, ' ', 0, 1, 'L');
        $this->Cell(20.5, .4, utf8_decode('APLICACIÓN DE SEGMENTOS DE NEGOCIO (Debe ser Llenada por el Solicitante)'), 0, 1, 'L');
    }

    public function create()
    {
        $this->SetMargins(0.4, 0.5, 0.1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,1);

        $this->partidas();
        try {
            $this->Output('I', "Formato - Solicitud_".$this->sol->folio.".pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

    public function partidas()
    {

        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(204, 204, 204);
        $w = 8.8;
        /*if ($this->sol->IVA == 0.00) $w += 1;
        if ($this->sol->Retenciones == 0.00) $w += 1;
        if ($this->sol->Importe == 0.00) $w += 1;
        if ($this->sol->OtrosImpuestos == 0.00) $w += 1;*/

        $this->Cell($w + 0.7, .3, 'SEGMENTO DE NEGOCIO', 1, 0, 'C', 1);
        $this->Cell($w - 0.7, .3, 'TIPO DE GASTO', 1, 0, 'C', 1);

        /*if ($this->sol->Importe != 0.00)
            $this->Cell(2.2, .3, 'IMPORTE', 1, 0, 'C', 1);
        if ($this->sol->IVA != 0.00)
            $this->Cell(2.2, .3, 'IVA', 1, 0, 'C', 1);
        if ($this->sol->Retenciones != 0.00)
            $this->Cell(2.2, .3, 'RETENCIONES', 1, 0, 'C', 1);
        if ($this->sol->OtrosImpuestos != 0.00)
            $this->Cell(2.2, .3, 'OTROS IMP.', 1, 0, 'C', 1);
*/
        $this->Cell(3, .3, 'TOTAL', 1, 1, 'C', 1);

        $this->SetFont('Arial', '', 7);
        $conter = 1;
        foreach ($this->sol->partidas_para_pdf as $partida)
        {
            $this->CellFitScale(0.7, .3, $partida->NS, 1, 0, 'C');
            $this->CellFitScale(0.7, .3, $partida->Facturable, 1, 0, 'C');
            if ($partida->TotalCC == 1) {
                $this->CellFitScale($w - 0.7, .3, $partida->SN, 1, 0, 'L');
            } else {
                $this->CellFitScale($w - 0.7, .3, $partida->SN . " (" . $partida->TotalCC . ")", 1, 0, 'L');
            }

            $this->CellFitScale($w - 0.7, .3, $partida->TG, 1, 0, 'L');
            /*
            if ($this->sol->Importe != 0.00)
                $this->Cell(2.2, .3, $partida->ImporteSN, 1, 0, 'R');
            if ($this->sol->IVA != 0.00)
                $this->Cell(2.2, .3,$partida->IVASN, 1, 0, 'R');
            if ($this->sol->Retenciones != 0.00)
                $this->Cell(2.2, .3, $partida->RetencionesSN, 1, 0, 'R');
            if ($this->sol->OtrosImpuestos != 0.00)
                $this->Cell(2.2, .3, $partida->OtrosImpuestos, 1, 0, 'R');
            */

            $this->Cell(3, .3, $partida->TotalSN, 1, 1, 'R');
            $conter = $conter + 1;
        }

        $resto = 50 - $conter;

        for ($b = 1; $b < $resto; $b++) {
            $this->Cell(0.7, .3, ' ', 1, 0, 'C');
            $this->Cell(0.7, .3, ' ', 1, 0, 'C');
            $this->Cell($w - 0.7, .3, ' ', 1, 0, 'L');
            $this->Cell($w - 0.7, .3, ' ', 1, 0, 'L');
           /*
            if ($this->sol->Importe != 0.00)
                $this->Cell(2.2, .3, ' ', 1, 0, 'R');
            if ($this->sol->IVA != 0.00)
                $this->Cell(2.2, .3, ' ', 1, 0, 'R');
            if ($this->sol->Retenciones != 0.00)
                $this->Cell(2.2, .3, ' ', 1, 0, 'R');
            if ($this->sol->OtrosImpuestos != 0.00)
                $this->Cell(2.2, .3, ' ', 1, 0, 'R');
            */
            $this->Cell(3, .3, ' ', 1, 1, 'R');
        }

        $this->SetFont('Arial', 'B', 8);
        $this->Cell($w, .3, '', 0, 0, 'L');
        $this->Cell($w, .3, "SUMAS:", 0, 0, 'R');
        /*
        if ($this->sol->Importe != 0.00)
            $this->Cell(2.2, .3, number_format($this->sol->Importe,3), 1, 0, 'R', 1);
        if ($this->sol->IVA != 0.00)
            $this->Cell(2.2, .3, number_format($this->sol->IVA,3), 1, 0, 'R', 1);
        if ($this->sol->Retenciones != 0.00)
            $this->Cell(2.2, .3, number_format($this->sol->Retenciones,3), 1, 0, 'R', 1);
        if ($this->sol->OtrosImpuestos != 0.00)
            $this->Cell(2.2, .3, number_format($this->sol->OtrosImpuestos,3), 1, 0, 'R', 1);
        */
        $this->Cell(3, .3, number_format($this->sol->Total,3), 1, 1, 'R', 1);


        $this->SetFont('Arial', 'B', 7);
        $this->Cell(20.5, .3, 'Documento de Soporte Recibido', 0, 1, 'L');
        $this->SetFont('Arial', '', 4);
        $this->Cell(2.3, .3, utf8_decode('REQUISICIÓN DEL USUARIO'), 'LRT', 0, 'C', 1);
        $this->Cell(2.3, .3, 'SOLICITUD DE COMPRA', 'LRT', 0, 'C', 1);
        $this->Cell(2.3, .3, utf8_decode('OFICIO DE AUTORIZACIÓN '), 'LRT', 0, 'C', 1);
        $this->Cell(2.3, .3, 'COMPARATIVA DE ', 'LRT', 0, 'C', 1);
        $this->Cell(2.3, .3, 'ORDEN DE COMPRA', 'LRT', 0, 'C', 1);
        $this->Cell(2.3, .3, utf8_decode('ENTRADA DE ALMACÉN /'), 'LRT', 0, 'C', 1);
        $this->Cell(2.25, .3, 'ORIGINAL', 'LRT', 0, 'C', 1);
        $this->Cell(2.25, .3, 'COPIA', 'LRT', 0, 'C', 1);
        $this->Cell(2.25, .3, 'OTRO', 'LRT', 1, 'C', 1);

        $this->Cell(2.3, .3, '', 'LRB', 0, 'C', 1);
        $this->Cell(2.3, .3, '', 'LRB', 0, 'C', 1);
        $this->Cell(2.3, .3, 'DE COMPRA DE AF', 'LRB', 0, 'C', 1);
        $this->Cell(2.3, .3, 'COTIZACIONES', 'LRB', 0, 'C', 1);
        $this->Cell(2.3, .3, '', 'LRB', 0, 'C', 1);
        $this->Cell(2.3, .3, utf8_decode('RECEPCIÓN DEL SERVICIO'), 'LRB', 0, 'C', 1);
        $this->Cell(2.25, .3, 'FACTURA/COMPROBANTE', 'LRB', 0, 'C', 1);
        $this->Cell(2.25, .3, 'FACTURA/COMPROBANTE', 'LRB', 0, 'C', 1);
        $this->Cell(2.25, .3, '', 'LRB', 1, 'C', 1);


        $this->Cell(1.15, 0.3, 'SI', 1, 0, 'C');
        $this->Cell(1.15, 0.3, 'NO', 1, 0, 'C');

        $this->Cell(1.15, 0.3, 'SI', 1, 0, 'C');
        $this->Cell(1.15, 0.3, 'NO', 1, 0, 'C');

        $this->Cell(1.15, 0.3, 'SI', 1, 0, 'C');
        $this->Cell(1.15, 0.3, 'NO', 1, 0, 'C');

        $this->Cell(1.15, 0.3, 'SI', 1, 0, 'C');
        $this->Cell(1.15, 0.3, 'NO', 1, 0, 'C');

        $this->Cell(1.15, 0.3, 'SI', 1, 0, 'C');
        $this->Cell(1.15, 0.3, 'NO', 1, 0, 'C');

        $this->Cell(1.15, 0.3, 'SI', 1, 0, 'C');
        $this->Cell(1.15, 0.3, 'NO', 1, 0, 'C');

        $this->Cell(1.125, 0.3, 'SI', 1, 0, 'C');
        $this->Cell(1.125, 0.3, 'NO', 1, 0, 'C');

        $this->Cell(1.125, 0.3, 'SI', 1, 0, 'C');
        $this->Cell(1.125, 0.3, 'NO', 1, 0, 'C');

        $this->Cell(1.125, 0.3, 'SI', 1, 0, 'C');
        $this->Cell(1.125, 0.3, 'NO', 1, 1, 'C');

        $this->SetFont('Arial', '', 7);
        $this->SetWidths(array(1.15, 1.15, 1.15, 1.15, 1.15, 1.15, 1.15, 1.15, 1.15, 1.15, 1.15, 1.126, 1.126, 1.126, 1.126, 1.126, 1.126, 1.126));

        if (count($this->sol->uuids) == 0) {
            $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
            $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0',));
        } else {
            $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '0,0,0', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
            $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '255,255,255', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0',));
        }

        $this->SetDrawColor(117, 117, 117);
        $this->SetHeights(array(0.4));
        $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
        $this->Row(array("", "", "", "", "", "", "", "", "", "", "", "", "  ", "", "", "", "", ""));
        $this->SetFont('Arial', '', 5);


        $this->Cell(19.5, .1, ' ', 0, 1, 'C');

        if (count($this->sol->uuids) == 0 && ($this->sol->Estatus == 1 || $this->sol->Estatus == 2 || $this->sol->Estatus == 3 || $this->sol->Estatus == 4 || $this->sol->Estatus == 5 || $this->sol->Estatus == 6 || $this->sol->Estatus == 7 || $this->sol->Estatus == 8 || $this->sol->Estatus == 9 || $this->sol->Estatus == 102 || $this->sol->Estatus == 103 || $this->sol->Estatus == 104 || $this->sol->Estatus == 10 || $this->sol->Estatus == 11 || $this->sol->Estatus == 12 || $this->sol->Estatus == 13 || $this->sol->Estatus == 14 || $this->sol->Estatus == 15 || $this->sol->Estatus == 16 || $this->sol->Estatus == 17 || $this->sol->Estatus == 18 || $this->sol->Estatus == 111 || $this->sol->Estatus == 112 || $this->sol->Estatus == 113 && $this->sol->idtipopago != 73)) {

            $this->SetFont('Arial', '', 10);
            $this->SetTextColor(255, 0, 0);
            $this->SetDrawColor(255, 0, 0);

            $this->Cell(15.5, .5, utf8_decode('SE REQUIERE AUTORIZACIÓN ADICIONAL PARA DAR TRÁMITE AL PAGO SIN CFDI:'), 0, 0, 'L');

            $y = $this->getY();
            $x = $this->getX();

            $this->setY($y);
            $this->setX($x);

            $this->SetFont('Arial', '', 5);
            $this->CellFitScale(4, .3, "AUTORIZA", 1, 2, 'C', 1);
            $this->Cell(4, .7, '', 1, 2, 'C');
            $this->SetFont('Arial', '', 5);
            $this->CellFitScale(4, .3, utf8_decode("C.P. PRIMITIVO AYALA SÁNCHEZ"), 1, 2, 'C', 1);
        }


    }

    function Footer()
    {
        if (!App::environment('production')) {
            $this->SetFont('Arial', 'B', 90);
            $this->SetTextColor(155, 155, 155);
            $this->RotatedText(3, 15, utf8_decode("MUESTRA"), 45);
            $this->RotatedText(6, 20, utf8_decode("SIN VALOR"), 45);
            $this->SetTextColor('0,0,0');
        }


        $this->SetY(-2.8);
        $this->SetFont('Arial', 'BI', 7);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);


        $this->Cell(19.5, .4, utf8_decode('LAS FIRMAS REQUERIDAS, ESTÁN EN FUNCIÓN DEL IMPORTE Y TIPO DE GASTO DE LA SOLICITUD '), 0, 1, 'C');
        $this->Cell(19.5, .2, ' ', 0, 1, 'C');
        $this->SetFont('Arial', '', 6);

        $no_firmas = count($this->sol->firmasSolicitantes);
        if ($no_firmas > 0) {
            if ($no_firmas >= 4) {
                $blancos = false;
                $ancho = 19.6 / $no_firmas;
            } else {
                $blancos = true;
                $ancho = 4.9;
                $ancho_blancos = (19.6 - ($ancho * $no_firmas)) / 2;
            }
            if ($blancos) {
                $this->Cell($ancho_blancos, 1, ' ', 0, 0, 'C');
            }
            $y = $this->getY();
            $x = $this->getX();
           foreach ($this->sol->firmasSolicitantes as $solicitante)
           {
                $this->setY($y);
                $this->setX($x);
                $this->SetFont('Arial', '', 6);
                $this->CellFitScale($ancho, .3, utf8_decode($solicitante->encabezado->descripcion), 1, 2, 'C', 1);
                $this->Cell($ancho, 1, '', 1, 2, 'C');
                $this->SetFont('Arial', '', 5);
                $this->CellFitScale($ancho, .3, utf8_decode($solicitante->firmante->descripcion), 1, 2, 'C', 1);
                $x += $ancho;
           }
        }

        $this->SetY(-0.5);
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(3, .3, 'V.15012024', 0, 0, 'L');
        $this->Cell(10, .3, utf8_decode('Formato generado desde el sistema de control de recursos. Fecha de registro: ' . date("d-m-Y", strtotime($this->sol->timestampRegistro))).' Fecha de consulta: '.date("d-m-Y H:i:s").'  Estado: '.$this->sol->descripcion_estado, 0, 0, 'L');
        $this->SetXY(22.6,-0.9);
        $this->Cell(5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }
}
