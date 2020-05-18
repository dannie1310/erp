<?php


namespace App\PDF\CTPQ;


use App\Models\CTPQ\Poliza;
use Ghidev\Fpdf\Rotation;

class PolizaFormato extends Rotation
{
    private $poliza;
    private $encabezado_pdf = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct($poliza)
    {
        parent::__construct('P', 'cm', 'Letter');
     //   $this->obra = Obra::find(Context::getIdObra());
       // $this->poliza = $poliza;

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
        $this->SetTextColor('0', '0', '255');
        $this->SetFont('Helvetica', 'BI', 12);
        $this->Cell(0, 0, 'CONTPAQ i', 0, 0, 'L');
        $this->setXY(5.76, 1.2);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 0, 'LA PENINSULAR (098 RESIDENCIAL EL TEMPLO)', 0, 0, 'L');
        $this->setXY(18.50, 1.2);
        $this->Cell(0, 0, 'Hoja:   1', 0, 0, 'L');

        $this->setXY(5.83, 1.6);
        $this->SetFont('Arial', 'B', 11.5);
        $this->Cell(0, 0, utf8_decode('Impreso de pólizas del 01/Nov/2019 al 30/Nov/2019'), 0, 0, 'L');
        $this->setXY(16.6, 1.6);
        $this->Cell(0, 0, utf8_decode('Fecha: 08/May/2020'), 0, 0, 'L');

        $this->setXY(8.3, 2);
        $this->Cell(0, 0, utf8_decode('Moneda: Peso Mexicano'), 0, 0, 'L');

        $this->setXY(0.9, 2.3);
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 0, utf8_decode('Dirección:'), 0, 0, 'L');
        $this->setXY(17, 2.3);
        $this->Cell(0, 0, utf8_decode('Código postal:'), 0, 0, 'L');

        $this->setXY(0.9, 2.6);
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 0, utf8_decode('Reg. Fed.: PC0811231EI4'), 0, 0, 'L');
        $this->setXY(5.83, 2.6);
        $this->Cell(0, 0, utf8_decode('Reg. Cámara:'), 0, 0, 'L');
        $this->setXY(12.9, 2.6);
        $this->Cell(0, 0, utf8_decode('Cta.Estatal:'), 0, 0, 'L');

        $this->partidasTitle();

        $currentPage = $this->PageNo();
        if ($currentPage > 1) {
            $this->Ln();
        }
    }

    public function partidasTitle()
    {
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);

        $this->setXY(0.9, 2.82);

        $this->Cell(3.1,0.6, 'Cuenta', 'BT', 0, 'L', 180);
        $this->Cell(5.2,0.6, 'Nombre', 'BT', 0, 'L', 180);
        $this->Cell(4.65,0.6, 'Referencia', 'BT', 0, 'L', 180);
        $this->Cell(2.5,0.6, 'Parcial', 'BT', 0, 'L', 180);
        $this->Cell(2.5, 0.6, 'Cargos', 'BT', 0, 'L', 180);
        $this->Cell(2, 0.6, 'Abonos', 'BT', 0, 'C', 180);
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

        /*foreach ($this->contrato->conceptos as $key => $c) {
            $this->Row([$key + 1,
                mb_strtoupper($c->clave),
                mb_strtoupper(utf8_decode($c->descripcion_guion_nivel_format)),
                $c->unidad != null ? $c->unidad : '',
                $c->cantidad_original != '0' ? number_format($c->cantidad_original, 2, ".", ",") : '-',
                $c->destino ? utf8_decode($c->destino->ruta_destino) : '']);

        }*/
    }

    public function Footer()
    {
        //PAGINA Y LEYENDA
        $this->SetY(-0.9);
        $this->SetX(14.7);
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(10, .3, (''), 0, 1, 'L');

        $this->SetFont('Arial', 'BI', 6);
       // $this->Cell(10, .3, utf8_decode('Formato generado desde el sistema de contratos. Fecha de registro: ' . date("d-m-Y", strtotime($this->contrato->fecha_hora_registro_format))).' Fecha de consulta: '.date("d-m-Y H:i:s"), 0, 0, 'L');
        $this->Cell(10, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create() {
        $this->SetMargins(1, 0.9, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,6.80);
        $this->partidas();

        try {
            //$this->Output('I', "Formato - poliza_".$this->contrato->numero_folio.".pdf", 1);
            $this->Output('I', "Formato - poliza.pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

}
