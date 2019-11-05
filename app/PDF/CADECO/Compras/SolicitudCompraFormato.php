<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 04/11/2019
 * Time: 07:17 p. m.
 */


namespace App\PDF\CADECO\Compras;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use Ghidev\Fpdf\Rotation;

class SolicitudCompraFormato extends Rotation
{

    protected $obra;
    protected $pagoAnticipado;
    private $encabezado_pdf = '';
    var $encola = '';


    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     * SolicitudCompraFormato constructor.
     * @param $solicitudCompra
     */

    public function __construct($id)
    {

        parent::__construct('P', 'cm', 'A4');
        $this->obra = Obra::find(Context::getIdObra());


        $this->SetAutoPageBreak(true, 5);
        $this->WidthTotal = $this->GetPageWidth() - 2;
        $this->txtTitleTam = 18;
        $this->txtSubtitleTam = 13;
        $this->txtSeccionTam = 9;
        $this->txtContenidoTam = 11;
        $this->txtFooterTam = 6;
    }

    function Header()
    {
        $this->setXY(1, 2);
        $this->SetFont('Arial', 'B', 20);
        $this->CellFitScale(1* $this->WidthTotal, 0.1, "Solicitud de Compra", '', 'CB');
    }

    function Footer()
    {
        $this->SetY($this->GetPageHeight() - 1);
        $this->SetFont('Arial', '', 6);

        $this->SetFont('Arial', 'B', 6);
        $this->SetTextColor('100,100,100');
        $this->SetY(28.5);
        $this->Cell(19.5, .4, utf8_decode('Sistema de Administración de Obra'), 0, 0, 'R');

        $this->SetFont('Arial', 'BI', 6);
        $this->SetY(28.5);
        $this->setX(1);
        $this->SetTextColor('0,0,0');
        $this->Cell(7, .4, utf8_decode('Formato generado desde el módulo de Solicitud de Pagos Anticipados. Fecha de registro: '), 0, 0, 'L');
//        .date("Y-m-d H:m:s", strtotime($this->fecha_solicitud))
        $this->Ln(.5);
        $this->SetY(-0.9);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(19.5, .5, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }


   function create() {
       $this->SetMargins(1, 0.5, 1);
       $this->AliasNbPages();
       $this->AddPage();
       $this->SetAutoPageBreak(true,3.75);

       try {
           $this->Output('I', 'Formato - Pago Anticipado.pdf', 1);
       } catch (\Exception $ex) {
           dd("error",$ex);
       }
       exit;
    }




    }
