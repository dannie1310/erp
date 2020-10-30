<?php


namespace App\PDF\Contratos;


use App\Facades\Context;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Subcontrato;

class SubcontratoFormato extends Rotation
{
    private $subcontrato;
    private $encabezado_pdf = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(Subcontrato $subcontrato)
    {

        parent::__construct('P', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());
        $this->subcontrato = $subcontrato;

        $this->SetAutoPageBreak(true, 5);
        $this->WidthTotal = $this->GetPageWidth() - 2;
        $this->txtTitleTam = 18;
        $this->txtSubtitleTam = 13;
        $this->txtSeccionTam = 9;
        $this->txtContenidoTam = 11;
        $this->txtFooterTam = 6;
        $this->encabezado_pdf = utf8_decode('SUBCONTRATO');
    }

    function Header(){
        $postTitle=.7;
        // if( BASE == "SAO1814" && ID_OBRA == 41){
        //     $this->image('../../site_media/img/LOGOTIPO_REHABILITACION_ATLACOMULCO.png',1,.3,5,2);
        //     $postTitle=3.5;
        // }
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'B', 12);

        $this->Cell(11.5);
        $this->Cell(1.5,.7,'No.'.$this->subcontrato->clasificacionSubcontrato->tipo->descripcion_corta.':','LT',0,'L');
        $this->CellFit(6.5,.7, $this->subcontrato->referencia,'RT',0,'L');
        $this->Ln(.7);

        $this->SetFont('Arial', 'B', 20);
        $this->Cell(11.5, $postTitle, utf8_decode($this->subcontrato->clasificacionSubcontrato->tipo->descripcion) , 0, 0, 'C', 0);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(4.5,.7, 'FECHA :','BL',0,'L');
        $this->Cell(3.5,.7, $this->subcontrato->fecha_format.' ','RB',0,'L');
        $this->Ln(.7);

        $this->Ln(.6);
        $y_inicial = $this->getY();
        $x_inicial = $this->getX();

        $alto = abs(1);
        $this->SetWidths(array(19.5));
        $this->SetRounds(array('1234'));
        $this->SetRadius(array(0.1));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array($alto));
        $this->SetStyles(array('DF'));
        $this->SetAligns("L");
        $this->SetFont('Arial', 'B', 13);
        $this->setY($y_inicial);
        $this->Row(array(""));
        $this->setY($y_inicial);
        $this->setX($x_inicial);
        $this->MultiCell(19.5, 1, 'PROYECTO: '. $this->obra->nombre, '', 'C');

        $this->Ln(.3);

        // dd($this->subcontrato->id_sucursal, $this->subcontrato->sucursal);
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
        $this->SetFont('Arial', '', 10);
        $this->Cell(9.5,.7,'Subcontratista',0,0,'L');
        $this->Cell(.5);
        $this->Cell(9.5,.7,'Cliente (Facturar a)',0,0,'L');
        $this->Ln(.6);
        $y_inicial = $this->getY();
        $x_inicial = $this->getX();
        $this->MultiCell(9.5, .5,$this->subcontrato->empresa->razon_social.''. $this->subcontrato->sucursal->direccion . ', C.P.'. $this->subcontrato->sucursal->codigo_postal  .', '. $this->subcontrato->sucursal->estado .', '.$this->subcontrato->empresa->rfc, '', 'L');
        $y_final_1 = $this->getY();
        $this->setY($y_inicial);
        $this->setX($x_inicial+10);
        $this->MultiCell(9.5, .5,$this->obra->facturar.''.$this->obra->direccion.''.$this->obra->rfc, '', 'L');
        $y_final_2 = $this->getY();
        if($y_final_1>$y_final_2){$y_alto = $y_final_1;}else{$y_alto = $y_final_2;}
        $alto = abs($y_inicial-$y_alto);
        $this->SetWidths(array(9.5));
        $this->SetRounds(array('1234'));
        $this->SetRadius(array(0.2));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array($alto));
        $this->SetStyles(array('DF'));
        $this->SetAligns("L");
        $this->SetFont('Arial', '', 10);
        $this->setY($y_inicial);
        $this->Row(array(""));
        $this->setY($y_inicial);
        $this->setX($x_inicial);
        $this->MultiCell(9.5, .5,$this->subcontrato->empresa->razon_social.''. $this->subcontrato->sucursal->direccion . ', C.P.'. $this->subcontrato->sucursal->codigo_postal  .', '. $this->subcontrato->sucursal->estado .', '.$this->subcontrato->empresa->rfc, '1', 'L');
        $this->setY($y_inicial);
        $this->setX($x_inicial+10);
        $this->Row(array(""));

        $this->setY($y_inicial);
        $this->setX($x_inicial+10);
        $this->MultiCell(9.5, .5,utf8_decode($this->obra->facturar.''.$this->obra->direccion.''.$this->obra->rfc), '', 'L');
        $this->Ln(.9);
        $y_inicial = $this->getY();
        $x_inicial = $this->getX();

        //$this->datos_encabezado["observacion"] = substr($this->datos_encabezado["observacion"],1,200);
        $long = strlen($this->subcontrato->observaciones);
        if($long <= 104){
            $alto = .5;
        }
        else{
            if($long <= 200){
                $alto = 1;
            }
            else{
                $alto = 1.5;
            }
        }

        $this->SetFont('Arial', 'B', 10);
        $this->CellFit(2.5, $alto, utf8_decode('Descripción: ') , 0, 0, 'C', 0);
        $alto = abs($alto);
        $this->SetWidths(array(17));
        $this->SetRounds(array('1234'));
        $this->SetRadius(array(.1));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array($alto));
        $this->SetStyles(array('DF'));
        $this->SetFont('Arial', '', 10);
        $this->Row(array(""));
        $this->setY($y_inicial);
        $this->setX($x_inicial);
        $this->CellFit(2.5, 1, '', 0, 0, 'C', 0);
        $this->MultiCell(17, .5,  utf8_decode($this->subcontrato->observaciones), '', 'L');
        
    }

    function footer(){}

    function create() {
        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,6.80);
        // $this->partidas();

        try {
            $this->Output('I', "Formato - Subcontrato ".$this->subcontrato->numero_folio_format.".pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

}