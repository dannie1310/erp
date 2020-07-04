<?php


namespace App\PDF\ContabilidadGeneral;

use Ghidev\Fpdf\Rotation;

class InformeDiferenciasPolizas extends Rotation
{
    private $data;
    private $info;
    private $encola = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct($data, $info)
    {
        parent::__construct('P', 'cm', 'Letter');
        $this->data = $data;
        $this->info = $info;
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
        $this->setXY(12, 1.5);
        $this->ln();

        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', 'B', 12);
        $this->SetWidths(array($this->WidthTotal));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(.55));
        $this->SetStyles(['DF']);
        $this->SetFont('Arial', '', 10);
        $this->MultiCell($this->WidthTotal, .75, 'Empresa: '. 'Nombre de la empresa', 'LTR', 'L');

        $w = $this->WidthTotal/3;
        
        $con_solicitud = $this->data->con_solicitud_relacionada == 'true'?'F':'D';
        $sin_solicitud = $this->data->sin_solicitud_relacionada == 'true'?'F':'D';

        $con_diferencias = $this->data->solo_diferencias_activas == 'true'?'F':'D';
        $sin_diferencias = $this->data->no_solo_diferencias_activas == 'true'?'F':'D';

        $agrupacion = $this->data->tipo_agrupacion == 1?'  Empresa->Tipo->Diferencia':'  Empresa->Póliza->Diferencia';

        $this->setFillColor(0,0,200);
        $this->Circle(2.2,  3.15, .17,  $sin_solicitud);
        $this->Circle(2.2,  3.75, .17,  $con_solicitud);
        $this->Circle(8.8,  3.15, .17,  $con_diferencias);
        $this->Circle(8.8,  3.75, .17,  $sin_diferencias);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell($w, .6,'Solicitud Relacionada:', 'L',0, 'C');
        $this->Cell($w, .6,\utf8_decode('¿Sólo Diferencias Activas?:'), '',0, 'C');
        $this->Cell($w, .6,\utf8_decode('Agrupación:'), 'R',0, 'C');
        $this->Ln();

        $this->SetFont('Arial', '', 8);
        $this->Cell($w, .6,'Sin Solicitud Relacionada', 'L',0, 'C');
        $this->Cell($w, .6,\utf8_decode('Si'), '',0, 'C');
        $this->Cell($w, .6,\utf8_decode($agrupacion), 'R',0, 'C');
        $this->Ln();

        $this->SetFont('Arial', '', 8);
        $this->Cell($w, .6,'Con Solicitud Relacionada', 'LB',0, 'C');
        $this->Cell($w, .6,\utf8_decode('No'), 'B',0, 'C');
        $this->Cell($w, .6,'', 'RB',0, 'C');
        $this->Ln();
        $this->SetY($this->GetY() + 0.5);
        if($this->encola == 'partidas' && $this->data->tipo_agrupacion == 1){
            
            $this->SetFont('Arial', '', 6);
            $this->SetWidths([.8,3,3,1.1,1,1.8,1.2,1.7,2.3,2.3,1.4]);
            $this->SetStyles(['F','F','F','F','F','F','F','F','F','F','F']);
            // $this->SetRounds(['2','2','2','3','4','4','3']);
            $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
            $this->SetHeights([0.5]);
            $this->SetAligns(['C','L','L','L','L','L','L','L','L','L','L']);
            $this->Row(["#","Base de Datos Revisada","Base de Datos Referencia","Ejercicio","Periodo",utf8_decode("Tipo Póliza"), 'Folio', "No. Movto.", "Valor", "Valor Referencia", "Solicitud"]);
            $this->SetFills(['220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220', '220,220,220', '220,220,220']);
        }
        if($this->encola == 'partidas' && $this->data->tipo_agrupacion == 2){
            // $this->SetY($this->GetY() + 1);
            $this->SetFont('Arial', '', 6);
            $this->SetWidths([.8,3,3,3.1,2,1.7,2,2,2]);
            $this->SetStyles(['F','F','F','F','F','F','F','F','F']);
            // $this->SetRounds(['2','2','2','3','4','4','3']);
            $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
            $this->SetHeights([0.5]);
            $this->SetAligns(['C','L','L','L','L','L','L','L','L']);
            $this->Row(["#","Base de Datos Revisada","Base de Datos Referencia","Tipo Diferencia", utf8_decode('Código de Cuenta'), "No. Movto.", "Valor", "Valor Referencia", "Solicitud"]);
            $this->SetFills(['220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220']);
        }

    }

    public function polizas(){
        $datos = $this->info['informe'];
        // $this->SetY($this->GetY() + 0.5);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor('255');
        $this->setFillColor(0,0,0); 
        $this->Cell($this->WidthTotal, .55,$datos['empresa'], 0,1, 'L', 1);

        foreach($datos['informe'] as $informe){
            $this->SetTextColor('255');
            $this->setFillColor(100); 
            $this->SetFont('Arial', '', 8);
            

            if($this->data->tipo_agrupacion == 1){
                $this->Cell($this->WidthTotal, .55,utf8_decode($informe['tipo']). ' (' . $informe['cantidad'] . ')', 0,1, 'L', 1);
                $this->SetFont('Arial', '', 6);
                $this->SetWidths([.8,3,3,1.1,1,1.8,1.2,1.7,2.3,2.3,1.4]);
                $this->SetStyles(['F','F','F','F','F','F','F','F','F','F','F']);
                // $this->SetRounds(['2','2','2','3','4','4','3']);
                $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
                $this->SetHeights([0.5]);
                $this->SetAligns(['C','L','L','L','L','L','L','L','L','L','L']);
                $this->Row(["#","Base de Datos Revisada","Base de Datos Referencia","Ejercicio","Periodo",utf8_decode("Tipo Póliza"), 'Folio', "No. Movto.", "Valor", "Valor Referencia", "Solicitud"]);
                $this->SetFills(['220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220', '220,220,220', '220,220,220']);
            }
            if($this->data->tipo_agrupacion == 2){
                $this->Cell($this->WidthTotal, .55,utf8_decode($informe['poliza']), 0,1, 'L', 1);
                $this->SetFont('Arial', '', 6);
                $this->SetWidths([.8,3,3,3.1,2,1.7,2,2,2]);
                $this->SetStyles(['F','F','F','F','F','F','F','F','F']);
                // $this->SetRounds(['2','2','2','3','4','4','3']);
                $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
                $this->SetHeights([0.5]);
                $this->SetAligns(['C','L','L','L','L','L','L','L','L']);
                $this->Row(["#","Base de Datos Revisada","Base de Datos Referencia","Tipo Diferencia", utf8_decode('Código de Cuenta'), "No. Movto.", "Valor", "Valor Referencia", "Solicitud"]);
                $this->SetFills(['220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220','220,220,220']);
                
            }

            $this->encola = 'partidas';
            foreach($informe['informe'] as $key => $data){
                if($key == 2)break;
                if($this->data->tipo_agrupacion == 1){
                    $this->Row([
                        $key,
                        $data['base_datos_revisada'],
                        $data['base_datos_referencia'],
                        $data['ejercicio'],
                        $data['periodo'],
                        $data['tipo'],
                        $data['numero_folio_poliza'],
                        $data['numero_movimiento'],
                        utf8_decode($data['valor']),
                        utf8_decode($data['valor_referencia']),
                        $data['solicitud_numero_folio'],
                    ]);
                }
                if($this->data->tipo_agrupacion == 2){
                    $this->Row([
                        $key,
                        $data['base_datos_revisada'],
                        $data['base_datos_referencia'],
                        $data['tipo'],
                        $data['codigo_cuenta'],
                        $data['numero_movimiento'],
                        utf8_decode($data['valor']),
                        utf8_decode($data['valor_referencia']),
                        $data['solicitud_numero_folio'],
                    ]);
                }

                
            }
            $this->encola = '';
        // break;
        }
        
        // $this->SetFont('Arial', 'B', 10);
        // // $this->SetFillColor(200);
        // $this->SetFills(['255,255,255']);
        


    }

    function create() {

        
            $this->SetMargins(1, 0.9, 1);
            $this->AliasNbPages();
            $this->AddPage();
            $this->SetAutoPageBreak(true,2.2);
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