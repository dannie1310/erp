<?php


namespace App\PDF\CTPQ;


use App\Models\CTPQ\Parametro;
use App\Models\CTPQ\Poliza;
use DateInterval;
use DateTime;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\DB;

class PolizaFormatoT1A extends Rotation
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
    private $suma_cfdi = 0;
    private $mes;
    private $anio;

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
        $this->SetFont('Helvetica', 'BI', 12);
        $this->Cell(0, 0, 'CONTPAQ i', 0, 0, 'L');
        $this->setXY(4, 1.2);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(5.76, 0, strlen($this->empresa->Nombre) > 51 ? utf8_decode(substr($this->empresa->Nombre, 0, 50)) : utf8_decode($this->empresa->Nombre), 0, 0, 'L');
        $this->setXY(18.50, 1.2);
        if($this->key_folio == 0)
        {
            $this->Cell(0, 0, 'Hoja:   '.$this->PageNo(), 0, 0, 'L');
        }else{
            $this->Cell(0, 0, 'Hoja:   '.($this->PageNo() - $this->num), 0, 0, 'L');
        }

        $this->setXY(5.83, 1.6);
        $this->SetFont('Arial', 'B', 11.5);
        $this->Cell(0, 0, utf8_decode('Impreso de pólizas del ').'01/'.$this->mes.'/'.$this->anio.' al 30/'.$this->mes.'/'.$this->anio, 0, 0, 'L');
        $this->setXY(16.6, 1.6);

        $this->Cell(0, 0, utf8_decode('Fecha: ').$this->poliza->fecha_consulta, 0, 0, 'L');

        //TODO: CAMBIAR EL HARCODEO DEL TIPO DE MONEDA
        $this->setXY(8.3, 2);
        $this->Cell(0, 0, utf8_decode('Moneda: Peso Mexicano'), 0, 0, 'L');

        $this->setXY(0.9, 2.3);
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 0, utf8_decode('Dirección: ') . Parametro::getDireccion(), 0, 0, 'L');
        $this->setXY(17, 2.3);
        $this->Cell(0, 0, utf8_decode('Código postal: ') . Parametro::getCodPostal(), 0, 0, 'L');

        $this->setXY(0.9, 2.6);
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 0, utf8_decode('Reg. Fed.: '). Parametro::getRFC(), 0, 0, 'L');
        $this->setXY(5.83, 2.6);
        $this->Cell(0, 0, utf8_decode('Reg. Cámara: ') . Parametro::getRegCamara(), 0, 0, 'L');
        $this->setXY(12.9, 2.6);
        $this->Cell(0, 0, utf8_decode('Cta.Estatal: ') . Parametro::getRegEstatal(), 0, 0, 'L');

        $this->partidasTitle();
    }

    public function partidasTitle()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);

        $this->setXY(1, 2.82);

        $this->Cell(3.1,0.6, 'Cuenta', 'BT', 0, 'L', 180);
        $this->Cell(5.2,0.6, 'Nombre', 'BT', 0, 'L', 180);
        $this->Cell(4,0.6, 'Referencia', 'BT', 0, 'L', 180);
        $this->Cell(2.5,0.6, 'Parcial', 'BT', 0, 'C', 180);
        $this->Cell(2.5, 0.6, 'Cargos', 'BT', 0, 'C', 180);
        $this->Cell(2.29, 0.6, 'Abonos', 'BT', 0, 'C', 180);

        $this->Ln();
    }

    public function partidas()
    {
        $this->SetFont('Arial', '', 10);
        $this->SetFillColor(255, 255, 255);
        $this->Cell(19.65, 0.5, utf8_decode('Póliza de ' . $this->poliza->tipo_poliza->Nombre . ' número ' . $this->poliza->Folio . ' correspondiente al ').$this->poliza->fecha_mes_letra_format, '', 0, 'C', 0);
        $this->Ln(0.4);
        $this->Cell(19.65, 0.5, utf8_decode($this->poliza->Concepto), '', 0, 'C', 0);
        $this->Ln(0.48);
        $this->SetX(1);
        $this->poliza_encola = $this->poliza;
        $this->suma_abono = 0;
        $this->suma_cargo = 0;

        foreach($this->poliza->cuentas_padres as $cuenta_padre){
            $suma_cargos = number_format($this->poliza->sumaMismoPadreCargos($cuenta_padre), 2, ".", ",");
            $suma_abonos = number_format($this->poliza->sumaMismoPadreAbonos($cuenta_padre), 2, ".", ",");
            $movimiento = $this->poliza->getPrimerMovimiento($cuenta_padre);

            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(255, 255, 255);
            $this->Cell(3.1, 0.5, $cuenta_padre->cuenta_format, '', 0, 'L', 180);
            $this->Cell(5.2, 0.5, strlen($cuenta_padre->Nombre) > 25 ? utf8_decode(substr($cuenta_padre->Nombre, 0, 25)) . '..' : utf8_decode($cuenta_padre->Nombre), '', 0, 'L', 180);
            $this->Cell(4, 0.5, '', '', 0, 'L', 180);
            $this->Cell(2.5, 0.5, '', '', 0, 'L', 180);
            $this->Cell(2.5, 0.5, $suma_cargos != 0 ? $suma_cargos : '', '', 0, 'R', 180);
            $this->Cell(2.29, 0.5,$suma_abonos != 0 ? $suma_abonos : '', '', 0, 'R', 180);
            $this->Ln(0.45);
            $this->Cell(3.1, 0.3, '', '', 0, 'L', 180);
            $this->Cell(5.2, 0.3, strlen($movimiento->Concepto) > 23 ? '  ' . utf8_decode(substr($movimiento->Concepto, 0, 22)) . '..' : '  ' . utf8_decode($movimiento->Concepto), '', 1, 'L', 180);

            foreach ($this->poliza->getMovimientos($cuenta_padre) as $k => $movimiento)
            {
                $this->SetFont('Arial', '', 10);
                $this->Cell(3.1, 0.5, $movimiento->cuenta->cuenta_format, '', 0, 'L', 180);
                $this->Cell(5.2, 0.5, strlen($movimiento->cuenta->Nombre) > 25 ? utf8_decode(substr($movimiento->cuenta->Nombre, 0, 25)) . '..' : utf8_decode($movimiento->cuenta->Nombre), '', 0, 'L', 180);
                $this->Cell(4, 0.5, strlen($movimiento->Referencia) > 11 ? utf8_decode(substr($movimiento->Referencia, 0, 9)) . ' ..' : utf8_decode($movimiento->Referencia), '', 0, 'L', 180);
                $this->Cell(2.5, 0.5, $movimiento->importe_coma_format, '', 0, 'R', 180);
                $this->Cell(2.5, 0.5, '', '', 0, 'L', 180);
                $this->Cell(2.29, 0.5, '', '', 0, 'L', 180);
                $this->Ln(0.4);
                $this->Cell(3.1, 0.3, '', '', 0, 'L', 180);
                $this->Cell(5.2, 0.3, strlen($movimiento->Concepto) > 23 ? '  ' . utf8_decode(substr($movimiento->Concepto, 0, 22)) . ' ..' : utf8_decode($movimiento->Concepto), '', 1, 'L', 180);
                $this->suma_abono += $movimiento->abono;
                $this->suma_cargo += $movimiento->cargo;
            }
        }
        $this->footer_encola = true;
    }

    public function Footer()
    {
        if($this->footer_encola) {
            $this->Ln();
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(255, 255, 255);

            $this->setXY(1, 24.5);
            $this->Cell(12.98, 0.6, strlen($this->poliza_encola->Concepto) > 63 ? utf8_decode(substr($this->poliza_encola->Concepto, 0, 63)) . '..' : utf8_decode($this->poliza_encola->Concepto), 'T', 0, 'L', 180);
            $this->setXY(14.15, 24.5);
            $this->Cell(3, 0.6, number_format($this->suma_cargo, 2, ".", ","), 'T', 0, 'R', 180);
            $this->setXY(17.3, 24.5);
            $this->Cell(3, 0.6, number_format($this->suma_abono, 2, ".", ","), 'T', 0, 'R', 180);

            $this->setXY(1, 25.4);
            $this->Cell(4.2, 0.6, utf8_decode('Elaboró'), 'T', 0, 'C', 180);
            $this->setXY(5.35, 25.4);
            $this->Cell(4.3, 0.6, utf8_decode('Revisó'), 'T', 0, 'C', 180);
            $this->setXY(9.8, 25.4);
            $this->Cell(4.2, 0.6, utf8_decode('Autorizó'), 'T', 0, 'C', 180);
            $this->setXY(14.15, 25.4);
            $this->Cell(3, 0.5, 'Origen', 'T', 0, 'L', 180);
            $this->setXY(17.3, 25.4);
            $this->Cell(3, 0.6, utf8_decode('Póliza'), 'T', 0, 'C', 180);

            $this->SetFont('Arial', '', 10);
            $this->setXY(14.15, 25.85);
            $this->Cell(3, 0.3, 'CONTPAQ i', '', 0, 'L', 180);

            $this->setXY(17.3, 26.2);
            $this->Cell(3, 0.5, $this->poliza_encola->tipo_poliza->Nombre . ' # ' . $this->poliza_encola->Folio, '', 0, 'R', 180);
            $this->setXY(17.3, 26.6);
            $this->Cell(3, 0.5, $this->poliza_encola->fecha_mes_letra_format, '', 0, 'R', 180);
            $this->footer_encola = false;
            $this->num = $this->PageNo();
        }
    }

    public function cfdi()
    {
        if($this->data->cfdi->toArray() != [])
        {
            $this->ln(1);
            $this->setXY(1, $this->getY());
            $this->SetFont('Arial', '', 10);
            $this->Cell(20, 0.5, utf8_decode('CFD/CFDI ASOCIADOS A LA PÓLIZA'), '', 0, 'L', 180);
            $this->cfdiAsociadoTitulos();
        }
    }

    public function cfdiAsociadoTitulos()
    {
        $this->ln();
        $this->setXY(1, $this->getY());
        $this->SetFont('Arial', '', 9);
        $this->Cell(20, 0.5, utf8_decode('Emisión'), 'B', 0, 'L',180);
        $this->setXY(3, $this->getY());
        $this->Cell(1, 0.5, utf8_decode('Tipo'), 0, 0, 'C');
        $this->setXY(4.3, $this->getY());
        $this->Cell(1, 0.5, utf8_decode('Serie'), 0, 0, 'C');
        $this->setXY(5.2, $this->getY());
        $this->Cell(1, 0.5, utf8_decode('Folio'), 0, 0, 'C');
        $this->setXY(9, $this->getY());
        $this->Cell(1, 0.5, utf8_decode('UUID'), 0, 0, 'C');
        $this->setXY(13, $this->getY());
        $this->Cell(1, 0.5, utf8_decode('RFC'), 0, 0, 'C');
        $this->setXY(16, $this->getY());
        $this->Cell(1, 0.5, utf8_decode('Razón Social'), 0, 0, 'C');
        $this->setXY(19, $this->getY());
        $this->Cell(1, 0.5, utf8_decode('Total'), 0, 0, 'C');
        $this->cfdipartidas();
    }

    public function cfdipartidas()
    {
        $this->SetFillColor(255, 255, 255);
        $this->suma_cfdi = 0;
        $this->ln(0.6);

        foreach ($this->data->cfdi as $cfdi) {
            $this->SetFont('Arial', '', 9);
            $this->SetFillColor(255, 255, 255);
            $this->setXY(1, $this->getY());
            $this->Cell(1.9, 0.5, $cfdi->fecha_sencilla_format, '', 0, 'L', 180);
            $this->setXY(3, $this->getY());
            $this->Cell(1.3, 0.5, $cfdi->tipo_descripcion, '', 0, 'L');
            $this->setXY(4.3, $this->getY());
            $this->Cell(0.6, 0.5, $cfdi->serie, '', 0, 'L');
            $this->setXY(5, $this->getY());
            $this->Cell(1.8, 0.5, $cfdi->folio, '', 0, 'L');
            $this->setXY(6.8, $this->getY()); // 33 + ..
            if (strlen($cfdi->uuid) > 27) {
                $uuid = substr($cfdi->uuid, 0, 27);
            } else {
                $uuid = $cfdi->uuid;
            }
            $this->Cell(5.6, 0.5, $uuid . '..', '', 0, 'L');
            $this->setXY(12.4, $this->getY());
            $this->Cell(2.6, 0.5, $cfdi->rfc_receptor, '', 0, 'L');
            $this->setXY(15, $this->getY());//14 ..
            if (strlen($cfdi->proveedor->razon_social) > 12) {
                $ra = substr($cfdi->proveedor->razon_social, 0, 12);
            } else {
                $ra = $cfdi->proveedor->razon_social;
            }
            $this->Cell(3, 0.5, $ra . '..', '', 0, 'L');
            $this->setXY(18, $this->getY());
            $this->Cell(3, 0.5, number_format($cfdi->total, 2, ".", ","), '', 0, 'R');
            $this->suma_cfdi = $this->suma_cfdi + $cfdi->total;
        }
        $this->ln(0.5);
        $this->setXY(15, $this->getY());
        $this->Cell(3, 0.5, 'Total CFD/CFDI :', '', 0, 'R');
        $this->setXY(18, $this->getY());
        $this->Cell(3, 0.5, number_format($this->suma_cfdi, 2, ".", ","), 'T', 0, 'R');
        $this->ln(0.5);
        $this->setXY(15, $this->getY());
        $this->Cell(3, 0.5, 'Total Comp. Ext :', '', 0, 'R');
        $this->setXY(18, $this->getY());
        $this->Cell(3, 0.5, number_format($this->suma_abono-$this->suma_cfdi, 2, ".", ","), '', 0, 'R');

    }

    function create($path = '') {
        DB::purge('cntpq');
        \Config::set('database.connections.cntpq.database',$this->empresa->AliasBDD);
        $this->poliza = Poliza::find($this->data->Id);
        $this->fecha = date_create($this->poliza->Fecha);
        $this->mes = substr($this->poliza->fecha_mes_letra_format, 3,3);
        $this->anio = substr($this->poliza->fecha_mes_letra_format, 7,4);
        $this->SetMargins(1, 0.9, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,5);
        $this->partidas();
        $this->cfdi();

        try {
            if($path != ''){
                return $this->Output('F', $path . "/Formato - Ejercicio ".$this->poliza->Ejercicio." - Periodo ".$this->poliza->Periodo." - Tipo ".$this->poliza->tipo_poliza->Nombre." - Folio ".$this->poliza->Folio .".pdf", 1);
            }
            $this->Output('I', "Formato - poliza.pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

}
