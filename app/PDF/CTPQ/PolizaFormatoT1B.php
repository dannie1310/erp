<?php


namespace App\PDF\CTPQ;


use App\Models\CTPQ\Parametro;
use App\Models\CTPQ\Poliza;
use DateInterval;
use DateTime;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\DB;

class PolizaFormatoT1B extends Rotation
{
    private $poliza;
    private $empresa;
    private $data;

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    private $suma_cargo = 0;
    private $suma_abono = 0;
    private $fecha;

    private $footer_encola = false;
    private $num = 1;
    private $key_folio = 0;

    public function __construct($data, $empresa)
    {
        parent::__construct('P', 'cm', 'Letter');
        $this->data = $data;
        $this->empresa = $empresa;
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
        $this->SetFont('Helvetica', 'I', 12);
        $this->Cell(0, 0, 'CONTPAQ i', 0, 0, 'L');
        $this->setXY(1 , 1.2);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($this->WidthTotal, 0, utf8_decode($this->empresa->Nombre), 0, 0, 'C');
        $this->setXY(18.50, 1.2);
        $this->Cell(0, 0, 'Hoja:   '.$this->PageNo(), 0, 0, 'L');
        $this->setXY(5.85, 1.6);
        $this->SetFont('Arial', 'B', 11.5);
        $this->Cell(0, 0, utf8_decode('Impreso de pólizas del ').date_format($this->fecha,"d/M/Y").' al '.date_format($this->fecha,"d/M/Y"), 0, 0, 'L');
        $this->setXY(16.6, 1.6);
        $this->Cell(0, 0, utf8_decode('Fecha: ').$this->poliza->fecha_consulta, 0, 0, 'L');

        //TODO: CAMBIAR EL HARCODEO DEL TIPO DE MONEDA
        $this->setXY(8.3, 2);
        $this->Cell(0, 0, utf8_decode('Moneda: Peso Mexicano'), 0, 0, 'L');

        $this->setXY(0.9, 2.3);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 0, utf8_decode('Dirección: ') . Parametro::getDireccion(), 0, 0, 'L');
        $this->setXY(16.6, 2.3);
        $this->Cell(0, 0, utf8_decode('Código postal: ') . Parametro::getCodPostal(), 0, 0, 'L');

        $this->setXY(0.9, 2.6);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 0, utf8_decode('Reg. Fed.: '). Parametro::getRFC(), 0, 0, 'L');
        $this->setXY(5.83, 2.6);
        $this->Cell(0, 0, utf8_decode('Reg. Cámara: ') . Parametro::getRegCamara(), 0, 0, 'L');
        $this->setXY(12.7, 2.6);
        $this->Cell(0, 0, utf8_decode('Cta. Estatal: ') . Parametro::getRegEstatal(), 0, 0, 'L');

        $this->partidasTitle();
    }

    public function partidasTitle()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);

        $this->setXY(1, 2.82);
        $this->cell($this->WidthTotal,0.1, '', 'B', 0, 'L', 180);
        $this->setXY(0.90, 2.96);
        
        $this->Cell(2.35,0.5, 'Fecha', '', 0, 'L', 180);
        $this->Cell(2.8,0.5, 'Tipo', '', 0, 'L', 180);
        $this->Cell(1.8,0.5, utf8_decode('Número'), '', 0, 'L', 180);
        $this->Cell(7.8,0.5, 'C o n c e p t o', '', 0, 'C', 180);
        $this->Cell(2.6, 0.5, 'Clase', '', 0, 'C', 180);
        $this->Cell(2.27, 0.5, 'Diario', '', 0, 'L', 180);

        $this->setXY(0.90, 3.45);
        
        $this->Cell(1.2,0.3, 'No.', '', 0, 'L', 180);
        $this->Cell(2.3,0.3, 'Refer.', '', 0, 'L', 180);
        $this->Cell(3.1,0.3, 'C u e n t a', '', 0, 'L', 180);
        $this->Cell(5.7,0.3, 'N o m b r e', '', 0, 'L', 180);
        $this->Cell(1.5, 0.3, 'Diario', '', 0, 'L', 180);
        $this->Cell(1.1, 0.3, 'Seg.', '', 0, 'L', 180);
        $this->Cell(2.26, 0.3, 'C a r g o s', '', 0, 'R', 180);
        $this->Cell(2.6, 0.3, 'A b o n o s', '', 0, 'R', 180);

        $this->setXY(1, 3.9);
        $this->cell($this->WidthTotal,0.1, '', 'T', 0, 'L', 180);

        $this->Ln();
    }

    public function partidas()
    {
        $this->SetFont('Arial', '', 10);
        $this->SetFillColor(255, 255, 255);
        $this->Cell(2.35,0.5, $this->poliza->fecha_mes_letra_format, '', 0, 'L', 180);
        $this->Cell(2.6,0.5, $this->poliza->tipo_poliza->Nombre, '', 0, 'L', 180);
        $this->Cell(1.5,0.5, $this->poliza->Folio, '', 0, 'R', 180);
        $this->Cell(7.8,0.5, strlen($this->poliza->Concepto) > 32 ? '' . utf8_decode(substr($this->poliza->Concepto, 0, 32)) . '' : utf8_decode($this->poliza->Concepto), '', 0, 'L', 180);
        $this->Cell(2.6, 0.5, '', '', 0, 'C', 180);
        $this->Cell(2.27, 0.5, '', '', 0, 'L', 180);
        $this->Ln(0.48);
        $this->SetX(1);
        $this->poliza_encola = $this->poliza;
        $this->suma_abono = 0;
        $this->suma_cargo = 0;
        $count = 1;

        foreach($this->poliza->movimientos as $movimiento){

            $this->SetFont('Arial', '', 10);
            $this->SetFillColor(255, 255, 255);

            $this->Cell(1.2,0.3, $count, '', 0, 'R', 180);
            $this->Cell(2.3,0.3, strlen($movimiento->Referencia) > 11 ? utf8_decode(substr($movimiento->Referencia, 1, 10)) . ' ..' : utf8_decode($movimiento->Referencia), '', 0, 'L', 180);
            $this->Cell(3.1,0.3, $movimiento->cuenta->cuenta_format, '', 0, 'L', 180);
            $this->Cell(7.2,0.3, strlen($movimiento->cuenta->Nombre) > 27 ? utf8_decode(substr($movimiento->cuenta->Nombre, 0, 26)) . '..' : utf8_decode($movimiento->cuenta->Nombre), '', 0, 'L', 180);
            $this->Cell(1.1, 0.3, '', '', 0, 'L', 180);
            $this->Cell(2.26, 0.3, $movimiento->TipoMovto == 0 ? \number_format($movimiento->Importe,2) : '', '', 0, 'R', 180);
            $this->Cell(2.6, 0.3, $movimiento->TipoMovto == 1 ? \number_format($movimiento->Importe,2) : '', '', 0, 'R', 180);
            
            $this->Ln(0.45);
            $this->SetFont('Arial', '', 10);

            $this->Cell(1.4,0.3, '', '', 0, 'R', 180);
            $this->Cell(1.9,0.3, '', '', 0, 'L', 180);
            $this->Cell(3.3,0.3, '', '', 0, 'L', 180);
            $this->Cell(7.2,0.3,  strlen($movimiento->Concepto) > 27 ? '' . utf8_decode(substr($movimiento->Concepto, 0, 26)) . '..' : utf8_decode($movimiento->Concepto), '', 0, 'L', 180);
            $this->Cell(1.1, 0.3, '', '', 0, 'L', 180);
            $this->Cell(2.26, 0.3, '', '', 0, 'R', 180);
            $this->Cell(2.6, 0.3,  '', '', 0, 'R', 180);
            
            $this->Ln(0.4);
            $movimiento->TipoMovto == 0 ? $this->suma_cargo += $movimiento->Importe:'';
            $movimiento->TipoMovto == 1 ? $this->suma_abono += $movimiento->Importe:'';
            $count++;
        }
        $this->cell($this->WidthTotal-5,0.1, '', '', 0, 'L', 180);
        $this->cell(5,0.1, '', 'B', 0, 'L', 180);
        $this->Ln(0.4);
        $this->cell($this->WidthTotal-7.1,0.3, '', '', 0, 'L', 180);
        $this->cell(2,0.3, utf8_decode('Total póliza :'), '', 0, 'L', 180);
        $this->cell(2.6,0.3, \number_format($this->suma_cargo, 2), '', 0, 'R', 180);
        $this->cell(2.6,0.3, \number_format($this->suma_abono, 2), '', 0, 'R', 180);
        $this->Ln(0.4);
        
        $this->SetLineWidth(0.04);
        $this->cell($this->WidthTotal- 2.5,0.1, '', '', 0, 'R', 180);
        $this->cell(2.5,0.1, '', 'B', 0, 'R', 180);
        $this->Ln(0.3);
        $this->cell($this->WidthTotal-5.1,0.3, '', '', 0, 'L', 180);
        $this->cell(2.6,0.3, 'Total CFD/CFDI :', '', 0, 'R', 180);
        $this->cell(2.6,0.3, 0, '', 0, 'R', 180);
        $this->Ln(0.4);
        $this->cell($this->WidthTotal-5.1,0.3, '', '', 0, 'L', 180);
        $this->cell(2.6,0.3, 'Total Comp. Ext..', '', 0, 'R', 180);
        $this->cell(2.6,0.3, 0, '', 0, 'R', 180);


        
    }

    function create() {
        DB::purge('cntpq');
        \Config::set('database.connections.cntpq.database',$this->empresa->AliasBDD);
        $this->poliza = Poliza::find($this->data->Id);
        $this->fecha = date_create($this->poliza->Fecha);

        
        
        $this->SetMargins(1, 0.9, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,5);
        $this->partidas();

        try {
            $this->Output('I', "Formato - poliza.pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }
}