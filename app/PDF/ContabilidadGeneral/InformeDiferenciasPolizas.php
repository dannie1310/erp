<?php


namespace App\PDF\ContabilidadGeneral;

use Ghidev\Fpdf\Rotation;

class InformeDiferenciasPolizas extends Rotation
{
    private $data;
    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct($data)
    {
        parent::__construct('P', 'cm', 'Letter');
        $this->data = $data;
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
        $this->setXY(0.9, 1.2);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Helvetica', 'BI', 12);
        $this->Cell(0, 0, \utf8_decode('Informe de Diferencias en Pólizas'), 0, 0, 'L');
        $this->setXY(12, 2);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', 'B', 12);
        $this->ln();

        $this->SetWidths(array($this->WidthTotal));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(.65));
        $this->SetStyles(['DF']);
        $this->SetFont('Arial', '', 10);
        $this->MultiCell($this->WidthTotal, .9, 'Empresa: '. 'Nombre de la empresa', 'LTR', 'L');

        $w = $this->WidthTotal/3;
        
        $con_solicitud = $this->data->sin_solicitud_relacionada == 'true'?'F':'D';
        $sin_solicitud = $this->data->con_solicitud_relacionada == 'true'?'F':'D';

        $con_diferencias = $this->data->solo_diferencias_activas == 'true'?'F':'D';
        $sin_diferencias = $this->data->no_solo_diferencias_activas == 'true'?'F':'D';

        $agrupacion = $this->data->no_solo_diferencias_activas == 1?'  Empresa->Tipo->Diferencia':'  Empresa->Póliza->Diferencia';

        $this->setFillColor(0,0,200);
        $this->Circle(2,  3.9, .17,  $sin_solicitud);
        $this->Circle(2,  4.6, .17,  $con_solicitud);
        $this->Circle(8.5,  3.9, .17,  $con_diferencias);
        $this->Circle(8.5,  4.6, .17,  $sin_diferencias);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell($w, .7,'Solicitud Relacionada:', 'L',0, 'C');
        $this->Cell($w, .7,\utf8_decode('¿Sólo Diferencias Activas?:'), '',0, 'C');
        $this->Cell($w, .7,\utf8_decode('Agrupación:'), 'R',0, 'C');
        $this->Ln();

        $this->SetFont('Arial', '', 8);
        $this->Cell($w, .7,'Sin Solicitud Relacionada', 'L',0, 'C');
        $this->Cell($w, .7,\utf8_decode('Si'), '',0, 'C');
        $this->Cell($w, .7,\utf8_decode($agrupacion), 'R',0, 'C');
        $this->Ln();

        $this->SetFont('Arial', '', 8);
        $this->Cell($w, .7,'Con Solicitud Relacionada', 'LB',0, 'C');
        $this->Cell($w, .7,\utf8_decode('No'), 'B',0, 'C');
        $this->Cell($w, .7,'', 'RB',0, 'C');
        $this->Ln();   

    }

    public function polizas(){
        $this->SetY($this->GetY() + 1);
        $this->SetTextColor('255');
        $this->setFillColor(0,0,0); 
        $this->Cell($this->WidthTotal, .7,'Nombre de la empresa   ', 0,1, 'L', 1);

        $this->SetTextColor('255');
        $this->setFillColor(100); 
        $this->Cell($this->WidthTotal, .7,'Poliza   ', 0,1, 'L', 1);

        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(200,200,180,180,0,0,180);
        $this->SetWidths([.8,2.5,8,2,2,2,2.3]);
        $this->SetStyles(['F','F','F','F','F','F','F']);
        $this->SetRounds(['2','2','2','3','4','4','3']);
        // $this->SetRadius([0.2,0,0,0,0,0,0.2]);
        $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
        // $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
        $this->SetHeights([0.8]);
        $this->SetAligns(['C','C','C','C','C','C','C']);
        $this->Row(["#","No. Parte",utf8_decode("Descripción"), "Unidad", "Cantidad", "Precio de Venta", "Importe"]);

        // $w = $this->WidthTotal;
        
        // $this->SetTextColor('0');
        // $this->setFillColor(180,180,180); 
        // $this->Cell($w*.03, .7,'#',0, 0,0, 'L', 0);
        // $this->Cell($w*.15, .7,'Base de Datos Revisada', 0, 0,0, 'L', 0);
        // $this->Cell($w*.15, .7,'Base de Datos Referencia', 0, 0,0, 'L', 0);
        // $this->Cell($w*.2, .7,'Tipo Diferencia', 0, 0,0, 'L', 0);
        // $this->Cell($w*.1, .7,utf8_decode('Código de Cuenta'), 0, 0,0, 'L', 0);
        // $this->Cell($w*.1, .7,'No. Movto.',0, 0,0, 'L', 0);
        // $this->Cell($w*.08, .7,'Valor', 0, 0,0, 'L', 0);
        // $this->Cell($w*.08, .7,'Valor Referencia', 0, 0,0, 'L', 0);
        // $this->Cell($w*.11, .7,'Solicitud',0, 0,0, 'L', 0);
        
        $this->SetFont('Arial', 'B', 10);
        // $this->SetFillColor(200);
        $this->SetFills(['255,255,255']);
        


    }

    function create() {

        
            $this->SetMargins(1, 0.9, 1);
            $this->AliasNbPages();
            $this->AddPage();
            $this->SetAutoPageBreak(true,5);
            $this->polizas();
        

        try {
            $this->Output('I', "Informe Diferencias Polizas.pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

    function Circle($x, $y, $r, $style='D')
{
    $this->Ellipse($x,$y,$r,$r,$style);
}

function Ellipse($x, $y, $rx, $ry, $style='D')
{
    if($style=='F')
        $op='f';
    elseif($style=='FD' || $style=='DF')
        $op='B';
    else
        $op='S';
    $lx=4/3*(M_SQRT2-1)*$rx;
    $ly=4/3*(M_SQRT2-1)*$ry;
    $k=$this->k;
    $h=$this->h;
    $this->_out(sprintf('%.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
        ($x+$rx)*$k,($h-$y)*$k,
        ($x+$rx)*$k,($h-($y-$ly))*$k,
        ($x+$lx)*$k,($h-($y-$ry))*$k,
        $x*$k,($h-($y-$ry))*$k));
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
        ($x-$lx)*$k,($h-($y-$ry))*$k,
        ($x-$rx)*$k,($h-($y-$ly))*$k,
        ($x-$rx)*$k,($h-$y)*$k));
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
        ($x-$rx)*$k,($h-($y+$ly))*$k,
        ($x-$lx)*$k,($h-($y+$ry))*$k,
        $x*$k,($h-($y+$ry))*$k));
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c %s',
        ($x+$lx)*$k,($h-($y+$ry))*$k,
        ($x+$rx)*$k,($h-($y+$ly))*$k,
        ($x+$rx)*$k,($h-$y)*$k,
        $op));
}

}