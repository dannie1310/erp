<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/07/2020
 * Time: 04:56 PM
 */

namespace App\PDF\Fiscal;


use App\Utils\PDF\FPDI\Fpdi;
use App\Utils\Util;
use Ghidev\Fpdf\Rotation;

class Comunicado extends Fpdi
{
    protected $emisor;

    const DPI = 96;
    const MAX_WIDTH = 180;
    const MAX_HEIGHT = 150;
    const MM_IN_INCH = 25.4;

    private $en_cola = '',
            $comunicado = '';

    public function __construct($emisor)
    {
        parent::__construct("P", "cm", "Letter");
        $this->emisor = $emisor;
        $this->SetMargins(2, 2, 2);
        $this->SetAutoPageBreak(true, 1);

        $this->setSourceFile(public_path('pdf/fiscal/ComunicadoRep.pdf'));
        $this->comunicado = $this->importPage(1);

        $this->AliasNbPages();
        $this->AddPage();
        $this->useTemplate($this->comunicado,0, 0.5, 22);
        $this->SetFont('Helvetica', 'B', 11);
        $this->SetTextColor("120,120,120");
        $this->setXY(2.7,5.8);
        $this->SetFillColor(254,254,254);
        if(strlen($this->emisor["proveedor"])<=50){

            $this->Cell(16.8,1,"Estimado proveedor ".$this->emisor["proveedor"], '',1,'L',1);

        }else{
            $this->MultiCell(16.8,0.5,"Estimado proveedor ".$this->emisor["proveedor"], '',1,'L',1);

        }


        //$this->comunicado();
        $this->AddPage();
        $this->listaCFDI();
    }

    function Header()
    {


    }

    function SetTextColor($r, $g=null, $b=null)
    {
        $datos = explode(',', $r);
        if(count($datos)==3){
            $r = $datos[0];
            $g = $datos[1];
            $b = $datos[2];
        }else{
            $r = $r;
            $g = $g;
            $b = $b;
        }

        // Set color for text
        if(($r==0 && $g==0 && $b==0) || $g===null) {

            $this->TextColor = sprintf('%.3F g', $datos[0] / 255);
            //dd(1,$this->TextColor);
        }
        else {
            $this->TextColor = sprintf('%.3F %.3F %.3F rg'
                , ($r/255)
                , ($g/255)
                , ($b/255)
            );
            //dd(2,$this->TextColor);
        }
        $this->ColorFlag = ($this->FillColor!=$this->TextColor);
    }


    public function comunicado()
    {
        $this->SetFont('Helvetica', '', 20);
        $this->Cell(17.7,1,"Comunicado", '',1,'C');
        $this->SetFont('Helvetica', '', 13);
        $this->ln("1.5");

        $this->Cell(17.7,1,utf8_decode("Ciudad de México a ").date("d")." de ".strtolower(Util::getMesTxt(date("m")))." de ".date("Y"), '',1,'R');
        $this->ln("1.5");
        ########

        $this->Cell(17.7,1,"Estimados proveedores: ", '',1,'J');
        $this->ln(".7");

        $this->MultiCell(17.7,0.6,utf8_decode("El presente comunicado es con relación a la emisión y entrega del Recibo Electrónico de Pago (REP) el cual tendrá que ser emitido a la liquidación de sus comprobantes fiscales digitales, debido a que es un requisito necesario para poder realizar la acreditación de los impuestos trasladados o en su caso, la deducción para cumplir con las disposiciones fiscales de la Ley de Impuesto Sobre la Renta y la Ley del Impuesto al Valor Agregado."),0,"J");
        $this->ln(".3");


        $this->MultiCell(17.7,0.6,utf8_decode("Es importante ponerse al corriente en el envío de su REP requerido, ya que, a partir de las siguientes semanas iniciaremos con el bloqueo del pago de sus comprobantes fiscales digitales pendientes, hasta en tanto no se pongan al corriente con la entrega de sus Recibos de Pago de los pagos emitidos durante el ejercicio fiscal de 2019, 2020, 2021 y 2022."),0,"J");
        $this->ln(".3");

        $this->MultiCell(17.7,0.6,utf8_decode("Se les solicita que sobre los siguientes pagos que les hagamos, nos realicen la emisión del Recibo Electrónico de Pago dentro del mes en el que se hizo el depósito. "),0,"J");
        $this->ln(".3");

        $this->MultiCell(17.7,0.6,utf8_decode("Una vez emitidos los Comprobantes Fiscales Digitales por Recibo Electrónico de Pago (PDF y XML), sin comprimir, deben enviarse a la dirección de correo electrónico _____________."),0,"J");
        $this->ln(".3");

        $this->MultiCell(17.7,0.6,"Cualquier duda y/o comentario comunicarse a las extensiones: 1771, 1772, 1789, 1751 y 1793.");
        $this->ln(".5");

        $this->Cell(17.7,1,"Saludos Cordiales", '',1,'C');
        $this->ln("2");

        $this->Cell(17.7,1,"Hermes Infraestructura", '',1,'C');
        $this->Cell(17.7,1,"Gerencia de Impuestos", '',1,'C');


    }

    public function listaCFDI()
    {
        $this->SetTextColor("0,0,0");
        $this->SetFont('Helvetica', '', 13);
        $this->MultiCell(17.7,0.6,"Lista de Facturas con REP Pendiente de ".$this->emisor["proveedor"].":", '','J',0);
        $this->ln(0.5);

        $this->listaCFDIxReceptor();
    }

    public function listaCFDIxReceptor()
    {

        $total_emisor = 0;
        $saldo_emisor = 0;
        $cantidad_uuid_por_emisor = 0;
        foreach ($this->emisor["receptores"] as $rfc=>$receptor) {
            $this->SetFont('Helvetica', '', 11);
            $this->SetTextColor(0,0,0);
            $this->MultiCell(17.7,0.6,utf8_decode("Emitidos a ".$receptor["empresa"])." (".$rfc."):", '','J',0);
            $this->ln(0.3);
            $this->partidasTitle();

            $i = 1;
            $total_receptor = 0;
            $saldo_receptor = 0;
            foreach ($receptor["uuid"] as $item) {
                $this->SetAligns(['C','C','C','L','C','R','R','L']);
                $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
                $this->SetFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
                $this->Row([$i, $item->uuid, $item->fecha_sencilla_format, $item->serie,$item->folio, $item->total_format,$item->monto_pendiente_rep_vw_format,$item->moneda]);
                $total_receptor += $item->total_mxn;
                $total_emisor += $item->total_mxn;
                $saldo_receptor += $item->monto_pendiente_rep_vw_mxn;
                $saldo_emisor += $item->monto_pendiente_rep_vw_mxn;
                $i++;
                $cantidad_uuid_por_emisor ++;
            }

            $this->SetFillColor(213,213,213);
            $this->SetFont('Helvetica', '', 6);
            $this->cell(12.7,0.5,"Total ".utf8_decode($receptor["empresa"]).": ",1,0,'R',1);
            $this->cell(2,0.5,"$ ".number_format($total_receptor,2,".",","),1,0,'R',1);
            $this->cell(2,0.5,"$ ".number_format($saldo_receptor,2,".",","),1,0,'R',1);
            $this->cell(1,0.5,"MXN",1,1,'L',1);
            $this->ln("0.5");

        }
        $this->SetFillColor(117,117,117);
        $this->setTextColor(255,255,255);

        $this->SetFont('Helvetica', '', 6);
        $this->cell(0.7,0.5,$cantidad_uuid_por_emisor,1,0,'C',1);
        $this->cell(12,0.5,"Total: ",1,0,'R',1);
        $this->cell(2,0.5,"$ ".number_format($total_emisor,2,".",","),1,0,'R',1);
        $this->cell(2,0.5,"$ ".number_format($saldo_emisor,2,".",","),1,0,'R',1);
        $this->cell(1,0.5,"MXN",1,1,'L',1);

    }

    public function partidasTitle()
    {
        $this->SetFont('Helvetica', '', 6);

        $this->SetFillColor(180,180,180);
        $this->SetWidths([0.7,5.7,1.8,1.5,3,2,2,1]);
        $this->SetStyles(['DF','DF','DF','DF','DF','DF','DF','DF']);
        $this->SetRounds(['','','','','','','','']);
        $this->SetRadius([0.2,0,0,0,0,0,0,0.2]);

        $this->SetFills(['213,213,213','213,213,213','213,213,213','213,213,213','213,213,213','213,213,213','213,213,213','213,213,213']);
        $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
        $this->SetDrawColor(100,100,100);
        $this->SetHeights([0.5]);
        $this->SetAligns(['C','C','C','C','C','C','C','C']);

        $this->Row(["#", "UUID","Fecha", "Serie","Folio","Total","Saldo REP","Moneda"]);

    }


    function create()
    {
        return $this;
    }

}
