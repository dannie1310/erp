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
            $this->factura["emisor"]["rfc"]."-".$this->factura["emisor"]["razon_social"]
            , ''
            , ($this->factura["receptor"]["rfc"]."-".$this->factura["receptor"]["razon_social"])
        ]);

        $this->SetFont('Helvetica', '', 8);

        $this->Cell(2,0.5,"Folio Fiscal: ");
        $this->Cell(2,0.5,$this->factura["uuid"],0,1);
        $this->Cell(2,0.5,"Serie y Folio: ");
        $this->Cell(2,0.5,$this->factura["serie"].$this->factura["folio"],0,1);

        $this->partidasTitle();
    }

    public function partidasTitle()
    {
        $this->SetFont('Arial', '', 8);

        $this->setXY(1, 3.5);

        $this->SetFillColor(180,180,180);
        $this->SetWidths([0.8,1.5,8,3.2,2,2,2.2]);
        $this->SetStyles(['DF','DF','DF','DF','DF','DF','DF']);
        $this->SetRounds(['','','','','','','']);
        $this->SetRadius([0.2,0,0,0,0,0,0.2]);

        $this->SetFills(['117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117']);
        $this->SetTextColors(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
        $this->SetDrawColor(100,100,100);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C','C','C','C','C','C','C']);

        $this->Row(["#", "Clave SAT", utf8_decode("Descripción"),"Unidad","Cantidad", "Precio Unitario", "Total"]);

    }

    public function partidas()
    {
        $i = 1;
        foreach($this->factura["conceptos"] as $partida){
            $this->SetDrawColor(100,100,100);
            $this->SetFont('Arial', '', 8);
            $this->SetAligns(['C','L','L','C','R','R','R']);
            $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
            $this->SetFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
            $this->Row([
                $i,


                utf8_decode($partida["clave_prod_serv"]),
                utf8_decode($partida["descripcion"]),
                $partida["unidad"],
                $partida["cantidad"],
                "$".number_format($partida["valor_unitario"],2),
                "$".number_format($partida["importe"],2),
            ]);
            $i++;
        }

    }

    public function detallesPagoImportes()
    {
        $this->SetFont('Arial', '', 8);
        $this->Cell(2,0.5,"Moneda y TC: ");
        $this->Cell(13.5,0.5,$this->factura["moneda"]."-".$this->factura["tipo_cambio"]);
        $this->Cell(2,0.5,"Subtotal: ", 0,0,"R");
        $this->Cell(2.2,0.5,"$".number_format($this->factura["subtotal"],2),0,1,"R");

        $this->Cell(15.5,0.5,"");
        $this->Cell(2,0.5,"Impuestos Trasladados: ", 0,0,"R");
        $this->Cell(2.2,0.5,"$".number_format($this->factura["total_impuestos_trasladados"],2),0,1,"R");

        $this->Cell(15.5,0.5,"");
        $this->Cell(2,0.5,"Impuestos Retenidos: ", 0,0,"R");
        $this->Cell(2.2,0.5,"$".number_format($this->factura["total_impuestos_retenidos"],2),0,1,"R");

        $this->Cell(15.5,0.5,"");
        $this->Cell(2,0.5,"Total: ", 0,0,"R");
        $this->Cell(2.2,0.5,"$".number_format($this->factura["total"],2),0,1,"R");
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
            $this->Output('I', 'CFDI_'.date("d-m-Y h:i:s").'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }

}
