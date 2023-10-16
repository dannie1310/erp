<?php

namespace App\PDF\ControlRecursos;

use App\Models\CONTROL_RECURSOS\RelacionGasto;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;

class RelacionGastosFormato extends Rotation
{
    private $encabezado_pdf = '';
    private $relacion = null;
    var $encola = "";

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(RelacionGasto $relacion)
    {
        parent::__construct('P', 'cm', 'Letter');
        $this->relacion = $relacion;
        $this->WidthTotal = $this->GetPageWidth() - 0.82;
        $this->encola = '';
    }

    function Header()
    {
        $this->Image(public_path('/img/logo_hc.png'), 1, .5, 2.5, 1.5);

        //REDEFINICION DEL TAMA�O
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(15.5, .5, ' ', 0, 0, 'C');
        $this->SetFillColor(0, 0, 0);
        $this->Cell(4, .5, 'FOLIO', 1, 1, 'C', 1);
        $this->SetFillColor(204, 204, 204);
        $this->SetTextColor(0, 0, 0);

        $this->Cell(3, .5, ' ', 0, 0, 'C');

        $this->SetFont('Arial', 'BU', 12);
        $this->SetTextColor(0, 0, 0);

        $this->Cell(12.5, .5, utf8_decode('RELACIÓN DE GASTOS'), 0, 0, 'C');

        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(4, .5, $this->relacion->folio, 1, 1, 'C', 1);
        $this->Cell(19.5, 1, '', 0, 1, 'C');

        $this->SetFont('Arial', 'B', 12);

        $this->Cell(19.5, .3, ' ', 0, 1, 'C');

        $this->SetFillColor(0, 0, 0);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(19.5, .5, utf8_decode($this->relacion->empresa_descripcion), 1, 1, 'C', 1);
        $this->SetFillColor(204, 204, 204);
        $this->SetTextColor(0, 0, 0);

        $this->Cell(19.5, .5, ' ', 0, 1, 'C');

        $this->SetFillColor(0, 0, 0);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(9, .5, utf8_decode('TOTAL DE LA RELACIÓN:'), 1, 0, 'L', 1);
        $this->SetFillColor(204, 204, 204);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(10.5, .5, $this->relacion->total_format . " " . $this->relacion->moneda_descripcion, 1, 1, 'C', 1);

        $this->SetFont('Arial', 'B', 7);

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
        $this->Ln();
        $this->SetWidths(array(0.5, 1.5, 1.5, 1.5, 2, 9, 1.5, 1, 1));
        $this->SetFont('Arial', '', 6);
        $this->SetStyles(array('DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF'));
        $this->SetWidths(array(0.5, 1.5, 1.5, 1.5, 2, 9, 1.5, 1, 1));
        $this->SetRounds(array('1', '', '', '', '', '', '', '', '2'));
        $this->SetRadius(array(0.2, 0, 0, 0, 0, 0, 0, 0, 0.2));
        $this->SetFills(array('180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
        $this->SetHeights(array(0.3));
        $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
        $this->Row(array("#", "Cant. Sol.", "Cant. Aut.", "Unidad", "No. Parte", utf8_decode("Descripción"), "Fecha Req.", "C. x C.*", "I. x C.**"));
        $this->SetTextColors(array('0,0,0'));
        $this->SetWidths(array(0.5, 1.5, 1.5, 1.5, 2, 9, 1.5, 1, 1));
        $this->SetRounds(array('', '', '', '', '', '', '', '', ''));
        $this->SetRadius(array(0, 0, 0, 0, 0, 0, 0, 0, 0));
        $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
        $this->SetHeights(array(0.3));
        $this->SetAligns(array('C', 'R', 'R', 'C', 'C', 'J', 'C', 'R', 'R'));
    }

    public function create()
    {
        $this->SetMargins(0.4, 0.5, 0.4);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,3.75);

        $this->partidas();
        try {
            $this->Output('I', "Formato - Relación_".$this->relacion->folio.".pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

    function Footer()
    {
        if (!App::environment('production')) {
            $this->SetFont('Arial','B',90);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(5,15,utf8_decode("MUESTRA"),45);
            $this->RotatedText(10,20,utf8_decode("SIN VALOR"),45);
            $this->SetTextColor('0,0,0');
        }
     //   $this->firmas();

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

        $this->Ln(.5);
        $this->SetY(-0.9);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);

        $this->SetFont('Arial', 'BI', 6);
       // $this->Cell(10, .3, utf8_decode('Formato generado desde el sistema de contratos. Fecha de registro: ' . date("d-m-Y", strtotime($this->fecha))).' Fecha de consulta: '.date("d-m-Y H:i:s").'  Estado: '.$this->estimacion->estado_descripcion, 0, 0, 'L');
        $this->SetXY(22.6,-0.9);
        $this->Cell(5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
        // $this->estatus(); /* Marca de agua : "Propuesta de estimación"
    }

    public function partidas()
    {
        $this->Cell(19.5, .3, ' ', 0, 1, 'C');
        $this->Cell(4, .4, 'FECHA DE REGISTRO: ', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $pdf->Cell(15.5, .4, $relacion->datos["fecha"], 0, 1, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, .4, 'EMPLEADO: ', 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(15.5, .4, $relacion->datos["empleado"], 0, 1, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, .4, 'DEPARTAMENTO: ', 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(15.5, .4, utf8_decode($relacion->datos["departamento"]), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, .4, 'PERIODO: ', 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(4.875, .4, $relacion->datos["periodo"], 0, 1, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, .4, 'PROYECTO/OFICINA: ', 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(15.4, .5, utf8_decode($relacion->datos["proyecto"]), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(4, .4, 'MOTIVO: ', 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(15.5, .4, utf8_decode($relacion->datos["motivo"]), 0, 1, 'J');
        $pdf->Ln(.5);

    }
}
