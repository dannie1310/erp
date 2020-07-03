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
        $solicitud = '';
        $solicitud .= $this->data->sin_solicitud_relacionada?'  Sin Solicitud Relacionada    ':'';
        $solicitud .= $this->data->con_solicitud_relacionada?'  Con Solicitud Relacionada':'';

        $diferencias = '';
        $diferencias .= $this->data->solo_diferencias_activas?'  Solo Diferencias Activas    ':'';
        $diferencias .= $this->data->no_solo_diferencias_activas?'   No Solo Diferencias Activas':'';

        $agrupacion = $this->data->no_solo_diferencias_activas == 1?'  Empresa->Tipo->Diferencia':'  Empresa->Póliza->Diferencia';

        $this->SetFont('Arial', 'B', 10);
        $this->Cell($w, .8,'Solicitud Relacionada:    ', 'L', 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell($w*2, .8, $solicitud, 'R', 'L');
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($w, .8,\utf8_decode('¿Sólo Diferencias Activas?:    '), 'L', 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell($w*2, .8, $diferencias, 'R', 'L');
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($w, .8,\utf8_decode('Agrupación:    '), 'LB', 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell($w*2, .8, \utf8_decode($agrupacion), 'RB', 'L');
        

    }

    public function polizas(){
        $this->SetY($this->GetY() + 2);
        $this->SetTextColor('255');
        $this->setFillColor(0,0,0); 
        $this->Cell($this->WidthTotal, .8,'Nombre de la empresa   ', 0,1, 'L', 1);

        $this->SetTextColor('255');
        $this->setFillColor(100); 
        $this->Cell($this->WidthTotal, .8,'Poliza   ', 0,1, 'L', 1);

        $this->SetFont('Arial', '', 6);
        // $this->SetFillColor(200,200,180,180,0,0,180);
        // $this->SetWidths([.8,2.5,8,2,2,2,2]);
        // // $this->SetStyles(['DF','DF','DF','DF','DF','DF','DF']);
        // $this->SetRounds(['0','','','','','','']);
        // // $this->SetRadius([0.2,0,0,0,0,0,0.2]);
        // $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
        // // $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
        // $this->SetHeights([0.8]);
        // $this->SetAligns(['C','C','C','C','C','C','C']);
        // $this->Row(["#","No. Parte",utf8_decode("Descripción"), "Unidad", "Cantidad", "Precio de Venta", "Importe"]);

        $w = $this->WidthTotal;
        
        $this->SetTextColor('0');
        $this->setFillColor(180,180,180); 
        $this->Cell($w*.03, .8,'#',0, 0,0, 'L', 0);
        $this->Cell($w*.15, .8,'Base de Datos Revisada', 0, 0,0, 'L', 0);
        $this->Cell($w*.15, .8,'Base de Datos Referencia', 0, 0,0, 'L', 0);
        $this->Cell($w*.2, .8,'Tipo Diferencia', 0, 0,0, 'L', 0);
        $this->Cell($w*.1, .8,utf8_decode('Código de Cuenta'), 0, 0,0, 'L', 0);
        $this->Cell($w*.1, .8,'No. Movto.',0, 0,0, 'L', 0);
        $this->Cell($w*.08, .8,'Valor', 0, 0,0, 'L', 0);
        $this->Cell($w*.08, .8,'Valor Referencia', 0, 0,0, 'L', 0);
        $this->Cell($w*.11, .8,'Solicitud',0, 0,0, 'L', 0);
        
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

}