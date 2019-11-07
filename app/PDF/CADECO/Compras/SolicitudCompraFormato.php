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
        $this->encabezado_pdf = utf8_decode('SOLICITUD DE COMPRA');
    }

    function Header()
    {
        $this->setXY(1, 2);
        $this->SetFont('Arial', 'B', 24);
        $this->CellFitScale(1* $this->WidthTotal, 0.1, $this->encabezado_pdf, '', 'CB');

        //Obtener Posiciones despues de los títulos
        $y_inicial = $this->getY() - 1;
        $x_inicial = $this->GetPageWidth() / 1.48;
        $this->setY($y_inicial);
        $this->setX($x_inicial);

        //Tabla Detalles de la Asignación
        $this->detallesSolicitudPagoAnticipado($x_inicial);

        //Posiciones despues de la primera tabla
        $y_final = $this->getY();

        $this->setXY($x_inicial, $y_inicial);

        $alto = abs($y_final - $y_inicial);

        //Redondear Bordes Detalles Asignacion
        $this->SetWidths(array(0.3 * $this->WidthTotal));

        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array($alto));
        $this->SetStyles(array('DF'));
        $this->SetAligns("L");
        $this->SetFont('Arial', '', $this->txtContenidoTam);
        $this->setXY($x_inicial, $y_inicial);


        $this->setXY($x_inicial, $y_inicial);


        //Tabla Detalles de la Asignacion
        $this->setY($y_inicial);
        $this->setX($x_inicial);
        $this->detallesSolicitudPagoAnticipado($x_inicial);

        //Obtener Y después de la tabla
        $this->setY($y_final);
        $this->Ln(1);

        $this->SetFont('Arial', 'B', 13);

        $this->SetWidths(array(19.5));
        $this->SetRounds(array('1234'));
        $this->SetRadius(array(0.2));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(0.7));
        $this->SetRounds(array('1234'));
        $this->SetRadius(array(0.2));
        $this->SetAligns("C");
        $this->Row(array(utf8_decode($this->obra->nombre)));
        $this->Ln(.2);
        $this->SetFont('Arial', '', 10);
        $this->Cell(10);
        $this->Cell(9.5, .7, utf8_decode('EMPRESA'), 0, 0, 'L');
        $this->Ln(.6);
        $y_inicial = $this->getY();
        $this->Cell(10);
        $this->SetFont('Arial', 'B', 10);
        $this->CellFitScale(9.5, .5, utf8_decode($this->obra->descripcion), '', 'J');
        $this->Ln(.5);
        $this->Cell(10);
        $this->SetFont('Arial', '', 10);
        $this->Multicell(9.5, .5, utf8_decode($this->obra->direccion) . ' 
        RFC: ' . $this->obra->rfc, '', 'J');
        $y_final = $this->getY();
        $alto = $y_final - $y_inicial;

        $this->SetWidths(array(10, 9.5));
        $this->SetRounds(array('1234', '1234'));
        $this->SetRadius(array(0.2, 0.2));
        $this->SetFills(array('255,255,255', '255,255,255'));
        $this->SetTextColors(array('0,0,0', '0,0,0'));
        $this->SetHeights(array($alto));
        $this->SetStyles(array('F', 'DF'));
        $this->SetAligns("L");
        $this->SetFont('Arial', 'B', 10);
        $this->setY($y_inicial);
        $this->Row(array("", ""));
        $this->setY($y_inicial);
        /* se sobre escribe la información */
        $this->Cell(10);
        $this->SetFont('Arial', 'B', 10);
        $this->CellFitScale(9.5, .5, utf8_decode($this->obra->descripcion), '', 'J');
        $this->Ln(.5);
        $this->Cell(10);
        $this->SetFont('Arial', '', 10);
        $this->Multicell(9.5, .5, utf8_decode($this->obra->direccion . ' 
RFC: ' . $this->obra->rfc), '', 'J');


        $this->Ln(.2);
        $this->SetWidths(array(19.5));
        $this->SetRounds(array('12'));
        $this->SetRadius(array(0.2));
        $this->SetFills(array('180,180,180'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetStyles(array('DF'));
        $this->SetHeights(array(0.5));
        $this->SetFont('Arial', '', 6);
        $this->SetAligns(array('C'));
        $this->Ln(.5);


//        if ($this->encola == "observaciones_encabezado") {
//            $this->SetWidths(array(19.5));
//            $this->SetRounds(array('12'));
//            $this->SetRadius(array(0.2));
//            $this->SetFills(array('180,180,180'));
//            $this->SetTextColors(array('0,0,0'));
//            $this->SetHeights(array(0.3));
//            $this->SetFont('Arial', '', 6);
//            $this->SetAligns(array('C'));
//        } else if ($this->encola == "observaciones") {
//            $this->SetRounds(array('34'));
//            $this->SetRadius(array(0.2));
//            $this->SetAligns(array('J'));
//            $this->SetStyles(array('DF'));
//            $this->SetFills(array('255,255,255'));
//            $this->SetTextColors(array('0,0,0'));
//            $this->SetHeights(array(0.3));
//            $this->SetFont('Arial', '', 6);
//            $this->SetWidths(array(19.5));
//        }
    }

    function detallesSolicitudPagoAnticipado($x)
    {

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FOLIO'), 'LT', 0, 'L');
        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->Cell(0.207 * $this->WidthTotal, 0.5, utf8_decode("# 555"), 'RT', 1, 'R');

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FECHA'), 'L', 0, 'L');
        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->Cell(0.207 * $this->WidthTotal, 0.5, "22-10-2019", 'R', 1, 'R');


        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FECHA REQ. O.'), 'L', 0, 'L');
        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->Cell(0.207 * $this->WidthTotal, 0.5, "22-10-2019", 'R', 1, 'R');


        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FOLIO REQ. O.'), 'L', 0, 'L');
        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->Cell(0.207 * $this->WidthTotal, 0.5, "22-10-2019", 'R', 1, 'R');

        $this->SetFont('Arial', 'B', 9);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FOLIO SAO'), 'LB', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.207 * $this->WidthTotal, 0.5,utf8_decode("555"), 'RB', 1, 'R');

    }

    function partidas(){
        $this->Ln(.8);
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180,180,180);
        $this->SetWidths([0.5,1.5,1.5,2.5,2.5,9,2]);
        $this->SetStyles(['DF','DF','DF','FD','FD','DF']);
        $this->SetRounds(['1','','','','','','2']);
        $this->SetRadius([0.2,0,0,0,0,0,0.2]);
        $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
        $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C','C','C','C','C','C','C']);
        $this->Row(["#","Cant. Solicitada", "Cant. Autorizada", "Unidad", "No. Parte", utf8_decode("Descripción"), "Fecha. Req"]);


    }

    function firmas(){
        $this->SetY(-4.5);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);


        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4, utf8_decode('Realizó'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);



        $this->SetY(-4.11);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 1.2, '', 'TRLB', 0, 'C');
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 1.2, '', 'TRLB', 0, 'C');
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 1.2, '', 'TRLB', 0, 'C');



        $this->SetY(-3.0);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4,  "", 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4,  "", 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4,  "", 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);


//        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4, utf8_decode('Solicitó'), 'TRLB', 0, 'C', 1);
//        $this->Cell(0.73);
//        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4, utf8_decode('Capturó'), 'TRLB', 0, 'C', 1);
//        $this->Cell(0.73);
//        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4, utf8_decode('Aprobó'), 'TRLB', 0, 'C', 1);
//        $this->Cell(0.73);
//
//
//
//        $this->Cell(($this->GetPageWidth() - 3) / 3, 1.2, '', 'TRLB', 0, 'C');
//        $this->Cell(0.73);
//        $this->Cell(($this->GetPageWidth() - 3) / 3, 1.2, '', 'TRLB', 0, 'C');
//        $this->Cell(0.73);
//        $this->Cell(($this->GetPageWidth() - 3) / 3, 1.2, '', 'TRLB', 0, 'C');
//        $this->Cell(0.73);
//
//
//
//        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4,  utf8_decode('RESPONSABLE DE ÁREA'), 'TRLB', 0, 'C', 1);
//        $this->Cell(0.73);
//        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4,  utf8_decode('GERENCIA DE ÁREA'), 'TRLB', 0, 'C', 1);
//        $this->Cell(0.73);
//        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4,  utf8_decode('DIRECCIÓN DE ÁREA'), 'TRLB', 0, 'C', 1);
//        $this->Cell(0.73);

    }

    function Footer()
    {
        $this->firmas();
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
       $this->partidas();

       try {
           $this->Output('I', 'Formato - Pago Anticipado.pdf', 1);
       } catch (\Exception $ex) {
           dd("error",$ex);
       }
       exit;
    }




    }
