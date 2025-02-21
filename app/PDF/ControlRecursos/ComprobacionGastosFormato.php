<?php

namespace App\PDF\ControlRecursos;

use App\Models\CONTROL_RECURSOS\Comprobacion;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;

class ComprobacionGastosFormato extends Rotation
{
    private $encabezado_pdf = '';
    private $comprobacion = null;
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

    public function __construct(Comprobacion $comprobacion)
    {
        parent::__construct('P', 'cm', 'Letter');
        $this->comprobacion = $comprobacion;
        $this->WidthTotal = $this->GetPageWidth() - 0.82;
        $this->encola = '';
    }

    function Header()
    {
        $this->Image(public_path('/img/logo_hc.png'), 1, .5, 2.5, 1.5);

        //REDEFINICION DEL TAMAÑO
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(255,255,255);
        $this->Cell(12.5,.5,' ',0,0,'C');
        $this->SetFillColor(0,0,0);
        $this->Cell(4,.5,'SERIE',1,0,'C',1);
        $this->Cell(4,.5,'FOLIO',1,1,'C',1);
        $this->SetFillColor(204,204,204);
        $this->SetTextColor(0,0,0);

        $this->Cell(3, .5, ' ', 0, 0, 'C');

        $this->SetFont('Arial', 'BU', 12);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(9.5,.5,utf8_decode('COMPROBACIÓN DE GASTOS'),0,0,'C');

        //REDEFINICION DEL TAMAÑO
        $this->SetFont('Arial','B',12);
        $this->SetTextColor(0,0,0);
        $this->Cell(4,.5,$this->comprobacion->Serie,1,0,'C',1);
        $this->Cell(4,.5,$this->comprobacion->Folio,1,1,'C',1);
        $this->Cell(19.5,1,'',0,1,'C');

        $this->tablaFechas();

        if($this->encola == "documentos")
        {
            $this->encabezadosDoctos();
        }

        $currentPage = $this->PageNo();
        if($currentPage>1){
            $this->Ln();
        }

/*

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
            $this->encabezadosDoctos();
        }

        $currentPage = $this->PageNo();
        if($currentPage>1){
            $this->Ln();
        }
*/
    }

    public function tablaFechas()
    {
        $this->encola = "documentos";
        $this->SetFont('Arial','',9);
        $this->SetFillColor(0,0,0);
        $this->SetTextColor(255,255,255);
        $this->Cell(8.5,.4,' ',0,0,'C');
        $this->Cell(3.5,.4,'FECHA',1,0,'C',1);
        $this->Cell(8.5,.4,'VALE DEFINITIVO RELACIONADO',1,1,'C',1);
        $this->SetFillColor(204,204,204);
        $this->SetTextColor(0,0,0);

        $this->SetFont('Arial','B',9);

        $VD = '';
		/*VERIFICAMOS LA EXISTENCIA DE UN VALE DEFINITIVO*/
		if(($this->comprobacion->SerieVD=="") ||($this->comprobacion->FolioVD==0))
        {
            $VD=" - SIN VALE RELACIONADO - ";
        }
        else
        {
            $VD=$this->comprobacion->SerieVD." - ".$this->comprobacion->FolioVD;
        }

        $this->Cell(8.5,.4,' ',0,0,'C');
        $this->Cell(3.5,.4,$this->comprobacion->fecha_format,1,0,'C',1);
        $this->Cell(8.5,.4,$VD,1,1,'C',1);

        $this->SetFont('Arial','B',12);

        $this->Cell(20.5,.3,' ',0,1,'C');

        $this->SetFillColor(0,0,0);
        $this->SetTextColor(255,255,255);
        $this->Cell(20.5,.5,$this->comprobacion->empresa_descripcion,1,1,'C',1);
        $this->SetFillColor(204,204,204);
        $this->SetTextColor(0,0,0);

        $this->SetFont('Arial','B',11);

        $this->Cell(20.5,.5,' ',0,1,'C');

        $this->SetFillColor(0,0,0);
        $this->SetTextColor(255,255,255);
        $this->Cell(9,.5,utf8_decode('TOTAL DE LA COMPROBACIÓN:'),1,0,'L',1);
        $this->SetFillColor(204,204,204);
        $this->SetTextColor(0,0,0);
        $this->Cell(11.5,.5,$this->comprobacion->total_comprobante." ".$this->comprobacion->moneda_descripcion,1,1,'C',1);

        $this->SetFont('Arial','',8);
        $this->Cell(20.5,.3,' ',0,1,'C');
        $this->Cell(4,.4,'SOLICITA: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(16.5,.4, $this->comprobacion->empleado_descripcion ,0,1,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(4,.4,'DEPARTAMENTO: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(16.5,.4,$this->comprobacion->departamento,0,1,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(4,.4,'DEL: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(4.875,.4,$this->comprobacion->FechaDelFormatos,0,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(2,.4,'AL: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(4.875,.4,$this->comprobacion->FechaAlFormatos,0,1,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(4,.4,'PROYECTO/OFICINA: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(16.4,.5,$this->comprobacion->proyecto_descripcion,0,1,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(4,.4,'MOTIVO: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->MultiCell(16.5,.4, $this->comprobacion->Motivo ,0,1,'J');
    }

    public function encabezadosDoctos()
    {
        $encabezadofiscal=0;
        $this->SetFont('Arial','B',10);
        $this->Cell(20.5,.5,' ',0,1,'C');
        $EncabezadoSubtotalDeduciblesTotal=0;

        for($i=1;$i<=$this->comprobacion->total_doctos;$i++)
        {
            if($clasificaciondoc[$i]==1)
            {
                $encabezadofiscal=$encabezadofiscal+1;
                $EncabezadoSubtotalDeduciblesTotal=$EncabezadoSubtotalDeduciblesTotal+$totaldoc[$i];
            }
        }


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
        $this->SetAutoPageBreak(true,3);

      //  $this->partidas();
        try {
            $this->Output('I', "Formato - Comprobación: ".$this->comprobacion->folio.".pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

    function Footer()
    {
        /*
        if (!App::environment('production')) {
            $this->SetFont('Arial','B',90);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(3,15,utf8_decode("MUESTRA"),45);
            $this->RotatedText(6,20,utf8_decode("SIN VALOR"),45);
            $this->SetTextColor('0,0,0');
        }

        $this->SetY(25.25);
        $no_firmas = count($this->relacion->firmas) + 1;

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

        $this->SetFont('Arial', '', 6);
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor(0, 0, 0);

        $this->CellFitScale($ancho, .3, "SOLICITA", 1, 2, 'C', 1);
        $this->Cell($ancho, 1, '', 1, 2, 'C');
        $this->SetFont('Arial', 'B', 5);
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0, 0, 0);

        $this->CellFitScale($ancho, .3, $this->relacion->empleado_descripcion, 1, 2, 'C', 1);
        $x+=$ancho;


        foreach($this->relacion->firmas as $firma){
            $this->setY($y);
            $this->setX($x);
            $this->SetFont('Arial', '', 6);
            $this->SetTextColor(255, 255, 255);
            $this->SetFillColor(0, 0, 0);
            $this->CellFitScale($ancho, .3, utf8_decode($firma->encabezado->descripcion), 1, 2, 'C', 1);
            $this->Cell($ancho, 1, '', 1, 2, 'C');
            $this->SetFont('Arial', '', 5);
            $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0, 0, 0);
            $this->CellFitScale($ancho, .3, utf8_decode($firma->firmante->descripcion), 1, 2, 'C', 1);
            $x+=$ancho;
        }


        $this->SetFont('Arial', 'BI', 6);
        $this->SetY(27);



        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(10, .3, utf8_decode('Formato generado desde el sistema de control de recursos. Fecha de registro: ' . date("d-m-Y", strtotime($this->relacion->timestamp_registro))).' Fecha de consulta: '.date("d-m-Y H:i:s").'  Estado: '.$this->relacion->estatus_descripcion, 0, 0, 'L');
        $this->SetXY(22.6,-0.9);
        $this->Cell(5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
        */
    }

    public function partidas()
    {
        $this->Cell(19.5, .3, ' ', 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, .4, 'FECHA DE REGISTRO: ', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(15.5, .4, $this->relacion->fecha_format, 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, .4, 'EMPLEADO: ', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(15.5, .4, $this->relacion->empleado_descripcion, 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, .4, 'DEPARTAMENTO: ', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(15.5, .4, utf8_decode($this->relacion->departamento_descripcion), 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, .4, 'PERIODO: ', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(4.875, .4, $this->relacion->fecha_inicio_format . '->' . $this->relacion->fecha_final_format, 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, .4, 'PROYECTO/OFICINA: ', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(15.4, .5, utf8_decode($this->relacion->proyecto_descripcion), 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, .4, 'MOTIVO: ', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->CellFitSpace(15.5, .4, utf8_decode($this->relacion->motivo), 0, 1, 'J');
        $this->Ln(.5);

        $this->deducibles = $this->relacion->documentos_deducibles->get();
        $this->no_deducibles = $this->relacion->documentos_no_deducibles->get();

        if (count($this->deducibles) > 0) {
            $this->deducibles();
        }

        if (count($this->no_deducibles) > 0) {
            $this->no_deducibles();
        }
        if (count($this->deducibles) + count($this->no_deducibles) == 0) {

            $this->CellFitScale(19.5, 1, utf8_decode('RELACIÓN SIN DOCUMENTOS'), 1, 0, 'C');
            $this->Ln(12);
        }else{
            $this->resumen_gastos();
        }
    }
}
