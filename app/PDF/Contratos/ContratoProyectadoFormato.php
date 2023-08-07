<?php


namespace App\PDF\Contratos;


use App\Facades\Context;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Obra;
use Ghidev\Fpdf\Rotation;

class ContratoProyectadoFormato extends Rotation
{
    private $contrato;
    private $encabezado_pdf = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(ContratoProyectado $contrato)
    {

        parent::__construct('P', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());
        $this->contrato = $contrato;

        $this->SetAutoPageBreak(true, 5);
        $this->WidthTotal = $this->GetPageWidth() - 2;
        $this->txtTitleTam = 18;
        $this->txtSubtitleTam = 13;
        $this->txtSeccionTam = 9;
        $this->txtContenidoTam = 11;
        $this->txtFooterTam = 6;
        $this->encabezado_pdf = utf8_decode('CONTRATO PROYECTADO');
    }

    function Header()
    {
        $this->setXY(1, 0.5);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'B', 12);

        $this->Cell(14);
        $this->Cell(2.5,.7,'Folio:','LT',0,'L');
        $this->Cell(3,.7, $this->contrato->numero_folio_format,'RT',0,'R');
        $this->Ln(.7);

        $this->SetFont('Arial', 'B', 20);
        $this->CellFitScale(14, 0.5, $this->encabezado_pdf, 0, 0, 'C', 0);

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(2.5,.7, 'Fecha:','BL',0,'L');
        $this->Cell(3,.7, $this->contrato->fecha_format.' ','RB',0,'R');
        $this->Ln(.7);

        //Obtener Posiciones despues de los títulos
        $y_inicial = $this->getY() - 1;
        $x_inicial = $this->GetPageWidth() / 1.48;
        $this->setY($y_inicial);
        $this->setX($x_inicial);

        $this->Ln(.6);

        $this->SetWidths(array(19.5));
        $this->SetRounds(array('1234'));
        $this->SetRadius(array(0.1));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(1));
        $this->SetStyles(array('DF'));
        $this->SetAligns("L");
        $this->SetFont('Arial', 'B', 13);
        $this->setY($y_inicial+1.5);
        $this->Row(array(""));
        $this->setY($y_inicial+1.5);
        $this->setX(2);
        $this->MultiCell(16, 1, utf8_decode($this->obra->nombre_obra_formatos) , '', 'C');

        $this->Ln(.6);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(2.5, 1, utf8_decode('Referencia: ') , 0, 0, 'C', 0);

        $this->SetWidths(array(17));
        $this->SetRounds(array('1234'));
        $this->SetRadius(array(.1));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array('0.7'));
        $this->SetStyles(array('DF'));
        $this->SetFont('Arial', '', 10);
        $this->Row(array(""));
        $this->setY($y_inicial+3.25);
        $this->setX($x_inicial-13.5);
        $this->CellFit(2.5, 1, '', 0, 0, 'C', 0);
        $this->MultiCell(17, .5,  utf8_decode($this->contrato->referencia), '', 'L');

        $this->partidasTitle();

       $currentPage = $this->PageNo();
        if($currentPage>1){
            $this->Ln();
        }
    }

    public function partidasTitle()
    {
        $this->Ln();
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);

        $this->setXY(1, 5.5);

        $this->Cell((0.030 * $this->WidthTotal), 0.4, '#', 'LRBT', 0, 'C', 180);
        $this->Cell((0.1 * $this->WidthTotal), 0.4, 'Clave', 'LRBT', 0, 'C', 180);
        $this->Cell((0.38 * $this->WidthTotal), 0.4, utf8_decode('Descripción'), 'LRBT', 0, 'C', 180);
        $this->Cell((0.099 * $this->WidthTotal), 0.4, 'Unidad', 'LRBT', 0, 'C', 180);
        $this->Cell((0.099 * $this->WidthTotal), 0.4, 'Cantidad', 'LRBT', 0, 'C', 180);
        $this->Cell((0.29 * $this->WidthTotal), 0.4, 'Destino', 'LRBT', 0, 'C', 180);
        $w_t = $this->WidthTotal;
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([$w_t * 0.030, $w_t * 0.1, $w_t * 0.38, $w_t * 0.099, $w_t * 0.099, $w_t * 0.29]);
        $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '2']);
        $this->SetRadius([0, 0, 0, 0, 0, 0]);
        $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C', 'L', 'L', 'R', 'R', 'L']);
    }

    public function partidas()
    {
        $w_t = $this->WidthTotal;
        $this->Ln();
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([$w_t * 0.030, $w_t * 0.1, $w_t * 0.38, $w_t * 0.099, $w_t * 0.099, $w_t * 0.29]);
        $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '2']);
        $this->SetRadius([0, 0, 0, 0, 0, 0]);
        $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C', 'L', 'L', 'R', 'R', 'L']);

        foreach ($this->contrato->conceptos as $key => $c) {
            $this->Row([$key + 1,
                mb_strtoupper($c->clave),
                utf8_decode(mb_strtoupper($c->descripcion_guion_nivel_format)),
                $c->unidad != null ? $c->unidad : '',
                $c->cantidad_original != '0' ? number_format($c->cantidad_original, 2, ".", ",") : '-',
                $c->destino ? utf8_decode($c->destino->concepto->path_corta) : '']);

        }
    }

    function firmas()
    {
        /*Primeras Firmas*/
        $this->SetY(-6);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);

        $this->SetFont('Arial', 'B', 5);
        $this->Cell(3.5);
        $this->Cell(($this->GetPageWidth()) / 3.5, 0.4, '', 'TRLB', 0, 'C');
        $this->Cell(($this->GetPageWidth()) / 3.5, 0.4, '', 'TRLB', 0, 'C');

        $this->Ln();
        $this->Cell(3.5);
        $this->Cell(($this->GetPageWidth()) / 3.5, 1.5, '', 'TRLB', 0, 'C');
        $this->Cell(($this->GetPageWidth()) / 3.5, 1.5, '', 'TRLB', 0, 'C');

        $this->SetFont('Arial', 'B', 4);
        $this->Ln();
        $this->Cell(3.5);
        $this->Cell(($this->GetPageWidth()) / 3.5, 0.8, utf8_decode('  Acepta:'), 'TRLB', 0, 'L');
        $this->Cell(($this->GetPageWidth()) / 3.5, 0.8, utf8_decode('  Autoriza:'), 'TRLB', 0, 'L');

        /*Firmas Generales*/
        $this->SetY(-3);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);

        $this->SetFont('Arial', 'B', 5);
        $this->Cell(2);
        $this->Cell(($this->GetPageWidth()) / 5.5, 0.4, utf8_decode('Vo.Bo.'), 'TRLB', 0, 'C');
        $this->Cell(($this->GetPageWidth()) / 5.5, 0.4, utf8_decode('Vo.Bo.'), 'TRLB', 0, 'C');
        $this->Cell(($this->GetPageWidth()) / 5.5, 0.4, utf8_decode('Vo.Bo.'), 'TRLB', 0, 'C');
        $this->Cell(($this->GetPageWidth()) / 5.5, 0.4, utf8_decode('Vo.Bo.'), 'TRLB', 0, 'C');

        $this->Ln();
        $this->Cell(2);
        $this->Cell(($this->GetPageWidth()) / 5.5, 1.2, '', 'TRLB', 0, 'C');
        $this->Cell(($this->GetPageWidth()) / 5.5, 1.2, '', 'TRLB', 0, 'C');
        $this->Cell(($this->GetPageWidth()) / 5.5, 1.2, '', 'TRLB', 0, 'C');
        $this->Cell(($this->GetPageWidth()) / 5.5, 1.2, '', 'TRLB', 0, 'C');

        $this->SetFont('Arial', 'B', 5);
        $this->Ln();
        $this->Cell(2);
        $this->Cell(($this->GetPageWidth()) / 5.5, 0.4, utf8_decode('Jurídico Corporativo'), 'TRLB', 0, 'C');
        $this->Cell(($this->GetPageWidth()) / 5.5, 0.4, utf8_decode('Gerente de Subcontratos Corporativo'), 'TRLB', 0, 'C');
        $this->Cell(($this->GetPageWidth()) / 5.5, 0.4, utf8_decode('Gerente de Seguros y Fianzas'), 'TRLB', 0, 'C');
        $this->Cell(($this->GetPageWidth()) / 5.5, 0.4, utf8_decode('Gerente de Proyecto'), 'TRLB', 0, 'C');
    }

    public function Footer()
    {
        $this->firmas();

        //PAGINA Y LEYENDA
        $this->SetY(-0.9);
        $this->SetX(14.7);
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(10, .3, (''), 0, 1, 'L');

        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(10, .3, utf8_decode('Formato generado desde el Sistema de Contratos del SAO ERP. Fecha de registro: ') . $this->contrato->fecha_hora_registro_format.' Fecha de consulta: '.date("d/m/Y H:i:s"), 0, 0, 'L');
        $this->Cell(10, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create() {
        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,6.80);
        $this->partidas();

        try {
            $this->Output('I', "Formato - Contrato_Proyectado_".$this->contrato->numero_folio.".pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

}
