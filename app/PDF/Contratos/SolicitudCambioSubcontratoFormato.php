<?php


namespace App\PDF\Contratos;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\SolicitudCambioSubcontrato;
use Carbon\Carbon;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;


class SolicitudCambioSubcontratoFormato extends Rotation
{
    protected $obra;
    protected $solicitud;
    private $encabezado_pdf = '';
    protected $extgstates = array();

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     * SolicitudCambioSubcontratoFormato constructor.
     * @param $id
     */
    public function __construct($id)
    {
        parent::__construct('L', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());
        $this->encabezado_pdf = utf8_decode($this->obra->facturar);
        $this->solicitud = SolicitudCambioSubcontrato::find($id);
        $this->SetAutoPageBreak(true, 3.5);
        $this->fecha = Carbon::parse($this->solicitud->fecha)->format('d-m-Y');
        $this->WidthTotal = $this->GetPageWidth() - 2;
        $this->encola = '';
    }

    function logo()
    {
        $data = $this->obra->getLogoAttribute();
        $data = pack('H*', hex2bin($data));
        $file = public_path('/img/logo_temp.png');
        if (file_put_contents($file, $data) !== false) {
            list($width, $height) = $this->resizeToFit($file);
            $this->Image($file, 1, 1, $width-2, $height-1);
            unlink($file);
        }
    }

    function Header()
    {
        $this->logo();

        $this->setXY(4.5, 1);
        $this->SetFont('Arial', 'B', 12);
        $this->MultiCell(13.5, 0.9, $this->encabezado_pdf, '', 'C');
        $this->setXY(7, 2);
        $this->Cell(1* $this->WidthTotal, 0.1,  utf8_decode("Solicitud de Cambio a Subcontrato"), '', 'CB');


        $y_inicial = $this->getY() - 1.5;
        $y_inicial = 1;
        $x_inicial = $this->GetPageWidth() / 1.51;
        $this->setY($y_inicial);
        $this->setX($x_inicial);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0.120 * $this->WidthTotal, 0.5, utf8_decode('Folio:'), 'LT, LR, LB', 0, 'L');
        $this->Cell(0.205 * $this->WidthTotal, 0.5, utf8_decode($this->solicitud->numero_folio_format), 'RT, RB', 1, 'R');

        $this->SetX($x_inicial);
        $this->SetFont('Arial', '',  9);
        $this->Cell(0.12 * $this->WidthTotal, 0.5, utf8_decode('Fecha:'), 'LB, LR', 0, 'L');
        $this->Cell(0.205 * $this->WidthTotal, 0.5,utf8_decode($this->solicitud->fecha_format), 'RB', 1, 'R');

        $this->SetX($x_inicial);
        $this->Cell(0.12 * $this->WidthTotal, 0.5, utf8_decode('Estado:'), 'LB, LR', 0, 'L');
        $this->Cell(0.205 * $this->WidthTotal, 0.5,utf8_decode($this->solicitud->estado_descripcion), 'RB', 1, 'R');

        $this->SetFont('Arial', 'B', 9);
        $this->setY(2.8);
        $this->Cell(0.280 * $this->WidthTotal, 0.5, utf8_decode('Organización:'), 'LRTB', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.720 * $this->WidthTotal, 0.5,utf8_decode($this->obra->nombre), 'LRTB', 1, 'C');

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0.280 * $this->WidthTotal, 0.5, utf8_decode('Contratista:'), 'LB, LR, LT', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.720* $this->WidthTotal, 0.5,utf8_decode($this->solicitud->empresa->razon_social), 'RB, LT', 1, 'C');

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0.280 * $this->WidthTotal, 0.5, utf8_decode('No. de Contrato:'), 'LB, LR, LT', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.720 * $this->WidthTotal, 0.5,$this->solicitud->subcontrato->contratoProyectado->numero_folio_format .' ('.$this->solicitud->subcontrato->contratoProyectado->referencia.').', 'RB, LT', 1, 'C');


        if ($this->encola == 'partidas')
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
        $this->Ln(0.5);
       // $this->SetFills(180,180,180);
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180,180,180);
        $this->Cell(0.020 * $this->WidthTotal,0.8,'#','RTLB',0,'C',180);
        $this->Cell(0.055 * $this->WidthTotal,0.8,'Tipo','RTLB',0,'C',180);
        $this->Cell(0.045 * $this->WidthTotal,0.8,'Clave','RTLB',0,'C',180);
        $this->Cell(0.170 * $this->WidthTotal,0.8,'Concepto','RTLB',0,'C',180);
        $this->Cell(0.045 * $this->WidthTotal,0.8,'Unidad','RTLB',0,'C',180);
        $this->Cell(0.122 * $this->WidthTotal,0.4,'Contratado','BTLR',0,'C',180);
        $this->Cell(0.122 * $this->WidthTotal,0.4,"Avance",'BTLR',0,'C',180);
        $this->Cell(0.122 * $this->WidthTotal,0.4,"Saldo",'BTLR',0,'C',180);
        $this->Cell(0.142 * $this->WidthTotal,0.4,"Addendum",'BTLR',0,'C',180);
        $this->Cell(0.162 * $this->WidthTotal,0.4,utf8_decode("Distribución"),'BTLR',0,'C',180);

        $this->setXY(1+$this->WidthTotal * 0.335,5.2);
        $this->Cell((0.080 * $this->WidthTotal),0.4,'Volumen','LRBT',0,'C',180);
        $this->Cell((0.042 * $this->WidthTotal),0.4,'P.U.','LRBT',0,'C',180);

        $this->Cell((0.061 * $this->WidthTotal),0.4,'Volumen','LRBT',0,'C',180);
        $this->Cell((0.061 * $this->WidthTotal),0.4,'Importe','LRBT',0,'C',180);

        $this->Cell((0.061 * $this->WidthTotal),0.4,'Cantidad','LRBT',0,'C',180);
        $this->Cell((0.061 * $this->WidthTotal),0.4,'Importe','LRBT',0,'C',180);

        $this->Cell((0.0473 * $this->WidthTotal),0.4,'Volumen','LRBT',0,'C',180);
        $this->Cell((0.0473 * $this->WidthTotal),0.4,'P.U.','LRBT',0,'C',180);
        $this->Cell((0.0473 * $this->WidthTotal),0.4,'Importe','LRBT',0,'C',180);

        $this->Cell((0.162 * $this->WidthTotal),0.4,'Destino en Presupuesto','LRBT',0,'C',180);
    }

    public function partidas()
    {
        $this->tableHeader();
        $w_t = $this->WidthTotal;
        $this->Ln();

        $this->encola = 'partidas';
        $this->setEstilo("partidas");
        $i = 1;

        foreach ($this->solicitud->partidas as $key => $p) {
            if($p->tiene_hijos==0){
                if($p->id_tipo_modificacion == 4){
                    $this->Row([
                        $i,
                        $p->tipo->descripcion,
                        $p->clave,
                        $p->descripcion,
                        $p->unidad,
                        '-',
                        '-',
                        '-',
                        '-',
                        '-',
                        '-',
                        $p->cantidad_format,
                        $p->precio_format,
                        $p->importe_format,
                        $p->itemSubcontrato ? $p->itemSubcontrato->concepto_path_corta : $p->concepto_path_corta
                    ]);
                } else {
                    $this->Row([
                        $i,
                        $p->tipo->descripcion,
                        $p->itemSubcontrato ? $p->itemSubcontrato->contrato->clave: '',
                        $p->itemSubcontrato ? utf8_decode($p->itemSubcontrato->contrato->descripcion) : '',
                        $p->itemSubcontrato ? $p->itemSubcontrato->contrato->unidad : $p->unidad,
                        $p->itemSubcontrato ? $p->itemSubcontrato->cantidad_format : '-',
                        $p->itemSubcontrato ? $p->itemSubcontrato->precio_unitario_format : '-',
                        $p->itemSubcontrato ? $p->itemSubcontrato->cantidad_estimada_format : '-',
                        $p->itemSubcontrato ? $p->itemSubcontrato->importe_estimado_format : '-',
                        $p->itemSubcontrato ? $p->itemSubcontrato->cantidad_saldo_format : '-',
                        $p->itemSubcontrato ? $p->itemSubcontrato->importe_saldo_format : '-',
                        $p->cantidad_format,
                        $p->precio_format,
                        $p->importe_format,
                        $p->itemSubcontrato ? $p->itemSubcontrato->concepto_path_corta : $p->concepto_path_corta
                    ]);
                }
                $i++;
            }
        }
        $this->encola = '';
        /*Observaciones*/
        if (!is_null($this->solicitud->observaciones)) {
            $this->Ln(.5);
            $this->SetWidths(array($this->WidthTotal));
            $this->SetRounds(array('12'));
            $this->SetRadius(array(0));
            $this->SetFills(array('180,180,180'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetStyles(array('DF'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 6);
            $this->SetAligns(array('C'));
            $this->Row(array("Observaciones"));
            $this->SetRounds(array('34'));
            $this->SetRadius(array(0));
            $this->SetAligns(array('C'));
            $this->SetStyles(array('DF'));
            $this->SetFills(array('255,255,255'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 6);
            $this->SetAligns(array('J'));
            $this->Row(array(utf8_decode(str_replace(array("\r", "\n"), '', "" . $this->solicitud->observaciones))));
        }
        $this->SetY($this->GetY());
    }

    function setEstilo($tipo){
        $w_t = $this->WidthTotal;
        switch ($tipo){
            case "partidas":
                $this->SetFont('Arial', '', 5);
                $this->SetFillColor(180, 180, 180);
                $this->SetWidths([$w_t * 0.020,$w_t * 0.055,$w_t * 0.045, $w_t * 0.170, $w_t * 0.045, $w_t * 0.080, $w_t * 0.042, $w_t * 0.061, $w_t * 0.061, $w_t * 0.061, $w_t * 0.061, $w_t * 0.0473, $w_t * 0.0473, $w_t * 0.0473, $w_t * 0.162]);
                $this->SetStyles(['DF', 'DF', 'DF','DF', 'DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF', 'DF', 'FD', 'FD', 'DF']);
                $this->SetRounds(['1', '', '', '', '', '','', '', '', '', '', '', '', '', '2']);
                $this->SetRadius([0, 0, 0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0, 0, 0]);
                $this->SetFills(['255,255,55', '255,255,55', '255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
                $this->SetHeights([0.4]);
                $this->SetAligns(['L','L','L', 'L', 'C', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'L']);
                break;
        }
    }

    function totales() {
        $this->setXY(21.5, $this->GetY());
        $this->SetFont('Arial', '', 6);
        $this->Cell(0.105 * $this->WidthTotal, 0.4, utf8_decode('Subtotal:'), '', 0, 'L');

        $this->Cell(0.107 * $this->WidthTotal, 0.4, $this->solicitud->subtotal_format, '', 1, 'R');

        $this->setXY(21.5, $this->GetY());

        $this->Cell(0.105 * $this->WidthTotal, 0.4, utf8_decode('IVA:'), '', 0, 'L');
        $this->Cell(0.107 * $this->WidthTotal, 0.4, $this->solicitud->impuesto_format, '', 1, 'R');

        $this->setXY(21.5, $this->GetY());

        $this->Cell(0.105 * $this->WidthTotal, 0.4, utf8_decode('Monto:'), '', 0, 'L');
        $this->Cell(0.107 * $this->WidthTotal, 0.4, $this->solicitud->monto_format, '', 1, 'R');

        $this->setXY(21.5, $this->GetY());

        $this->Cell(0.105 * $this->WidthTotal, 0.4, utf8_decode('Porcentaje del Cambio:'), '', 0, 'L');
        $this->Cell(0.107 * $this->WidthTotal, 0.4, $this->solicitud->porcentaje_cambio_format, '', 1, 'R');
    }

    function pixelsToCM($val)
    {
        return ($val * self::MM_IN_INCH / self::DPI) / 10;
    }

    function resizeToFit($imgFilename)
    {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);
        return [
            round($this->pixelsToCM($scale * $width)),
            round($this->pixelsToCM($scale * $height))
        ];
    }

    function firmas()
    {
        if (Context::getDatabase() == "SAO1814_TERMINAL_NAICM") {
            $this->SetY(-3.5);
            $this->SetTextColor('0', '0', '0');
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);

            $this->SetFont('Arial', 'B', 4.5);
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('RECIBE'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->SetXY(18.19, -3.1);

            $this->Ln();
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('CONTROL DE ESTIMACIONES'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('CALIDAD'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('CONTROL DE PROYECTOS'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('DIRECTOR DE ÁREA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('ADMINISTRACIÓN'), 'TRLB', 0, 'C', 1);
        }
        else if (Context::getDatabase() == "SAO1814_PRESA_LIBERTAD" && Context::getIdObra() == 6 && $this->estimacion->origenAcarreos) {
            /*Firmas Acarreos Presa Libertad*/
            $this->SetY(-3.5);
            $this->SetTextColor('0', '0', '0');
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);

            $this->SetFont('Arial', 'B', 4.2);
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');

            $this->SetFont('Arial', 'B', 4.2);
            $this->Ln();
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode("Ing. Victor Hugo López Briones"), 'RLB', 0, 'C', 0);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Ing. Jonathan Vergara Sánchez'), 'RLB', 0, 'C', 0);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('C.P. Felix Quintero Sosa'), 'RLB', 0, 'C', 0);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Ing. Gilberto García Rangel'), 'RLB', 0, 'C', 0);
            $this->Ln();
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de Subcontratos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de Construcción'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de ACSMA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de Personal'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de Control de Proyectos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de Administración'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable del Proyecto'), 'TRLB', 0, 'C', 1);

        }
        else if(Context::getDatabase() == "SAO1814_CIRCUITO" && (Context::getIdObra() == 7 || Context::getIdObra() == 8)){
            /*Firmas en CEVASG E INFRASG*/
            $this->SetY(-3.5);
            $this->SetTextColor('0', '0', '0');
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);

            $this->SetFont('Arial', 'B', 4.2);
            $this->Cell(1);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('VoBo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('Autoriza'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(1);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 1.2, '', 'TRLB', 0, 'C');

            $this->SetFont('Arial', 'B', 4.2);
            $this->Ln();
            $this->Cell(1);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('Responsable de Subcontratos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('Jefe de Frente'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('Superintendente'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('Coordinador Técnico'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('Gerente de Proyecto'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 6, 0.4, utf8_decode('Director de Proyecto'), 'TRLB', 0, 'C', 1);
        }
        else {
            /*Firmas en Gral*/
            $this->SetY(-3.5);
            $this->SetTextColor('0', '0', '0');
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);

            $this->SetFont('Arial', 'B', 4.2);
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');

            $this->SetFont('Arial', 'B', 4.2);
            $this->Ln();
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de Subcontratos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de Construcción'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de ACSMA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de Personal'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de Control de Proyectos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de Administración'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable del Proyecto'), 'TRLB', 0, 'C', 1);
        }
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
        $this->firmas();

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
        $this->Cell(10, .3, utf8_decode('Formato generado desde el sistema de contratos del SAO ERP. Fecha de registro: ' . date("d-m-Y", strtotime($this->fecha))).' Fecha de consulta: '.date("d-m-Y H:i:s").'  Estado: '.$this->solicitud->estado_descripcion, 0, 0, 'L');
        $this->SetXY(22.6,-0.9);
        $this->Cell(5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    public function create()
    {
        $this->SetMargins(1, 1, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,3.5);
        $this->partidas();
        $this->Ln(0.3);
        $this->totales();
        try {
            $this->Output('I', "Formato - Solicitud de Cambio a Subcontrato_".$this->solicitud->numero_folio.".pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

    public function _enddoc()
    {
        if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
            $this->PDFVersion='1.4';
        parent::_enddoc();
    }

    public function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++)
        {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_put(sprintf('/ca %.3F', $parms['ca']));
            $this->_put(sprintf('/CA %.3F', $parms['CA']));
            $this->_put('/BM '.$parms['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    public  function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach($this->extgstates as $k=>$extgstate)
            $this->_put('/GS'.$k.' '.$extgstate['n'].' 0 R');
        $this->_put('>>');
    }

    public function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }
}
