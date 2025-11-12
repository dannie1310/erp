<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/07/2020
 * Time: 04:56 PM
 */

namespace App\PDF\Fiscal;


use Ghidev\Fpdf\Rotation;

class CFDI extends Rotation
{
    protected $factura;
    protected $etiqueta_titulo;

    const DPI = 96;
    const MAX_WIDTH = 180;
    const MAX_HEIGHT = 150;
    const MM_IN_INCH = 25.4;

    private $en_cola = '';
    private $IEPS = 0;

    public function __construct($factura)
    {
        parent::__construct("P", "cm", "Letter");
        $this->factura = $factura;
    }

    function Header()
    {
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Helvetica', '', 9);



        $this->SetWidths([9.25,1.2,9.25]);
        $this->SetStyles(['DF','DF','DF']);
        $this->SetRounds(['','','']);

        $this->SetHeights([0.5]);
        $this->SetAligns(['C','','C']);
        $this->SetFills(['117,117,117','255,255,255','117,117,117']);
        $this->SetTextColors(['255,255,255','255,255,255','255,255,255']);
        $this->SetDrawColor(255,255,255);
        $this->Row([
            'EMISOR'
            , ''
            , 'RECEPTOR'
        ]);
        $this->SetHeights([0.4]);
        $this->SetFills(['255,255,255','255,255,255','255,255,255']);
        $this->SetTextColors(['0,0,0','0,0,0','0,0,0']);

        $this->Row([
            utf8_decode($this->factura["emisor"]["rfc"]."-".$this->factura["emisor"]["razon_social"])
            , ''
            , utf8_decode($this->factura["receptor"]["rfc"]."-".$this->factura["receptor"]["razon_social"])
        ]);

        $this->SetFont('Helvetica', '', 8);

        $this->Cell(2,0.4,"Folio Fiscal: ");
        $this->Cell(7.25,0.4,$this->factura["uuid"],0,0);
        $this->Cell(1.2,0.4,"");
        $this->Cell(4,0.4,utf8_decode("Fecha y Hora de Emisión:"));
        $this->Cell(5.25,0.4,$this->factura["fecha"]->format("d/m/Y H:i:s"),0,1,"R");

        $this->Cell(2,0.4,"Serie y Folio: ");
        $this->Cell(7.25,0.4,$this->factura["serie"].$this->factura["folio"],0,0);
        $this->Cell(1.2,0.4,"");
        $this->Cell(4,0.4,utf8_decode("Tipo de Comprobante:"));
        $this->Cell(5.25,0.4,$this->factura["tipo_comprobante"],0,1,"R");

        $this->partidasTitle();
    }

    public function partidasTitle()
    {
        $this->SetFont('Arial', '', 6);

        $this->setXY(1, 3.5);

        $this->SetFillColor(180,180,180);
        $this->SetWidths([0.5,1.5,8,1.1,1.1,2,1.5,2,2]);
        $this->SetStyles(['DF','DF','DF','DF','DF','DF','DF','DF','DF']);
        $this->SetRounds(['','','','','','','','','']);
        $this->SetRadius([0.2,0,0,0,0,0,0,0,0.2]);

        $this->SetFills(['117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117']);
        $this->SetTextColors(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
        $this->SetDrawColor(100,100,100);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C','C','C','C','C','C','C','C','C']);

        $this->Row(["#", "Clave SAT", utf8_decode("Descripción"),"Unidad","Cantidad", "Precio Unitario", "Base con Descuentos", "Total", "Descuento"]);

    }

    public function partidas()
    {
        $i = 1;
        foreach($this->factura["conceptos"] as $partida){
            $this->SetWidths([0.5,1.5,8,1.1,1.1,2,1.5,2,2]);
            $this->SetDrawColor(100,100,100);
            $this->SetFont('Arial', '', 7);
            $this->SetAligns(['C','L','L','C','R','R','R','R','R']);
            $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
            $this->SetFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
            $ieps=0;
            if(key_exists("traslados",$partida)) {
                foreach ($partida["traslados"] as $traslado)
                {
                    if($traslado['importe'] == 0 && $traslado['tasa_o_cuota'] == 0 && $partida["importe"] != $traslado['base'])
                    {
                        $ieps = $traslado['base'];
                    }
                }
            }
            $this->Row([
                $i,
                utf8_decode($partida["clave_prod_serv"]),
                utf8_decode($partida["descripcion"]),
                $partida["unidad"],
                $partida["cantidad"],
                "$".number_format($partida["valor_unitario"],2),
                "$".number_format($ieps,2),
                "$".number_format($partida["importe"],2),
                "$".number_format($partida["descuento"],2)
            ]);
            if(key_exists("traslados",$partida)){
                $this->trasladosConcepto($partida["traslados"],$partida["importe"]);
            }
            if(key_exists("retenciones",$partida)){
                $this->retencionesConcepto($partida["retenciones"]);
            }

            $i++;
        }

    }

    public function trasladosConcepto($traslados, $importe)
    {
        $i = 1;
        foreach($traslados as $traslado){
            $this->SetWidths([19.7]);
            $this->SetDrawColor(100,100,100);
            $this->SetFont('Arial', '', 7);
            $this->SetAligns(['L']);
            $this->SetTextColors(['0,0,0']);
            $this->SetFills(['255,255,255']);
            $impuesto = ($traslado["impuesto"]=="002")?"IVA":(($traslado["impuesto"]=="001")?"ISR":($traslado["impuesto"]=="003")?"IEPS":"");
            $ieps = 0;
            if($importe == 0 && $traslado['tasa_o_cuota'] == 0 && $traslado['base'] != $importe)
            {
                $ieps = $importe - $traslado['base'];
            }
            $this->Row([
                "-Impuesto Trasladado-    Base: ".
                number_format($traslado["base"],2).
                "   Impuesto: ".
                $impuesto.
                "   Tipo Factor: ".
                $traslado["tipo_factor"].
                "   Tasa o Cuota: ".
                $traslado["tasa_o_cuota"].
                "   Importe: ".
                number_format($traslado["importe"],2).
                "   IEPS: ".
                number_format($ieps,2),
            ]);
            $this->IEPS = ($ieps) + $this->IEPS;
            $i++;
        }

    }

    public function retencionesConcepto($retenciones)
    {
        $i = 1;
        foreach($retenciones as $retencion){
            $this->SetWidths([19.7]);
            $this->SetDrawColor(100,100,100);
            $this->SetFont('Arial', '', 7);
            $this->SetAligns(['L']);
            $this->SetTextColors(['0,0,0']);
            $this->SetFills(['255,255,255']);
            $impuesto = ($retencion["impuesto"]=="002")?"IVA":(($retencion["impuesto"]=="001")?"ISR":($retencion["impuesto"]=="003")?"IEPS":"");
            $this->Row([
                "-Impuesto Retenido-    Base: ".
                number_format($retencion["base"],2).
                "   Impuesto: ".
                $impuesto.
                "   Tipo Factor: ".
                $retencion["tipo_factor"].
                "   Tasa o Cuota: ".
                $retencion["tasa_o_cuota"].
                "   Importe: ".
                number_format($retencion["importe"],2),
            ]);
            $i++;
        }

    }

    public function detallesPagoImportes()
    {
        $this->ln(0.2);
        $this->SetFont('Arial', '', 8);
        $this->Cell(2,0.4,"Moneda y TC: ");
        $this->Cell(13.5,0.4,$this->factura["moneda"]."-".$this->factura["tipo_cambio"]);
        $this->Cell(2,0.4,"Subtotal: ", 0,0,"R");
        $this->Cell(2.2,0.4,"$".number_format($this->factura["subtotal"],2),0,1,"R");

        if($this->factura["descuento"]>0){
            $this->Cell(15.5,0.4,"");
            $this->Cell(2,0.4,"Descuento: ", 0,0,"R");
            $this->Cell(2.2,0.4,"$".number_format($this->factura["descuento"],2),0,1,"R");
        }

        if($this->IEPS != 0)
        {
            $this->Cell(15.5,0.4,"");
            $this->Cell(2,0.4,"Subtotal c/Descuentos: ", 0,0,"R");
            $this->Cell(2.2,0.4,"$".number_format(($this->factura["subtotal"]-$this->IEPS),2),0,1,"R");
        }

        if(key_exists("traslados",$this->factura)) {
            foreach($this->factura["traslados"] as $traslado){
                $impuesto = ($traslado["impuesto"]=="002")?"IVA":(($traslado["impuesto"]=="001")?"ISR":($traslado["impuesto"]=="003")?"IEPS":"");
                $this->Cell(15.5,0.4,"");
                $this->Cell(2,0.4,$impuesto." Trasladado: ", 0,0,"R");
                $this->Cell(2.2,0.4,"$".number_format($traslado["importe"],2),0,1,"R");
            }
        }

        if(key_exists("retenciones",$this->factura)){
            foreach($this->factura["retenciones"] as $retencion){
                $impuesto = ($retencion["impuesto"]=="002")?"IVA":(($retencion["impuesto"]=="001")?"ISR":($retencion["impuesto"]=="003")?"IEPS":"");
                $this->Cell(15.5,0.4,"");
                $this->Cell(2,0.4,$impuesto." Retenido: ", 0,0,"R");
                $this->Cell(2.2,0.4,"$".number_format($retencion["importe"],2),0,1,"R");
            }
        }

        if($this->IEPS != 0)
        {
            $this->Cell(15.5,0.4,"");
            $this->Cell(2,0.4,"IEPS: ", 0,0,"R");
            $this->Cell(2.2,0.4,"$".number_format($this->IEPS,2),0,1,"R");
        }

        $this->Cell(15.5,0.4,"");
        $this->Cell(2,0.4,"Total: ", 0,0,"R");
        $this->Cell(2.2,0.4,"$".number_format($this->factura["total"],2),0,1,"R");
    }



    function create()
    {
        $this->SetMargins(1, 1, 2);
        $this->SetAutoPageBreak(true, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->partidas();
        $this->detallesPagoImportes();

        try {
            $this->Output('I', 'CFDI_'.$this->factura["uuid"]."_".date("d-m-Y_h_i_s").'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }

}
