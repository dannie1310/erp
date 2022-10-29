<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/07/2020
 * Time: 04:56 PM
 */

namespace App\PDF\Fiscal;


use App\Utils\Util;
use Ghidev\Fpdf\Rotation;

class Comunicado extends Rotation
{
    protected $emisor;

    const DPI = 96;
    const MAX_WIDTH = 180;
    const MAX_HEIGHT = 150;
    const MM_IN_INCH = 25.4;

    private $en_cola = '';

    public function __construct($emisor)
    {
        parent::__construct("P", "cm", "Letter");
        $this->emisor = $emisor;
    }

    function Header()
    {


    }


    public function cuerpo()
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

        $this->MultiCell(17.7,0.6,utf8_decode("El presente comunicado es en relación con la emisión y entrega del Recibo Electrónico de Pago (REP) el cual tendrá que ser emitido a la liquidación de sus comprobantes fiscales digitales, debido a que es un requisito necesario para poder realizar la acreditación de los impuestos trasladados o en su caso, la deducción para cumplir con las disposiciones fiscales de la Ley de Impuesto Sobre la Renta y la Ley del Impuesto al Valor Agregado."),0,"J");
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

        $this->SetFont('Helvetica', '', 13);


        $this->MultiCell(17.7,0.6,"Lista de Facturas con REP Pendiente de ".$this->emisor["proveedor"].":", '','J',0);
        $this->ln(1);
        $this->SetFont('Helvetica', '', 8);

        $this->partidasTitle();

        $i = 1;
        $this->SetAligns(['C','C','C','L','L','R','L']);
        $this->SetTextColors(['117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117']);
        $this->SetFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);

        foreach ($this->emisor["uuid"] as $item) {
            $this->Row([$i, $item->uuid, $item->fecha_format, $item->serie,$item->folio, $item->total_format,$item->moneda]);

            $i++;

        }

    }

    public function partidasTitle()
    {


        $this->SetFillColor(180,180,180);
        $this->SetWidths([0.7,6,2.5,2,2,2.5,2]);
        $this->SetStyles(['DF','DF','DF','DF','DF','DF','DF']);
        $this->SetRounds(['','','','','','','']);
        $this->SetRadius([0.2,0,0,0,0,0,0.2]);

        $this->SetFills(['117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117']);
        $this->SetTextColors(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
        $this->SetDrawColor(100,100,100);
        $this->SetHeights([0.5]);
        $this->SetAligns(['C','C','C','C','C','C','C']);

        $this->Row(["#", "UUID","Fecha", "Serie","Folio","Total","Moneda"]);


    }


    function create()
    {
        $this->SetMargins(2, 2, 2);
        $this->SetAutoPageBreak(true, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->cuerpo();
        $this->AddPage();
        $this->listaCFDI();

        return $this;

        try {
            //$this->Output('F', 'Comunicado_'.date("d-m-Y_h_i_s").'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }

}
