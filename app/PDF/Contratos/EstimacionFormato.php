<?php


namespace App\PDF\Contratos;

use App\Facades\Context;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\ItemSubcontrato;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Contrato;
use Carbon\Carbon;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;


class EstimacionFormato extends Rotation
{
    protected $obra;
    protected $estimacion;
    private $encabezado_pdf = '';
    protected $extgstates = array();
    protected $conceptos_ordenados = array();


    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     * Estimacion constructor.
     * @param $estimacion
     */

    public function __construct($id)
    {
        parent::__construct('L', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());
        $this->id = $id;
        $this->encabezado_pdf = utf8_decode($this->obra->facturar);
        $this->estimacion = Estimacion::find($id);
        $this->conceptos_ordenados = $this->estimacion->subcontratoAEstimar();

        $this->fecha = Carbon::parse($this->estimacion->fecha)->format('d-m-Y');
        $this->fecha_inicial = Carbon::parse($this->estimacion->cumplimiento)->format('d-m-Y');
        $this->fecha_final = Carbon::parse($this->estimacion->vencimiento)->format('d-m-Y');

        $this->tran_antecedentes = 0;
        $this->suma_contrato = 0;
        $this->suma_estimacionAnterior = 0;
        $this->suma_estimacion = 0;
        $this->suma_acumulada = 0;
        $this->suma_porEstimar = 0;
        $this->WidthTotal = $this->GetPageWidth() - 0.82;
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

        $this->setXY(5.2, 1.5);
        $this->SetFont('Arial', 'B', 12);
        $this->MultiCell(13, 0.9, $this->encabezado_pdf, '', 'C');

        $this->setXY(10, 3.5);
        $this->SetFont('Arial', 'B', 14);
        $this->CellFitScale(1* $this->WidthTotal, 0.1,  utf8_decode("Estimación"), '', 'CB');

        $y_inicial = $this->getY() - 2;
        $x_inicial = $this->GetPageWidth() / 1.51;
        $this->setY($y_inicial);
        $this->setX($x_inicial);

        $this->SetFont('Arial', 'B', 12);
        $this->SetX($x_inicial);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('Folio SAO'), 'LT, LR, LB', 0, 'L');
        $this->SetFont('Arial', 'B', "SDASDA");
        $this->Cell(0.207 * $this->WidthTotal, 0.5, utf8_decode($this->conceptos_ordenados['folio']), 'RT, RB', 1, 'R');

        $this->SetFont('Arial', 'B', 12);
        $this->SetX($x_inicial);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('No. Estimación'), 'L, LR, LB', 0, 'L');
        $this->SetFont('Arial', 'B', "");
        $this->Cell(0.207 * $this->WidthTotal, 0.5, $this->conceptos_ordenados['folio_consecutivo_num'], 'RB', 1, 'R');

        $this->SetFont('Arial', 'B', 9);
        $this->SetX($x_inicial);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('Semana de Contrato'), 'LB, LR', 0, 'L');
        $this->SetFont('Arial', 'B',  10);
        $this->Cell(0.207 * $this->WidthTotal, 0.5,utf8_decode(""), 'RB', 1, 'R');

        $this->SetFont('Arial', 'B', 9);
        $this->SetX($x_inicial);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('Fecha'), 'LB, LR', 0, 'L');
        $this->SetFont('Arial', '',  10);
        $this->Cell(0.207 * $this->WidthTotal, 0.5,utf8_decode($this->fecha), 'RB', 1, 'R');

        $this->SetFont('Arial', 'B', 9);
        $this->SetX($x_inicial);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('Periodo'), 'LB, LR', 0, 'L');
        $this->SetFont('Arial', '',  10);
        $this->Cell(0.207 * $this->WidthTotal, 0.5,utf8_decode("De: ".$this->fecha_inicial. " A: ".$this->fecha_final), 'RB', 1, 'R');

        $this->SetFont('Arial', 'B', 9);
        $this->setXY(0.40, 4.2);
        $this->Cell(0.280 * $this->WidthTotal, 0.5, utf8_decode('Organización:'), 'LRTB', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.720 * $this->WidthTotal, 0.5,utf8_decode($this->obra->nombre), 'LRTB', 1, 'C');


        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0.280 * $this->WidthTotal, 0.5, utf8_decode('Contratista:'), 'LB, LR, LT', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.720* $this->WidthTotal, 0.5,utf8_decode($this->conceptos_ordenados['razon_social']), 'RB, LT', 1, 'C');

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0.280 * $this->WidthTotal, 0.5, utf8_decode('No. de Contrato:'), 'LB, LR, LT', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.720 * $this->WidthTotal, 0.5,utf8_decode($this->conceptos_ordenados['subcontrato']['referencia']), 'RB, LT', 1, 'C');

        $this->tableHeader();

        $currentPage = $this->PageNo();
        if($currentPage>1){
            $this->Ln();
        }
    }

    public function tableHeader()
    {
        $this->Ln();
        $this->SetFills(180,180,180);
        $this->SetFont('Arial', 'B', 6);
        $this->SetFillColor(180,180,180);
        $this->Cell(0.230 * $this->WidthTotal,0.8,'Concepto','RTLB',0,'C',180);
        $this->Cell(0.050 * $this->WidthTotal,0.8,'C. Concepto','RTLB',0,'C',180);
        $this->Cell(0.060 * $this->WidthTotal,0.8,'U.M.','RTLB',0,'C',180);
        $this->Cell(0.050 * $this->WidthTotal,0.8,'P.U.','RTLB',0,'C',180);
        $this->Cell(0.122 * $this->WidthTotal,0.4,'Contrato y Aditamentos ','BTLR',0,'C',180);
        $this->Cell(0.122 * $this->WidthTotal,0.4,utf8_decode("Acum. A Estimación Anterior"),'BTLR',0,'C',180);
        $this->Cell(0.122 * $this->WidthTotal,0.4,utf8_decode("Esta Estimación "),'BTLR',0,'C',180);
        $this->Cell(0.122 * $this->WidthTotal,0.4,utf8_decode("Acum. A Esta Estimación "),'BTLR',0,'C',180);
        $this->Cell(0.122 * $this->WidthTotal,0.4,utf8_decode("Saldo por Estimar "),'BTLR',0,'C',180);

        $this->setXY(10.98, 6.6);
        $this->Cell((0.050 * $this->WidthTotal),0.4,'Cantidad','LRBT',0,'C',180);
        $this->Cell((0.072 * $this->WidthTotal),0.4,'Importe','LRBT',0,'C',180);

        $this->setXY(10.98+(0.122 * $this->WidthTotal), 6.6);
        $this->Cell((0.050 * $this->WidthTotal),0.4,'Cantidad','LRBT',0,'C',180);
        $this->Cell((0.072 * $this->WidthTotal),0.4,'Importe','LRBT',0,'C',180);

        $this->setXY(10.98+(0.122 * $this->WidthTotal)*2, 6.6);
        $this->Cell((0.050 * $this->WidthTotal),0.4,'Cantidad','LRBT',0,'C',180);
        $this->Cell((0.072 * $this->WidthTotal),0.4,'Importe','LRBT',0,'C',180);

        $this->setXY(10.98+(0.122 * $this->WidthTotal)*3, 6.6);
        $this->Cell((0.050 * $this->WidthTotal),0.4,'Cantidad','LRBT',0,'C',180);
        $this->Cell((0.072 * $this->WidthTotal),0.4,'Importe','LRBT',0,'C',180);

        $this->setXY(10.98+(0.122 * $this->WidthTotal)*4, 6.6);
        $this->Cell((0.050 * $this->WidthTotal),0.4,'Cantidad','LRBT',0,'C',180);
        $this->Cell((0.072 * $this->WidthTotal),0.4,'Importe','LRBT',0,'C',180);

        if($this->encola == 'obra_ejecutada'){
            $this->obraEjecutadaTitle();
        }
        if($this->encola == 'partidas_deductivas'){
            $this->deductivaDescuentosTitle();
        }
        if($this->encola == 'retenciones'){
            $this->retencionesTitle();
        }
        if($this->encola == 'liberaciones'){
            $this->retencionesLiberadasTitle();
        }
        if($this->encola == 'penalizaciones'){
            $this->penalizacionesTitle();
        }
        if($this->encola == 'penalizaciones_liberadas'){
            $this->penalizacionesLiberadasTitle();
        }
        if($this->encola == 'resumen'){
            $w_t = $this->WidthTotal;
            $this->Ln();
            $this->SetFont('Arial', '', 5);
            $this->SetFillColor(180, 180, 180);
            $this->SetWidths([$w_t* 0.230, $w_t* 0.050, $w_t* 0.060, $w_t* 0.050, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072]);
            $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF', 'DF', 'FD', 'FD', 'DF']);
            $this->SetRounds(['1', '', '', '', '', '', '', '', '', '', '', '', '', '2']);
            $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.0]);
            $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            $this->SetHeights([0.4]);
            $this->SetAligns(['L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R']);
        }

    }

    public function obraEjecutadaTitle(){
        $this->SetFont('Arial', 'B', 7);
        $this->setXY(0.4, 7);

        $this->Cell(0.340 * $this->WidthTotal,0.4,'OBRA EJECUTADA',0,0,'L',0);

        $this->SetFont('Arial', '', 5);
        $this->setXY(0.4+(0.340 * $this->WidthTotal), 7);
        $this->Cell(0.050 * $this->WidthTotal,0.4,$this->conceptos_ordenados['moneda'],0,0,'C',0);

        $this->setXY(0.4+(0.440* $this->WidthTotal), 7);
        $this->Cell(0.072 * $this->WidthTotal,0.4,$this->conceptos_ordenados['moneda'],0,0,'C',0);

        $this->setXY(0.4+(0.562* $this->WidthTotal), 7);
        $this->Cell(0.072 * $this->WidthTotal,0.4,$this->conceptos_ordenados['moneda'],0,0,'C',0);

        $this->setXY(0.4+(0.682* $this->WidthTotal), 7);
        $this->Cell(0.072 * $this->WidthTotal,0.4,$this->conceptos_ordenados['moneda'],0,0,'C',0);

        $this->setXY(0.4+(0.804* $this->WidthTotal), 7);
        $this->Cell(0.072 * $this->WidthTotal,0.4, $this->conceptos_ordenados['moneda'],0,0,'C',0);

        $this->setXY(0.4+(0.926* $this->WidthTotal), 7);
        $this->Cell(0.07 * $this->WidthTotal,0.4,$this->conceptos_ordenados['moneda'],0,0,'C',0);
        $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255',  '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);

    }

    public function setWaterText($txt1="fdsgfd", $txt2="dfgsfds")
    {
        $this->_outerText1 = $txt1;
        $this->_outerText2 = $txt2;
    }

    public function partidas()
    {
        $this->obraEjecutadaTitle();
        $w_t = $this->WidthTotal;
        $this->Ln();
        $this->SetFont('Arial', '', 5);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([$w_t * 0.230, $w_t * 0.050, $w_t * 0.060, $w_t * 0.050, $w_t * 0.050, $w_t * 0.072, $w_t * 0.050, $w_t * 0.072, $w_t * 0.050, $w_t * 0.072, $w_t * 0.050, $w_t * 0.072, $w_t * 0.050, $w_t * 0.072]);
        $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF', 'DF', 'FD', 'FD', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '', '', '', '', '', '', '', '', '2']);
        $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.0]);
        $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['L', 'C', 'C', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R']);
        $this->encola = 'obra_ejecutada';

        foreach ($this->estimacion->subcontrato->partidasPDF($this->estimacion->id_transaccion) as $p) {
            if(!array_key_exists('para_estimar', $p)) {
                $this->suma_contrato += $p['importe_subcontrato'];
                $this->suma_estimacionAnterior += $p['importe_acumulado_anterior'];
                $this->suma_estimacion += $p['importe_estimacion'];
                $this->suma_acumulada += $p['importe_acumulado_a_esta_estimacion'];
                $this->suma_porEstimar += $p['importe_porEstimar'];

                $this->Row([
                    mb_strtoupper($p['descripcion_concepto']),
                    mb_strtoupper($p['clave']),
                    mb_strtoupper($p['unidad']),
                    number_format($p['precio_unitario_subcontrato'], 4, ".", ","),
                    number_format($p['cantidad_subcontrato'], 4, ".", ","),
                    number_format($p['importe_subcontrato'], 4, ".", ","),
                    number_format($p['cantidad_acumulado_anterior'], 4, ".", ","),
                    number_format($p['importe_acumulado_anterior'], 4, ".", ","),
                    number_format($p['cantidad_estimacion'], 4, ".", ","),
                    number_format($p['importe_estimacion'], 4, ".", ","),
                    number_format($p['cantidad_acumulado_a_esta_estimacion'], 4, ".", ","),
                    number_format($p['importe_acumulado_a_esta_estimacion'], 4, ".", ","),
                    number_format($p['cantidad_porEstimar'], 4, ".", ","),
                    number_format($p['importe_porEstimar'], 4, ".", ",")
                ]);
            }
        }

        /*Footer partidas*/
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 5);
        $this->SetFillColor(180, 180, 180);

        $this->Cell(0.230 * $w_t, 0.3, "Sub-totales Obra Ejecutada", 'RTLB', 0, 'R', 180);   // empty cell with left,top, and right borders
        $this->Cell(0.050 * $w_t, 0.3, "", 'RTLB', 0, 'C', 180);
        $this->Cell(0.060 * $w_t, 0.3, "", 'RTLB', 0, 'C', 180);
        $this->Cell(0.050 * $w_t, 0.3, '', 'RTLB', 0, 'R', 180);
        $this->Cell(0.050 * $w_t, 0.3, ' ', 'BTLR', 0, 'R', 180);
        $this->Cell(0.072 * $w_t, 0.3, number_format($this->suma_contrato, 4, ".", ","), 'BTLR', 0, 'R', 180);
        $this->Cell(0.050 * $w_t, 0.3, '', 'BTLR', 0, 'R', 180);
        $this->Cell(0.072 * $w_t, 0.3, number_format($this->suma_estimacionAnterior, 4, ".", ","), 'BTLR', 0, 'R', 180);
        $this->Cell(0.050 * $w_t, 0.3, ' ', 'BTLR', 0, 'R', 180);
        $this->Cell(0.072 * $w_t, 0.3, number_format($this->suma_estimacion, 4, ".", ","), 'BTLR', 0, 'R', 180);
        $this->Cell(0.050 * $w_t, 0.3, ' ', 'BTLR', 0, 'R', 180);
        $this->Cell(0.072 * $w_t, 0.3, number_format($this->suma_acumulada, 4, ".", ","), 'BTLR', 0, 'R', 180);
        $this->Cell(0.050 * $w_t, 0.3, '', 'BTLR', 0, 'R', 180);
        $this->Cell(0.072 * $w_t, 0.3, number_format($this->suma_porEstimar, 4, ".", ","), 'BTLR', 0, 'R', 180);
        $this->encola = '';
    }

    public function deductivaDescuentosTitle()
    {
        $this->setXY(0.4, $this->getY() + 0.4);
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(180, 180, 180);
        $this->Ln(.3);

        if($this->getY() > 17.2)$this->AddPage();
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'DEDUCTIVAS Y DESCUENTOS', '', 0, 'L', 0);
        $this->Cell(0.282 * $this->WidthTotal, 0.4, 'DEDUCTIVAS', 'RTLB', 0, 'C', 1);
        $this->Cell(0.488 * $this->WidthTotal, 0.4, 'DESCUENTOS', 'RTLB', 1, 'C', 1);
        $w_t = $this->WidthTotal;
        $this->SetFont('Arial', '', 5);

        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', '', 5);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([$w_t* 0.230, $w_t* 0.050, $w_t* 0.060, $w_t* 0.050, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072]);
        $this->SetStyles(['DF', 'DF','DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF', 'DF', 'FD', 'FD', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '', '', '', '', '', '', '', '', '2']);
        $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.0]);
        $this->SetFills(['255,255,55', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0','0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['L', 'R','R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R']);
    }

    public function partidaDeductiva($partidas)
    {
        $this->deductivaDescuentosTitle();
        $this->deductivas = $partidas;

        $this->encola = 'partidas_deductivas';
        foreach ($partidas['partidas_descuento'] as $partida)
        {
            $this->Row([
                utf8_decode($partida['descripcion']),
                '',
                $partida['unidad'],
                number_format($partida['precio_unitario'], 4, ".", ","),
                number_format($partida['cantidad_original'], 4, ".", ","),
                number_format($partida['importe_original'], 4, ".", ","),
                number_format($partida['cantidad_descuento_anterior'], 4, ".", ","),
                number_format($partida['importe_descuento_anterior'], 4, ".", ","),
                number_format($partida['cantidad_descuento'], 4, ".", ","),
                number_format($partida['importe_descuento'], 4, ".", ","),
                number_format($partida['cantidad_a_esta_estimacion'], 4, ".", ","),
                number_format($partida['importe_a_esta_estimacion'], 4, ".", ","),
                number_format($partida['cantidad_descuento_porEstimar'], 4, ".", ","),
                number_format($partida['importe_descuento_porEstimar'], 4, ".", ","),
            ]);
        }

        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 5);
        $this->SetFillColor(180, 180, 180);
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'Sub-Totales Deductivas', 'RTLB', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.060 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4,  number_format($partidas['importe_total_original'], 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4,  number_format($partidas['importe_descuento_anterior'], 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4,  number_format($partidas['importe_descuento'], 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4,  number_format($partidas['$importe_acumulado'], 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4,  number_format($partidas['importe_porEstimar'], 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->encola ='';
    }

    public function retencionesTitle()
    {
        $this->Ln();
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(180, 180, 180);
        $this->Ln(.3);
        if($this->getY() > 17.2)$this->AddPage();
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'RETENCIONES', '', 0, 'L', 0);

        $w_t = $this->WidthTotal;
        $this->SetFont('Arial', '', 5);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([$w_t* 0.230, $w_t* 0.050, $w_t* 0.060, $w_t* 0.050, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072]);
        $this->SetStyles(['DF', 'DF','DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF', 'DF', 'FD', 'FD', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '', '', '', '', '', '', '', '', '2']);
        $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.0]);
        $this->SetFills(['255,255,55', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0','0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R']);
        // $this->Ln();
    }

    public function partidaRetenciones()
    {
        $this->retencionesTitle();
        $this->encola = 'retenciones';
        $this->Ln();
        $retenciones = $this->estimacion->retenciones()->get();
       foreach ($retenciones as $retencion) {

           $this->Row([
               utf8_decode($retencion->concepto),
               '',
               utf8_decode($retencion->tipo_retencion),
               '', '', '', '', '', '',
               number_format($retencion->importe, 4, ".", ","),
               '', '', '', '',
           ]);

       }
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 5);
        $this->SetFillColor(180, 180, 180);
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'Sub-Totales Retenciones', 'RTLB', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.060 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->subcontrato->acumulado_retencion_anteriores - $retenciones->sum('importe'), 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($retenciones->sum('importe'), 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->subcontrato->acumulado_retencion_anteriores, 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->encola ='';
    }

    public function retencionesLiberadasTitle()
    {
        $this->Ln();
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(180, 180, 180);
        $this->Ln(.5);
        if($this->getY() > 17.2)$this->AddPage();
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'RETENCIONES LIBERADAS', '', 0, 'L', 0);
        $w_t = $this->WidthTotal;
        $this->SetFont('Arial', '', 5);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([$w_t* 0.230, $w_t* 0.050, $w_t* 0.060, $w_t* 0.050, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072]);
        $this->SetStyles(['DF', 'DF','DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF', 'DF', 'FD', 'FD', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '', '', '', '', '', '', '', '', '2']);
        $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.0]);
        $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R']);
    }

    public function partidaRetencionesLiberadas()
    {
        $this->retencionesLiberadasTitle();
        $this->encola = 'liberaciones';
        $this->Ln();
        $liberaciones = $this->estimacion->liberaciones;

        foreach ($liberaciones as $liberacion) {
            $this->Row([
                utf8_decode($liberacion->concepto),
                '', '', '', '', '', '', '', '',
                number_format($liberacion->importe, 4, ".", ","),
                '', '', '', '',
            ]);
        }

        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 5);
        $this->SetFillColor(180, 180, 180);
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'Sub-Totales Liberaciones', 'RTLB', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.060 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->subcontrato->acumulado_liberacion_anteriores - $liberaciones->sum('importe'), 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($liberaciones->sum('importe'), 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->subcontrato->acumulado_liberacion_anteriores, 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);

        $this->Ln();
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 5);
        $this->SetFillColor(180, 180, 180);
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'Por Liberar', 'RTLB', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.060 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4,number_format(($this->estimacion->subcontrato->acumulado_retencion_anteriores-$this->estimacion->subcontrato->acumulado_liberacion_anteriores)-($this->estimacion->retenciones->sum('importe')-$liberaciones->sum('importe')), 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->retenciones->sum('importe')-$liberaciones->sum('importe'), 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->subcontrato->acumulado_retencion_anteriores-$this->estimacion->subcontrato->acumulado_liberacion_anteriores, 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);

        $this->encola ='';
    }

    public function penalizacionesTitle()
    {
        $this->Ln();
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(180, 180, 180);
        $this->Ln(.3);
        if($this->getY() > 17.2)$this->AddPage();
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'PENALIZACIONES', '', 0, 'L', 0);

        $w_t = $this->WidthTotal;
        $this->SetFont('Arial', '', 5);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([$w_t* 0.230, $w_t* 0.050, $w_t* 0.060, $w_t* 0.050, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072]);
        $this->SetStyles(['DF', 'DF','DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF', 'DF', 'FD', 'FD', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '', '', '', '', '', '', '', '', '2']);
        $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.0]);
        $this->SetFills(['255,255,55', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0','0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R']);
        // $this->Ln();
    }

    public function partidaPenalizaciones()
    {
        $this->penalizacionesTitle();
        $this->encola = 'penalizaciones';
        $this->Ln();

        foreach ( $this->estimacion->penalizaciones as $penalizacion) {

            $this->Row([
                utf8_decode($penalizacion->concepto),
                '',
                '',
                '', '', '', '', '', '',
                number_format($penalizacion->importe, 4, ".", ","),
                '', '', '', '',
            ]);

        }
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 5);
        $this->SetFillColor(180, 180, 180);
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'Sub-Totales Penalizaciones', 'RTLB', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.060 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->subcontrato->acumulado_penalizaciones_anteriores - $this->estimacion->suma_penalizaciones, 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format( $this->estimacion->suma_penalizaciones, 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->subcontrato->acumulado_penalizaciones_anteriores, 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->encola ='';
    }

    public function penalizacionesLiberadasTitle()
    {
        $this->Ln();
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(180, 180, 180);
        $this->Ln(.5);
        if($this->getY() > 17.2)$this->AddPage();
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'PENALIZACIONES LIBERADAS', '', 0, 'L', 0);
        $w_t = $this->WidthTotal;
        $this->SetFont('Arial', '', 5);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([$w_t* 0.230, $w_t* 0.050, $w_t* 0.060, $w_t* 0.050, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072]);
        $this->SetStyles(['DF', 'DF','DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF', 'DF', 'FD', 'FD', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '', '', '', '', '', '', '', '', '2']);
        $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.0]);
        $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R']);
    }

    public function partidaPenalizacionesLiberadas()
    {
        $this->penalizacionesLiberadasTitle();
        $this->encola = 'penalizaciones_liberadas';
        $this->Ln();

        foreach ($this->estimacion->penalizacionLiberaciones as $liberacion) {
            $this->Row([
                utf8_decode($liberacion->concepto),
                '', '', '', '', '', '', '', '',
                number_format($liberacion->importe, 4, ".", ","),
                '', '', '', '',
            ]);
        }

        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 5);
        $this->SetFillColor(180, 180, 180);
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'Sub-Totales Liberaciones', 'RTLB', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.060 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->subcontrato->acumulado_penalizaciones_liberada_anteriores - $this->estimacion->suma_penalizaciones_liberadas, 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->suma_penalizaciones_liberadas, 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->subcontrato->acumulado_penalizaciones_liberada_anteriores, 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);

        $this->Ln();
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 5);
        $this->SetFillColor(180, 180, 180);
        $this->Cell(0.230 * $this->WidthTotal, 0.4, 'Por Liberar', 'RTLB', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.060 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4,number_format(($this->estimacion->subcontrato->acumulado_penalizaciones_anteriores-$this->estimacion->subcontrato->acumulado_penalizaciones_liberada_anteriores)-($this->estimacion->suma_penalizaciones - $this->estimacion->suma_penalizaciones_liberadas), 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->suma_penalizaciones - $this->estimacion->suma_penalizaciones_liberadas, 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, number_format($this->estimacion->subcontrato->acumulado_penalizaciones_anteriores-$this->estimacion->subcontrato->acumulado_penalizaciones_liberada_anteriores, 4, ".", ","), 'BTLR', 0, 'R', 1);
        $this->Cell(0.050 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);
        $this->Cell(0.072 * $this->WidthTotal, 0.4, '', 'BTLR', 0, 'R', 1);

        $this->encola ='';
    }

    public function tablaResumen()
    {
        $this->encola = 'resumen';
        $w_t = $this->WidthTotal;
        $this->Ln();
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(180, 180, 180);
        $this->Ln(.3);
        $this->Cell(0.230 * $w_t, 0.4, 'RESUMEN', '', 0, 'L', 0);

        $this->Ln();
        $this->SetFont('Arial', 'B', 5);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([$w_t* 0.230, $w_t* 0.050, $w_t* 0.060, $w_t* 0.050, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072, $w_t* 0.050, $w_t* 0.072]);
        $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF', 'DF', 'FD', 'FD', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '', '', '', '', '', '', '', '', '2']);
        $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.0]);
        $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R']);

        /// Calculos Resumen Amortizacion Anticpo
        $amort_anticipo_anterior = $this->estimacion->anticipo_anterior;
        $amortizacion_anticipo =  $this->estimacion->monto_anticipo_aplicado;
        $anticipo_actual = $amort_anticipo_anterior + $amortizacion_anticipo;
        $anticipo_saldo = $this->estimacion->subcontrato->anticipo_monto - $anticipo_actual;

        /// Calculos Resumen Fondo Garantia
        $fondo_garantia_contrato = $this->estimacion->subcontrato->importe_fondo_garantia;
        $fondo_garantia_anterior= $this->estimacion->fondo_garantia_acumulado_anterior;
        $fondo_garantia= $this->estimacion->retencion_fondo_garantia_orden_pago;
        $fondo_garantia_actual = $fondo_garantia_anterior + $fondo_garantia;
        $fondo_garantia_saldo = $fondo_garantia_contrato - $fondo_garantia_actual;

        $subtotal_contrato = $this->suma_contrato;
        $subtotal_acum_estimado_anterior = $this->suma_estimacionAnterior - $amort_anticipo_anterior;
        $subtotal_estimacion = $this->suma_estimacion - $amortizacion_anticipo;
        $subtotal_acumulado =  $this->suma_acumulada - $anticipo_actual;
        $subtotal_a_estimar = $this->suma_porEstimar - $anticipo_saldo;

        $this->Row(['Importe asociado a trabajos ejecutados', '', '', '', '',  number_format($this->suma_contrato, 4, ".", ","), '', number_format($this->suma_estimacionAnterior, 4, ".", ","), '', number_format($this->suma_estimacion, 4, ".", ","), '',number_format($this->suma_acumulada, 4, ".", ","), '', number_format($this->suma_porEstimar, 4, ".", ",")]);
        $this->Row([utf8_decode('Amortización Anticipo'), '', '%',  number_format($this->estimacion->anticipo, 4, ".", ","), '', '', '',number_format($amort_anticipo_anterior, 4, ".", ",") , '', number_format($amortizacion_anticipo, 4, ".", ","), ' ',number_format($anticipo_actual, 4, ".", ","), ' ',number_format($anticipo_saldo, 4, ".", ",")]);

        if($this->estimacion->configuracion->ret_fon_gar_antes_iva==1)
        {
            $subtotal_contrato -= $fondo_garantia_contrato;
            $subtotal_acum_estimado_anterior -= $fondo_garantia_anterior;
            $subtotal_estimacion -= $fondo_garantia;
            $subtotal_acumulado -=  $fondo_garantia_actual;
            $subtotal_a_estimar -= $fondo_garantia_saldo;
            $this->Row([utf8_decode('Fondo de Garantía'), ' ', '%', $this->estimacion->subcontratoEstimacion ? $this->estimacion->subcontratoEstimacion->PorcentajeFondoGarantia : '', ' ', number_format($fondo_garantia_contrato, 4, ".", ",") , ' ', number_format($fondo_garantia_anterior, 4, ".", ",") , ' ', number_format($fondo_garantia, 4, ".", ",") , ' ', number_format($fondo_garantia_actual, 4, ".", ",") , ' ', number_format($fondo_garantia_saldo, 4, ".", ",") ]);
        }

        if($this->estimacion->configuracion->retenciones_antes_iva==1)
        {
            $subtotal_acum_estimado_anterior -= $this->estimacion->acumulado_retencion_anteriores;
            $subtotal_acum_estimado_anterior += $this->estimacion->acumulado_liberacion_anteriores;
            $subtotal_estimacion -= $this->estimacion->suma_retenciones;
            $subtotal_estimacion += $this->estimacion->suma_liberaciones;
            $subtotal_acumulado -=  ($this->estimacion->acumulado_retencion_anteriores + $this->estimacion->suma_retenciones);
            $subtotal_acumulado +=  ($this->estimacion->acumulado_liberacion_anteriores + $this->estimacion->suma_liberaciones);

            $this->Row(['Total Retenciones', '', '', '', '', number_format(0, 4, ".", ","), '', number_format($this->estimacion->acumulado_retencion_anteriores, 4, ".", ","), '', number_format($this->estimacion->suma_retenciones, 4, ".", ","), '',  number_format(($this->estimacion->acumulado_retencion_anteriores + $this->estimacion->suma_retenciones), 4, ".", ","), '', '']);
            $this->Row(['Total Retenciones Liberadas', '', '', '', '', number_format(0, 4, ".", ","), '', number_format($this->estimacion->acumulado_liberacion_anteriores, 4, ".", ","), '', number_format( $this->estimacion->suma_liberaciones, 4, ".", ","), '',  number_format(($this->estimacion->acumulado_liberacion_anteriores + $this->estimacion->suma_liberaciones), 4, ".", ","), '', '']);
        }

        if($this->estimacion->configuracion->desc_pres_mat_antes_iva == 1)
        {
            $subtotal_contrato -= $this->deductivas['importe_total_original'];
            $subtotal_acum_estimado_anterior -= $this->deductivas['importe_descuento_anterior'];
            $subtotal_estimacion -= $this->deductivas['importe_descuento'];
            $subtotal_acumulado -=  $this->deductivas['$importe_acumulado'];
            $subtotal_a_estimar -= $this->deductivas['importe_porEstimar'];
            $this->Row(['Deductivas y Descuentos', '', '', '', '', number_format($this->deductivas['importe_total_original'], 4, ".", ","), '', number_format($this->deductivas['importe_descuento_anterior'], 4, ".", ","),'', number_format($this->deductivas['importe_descuento'], 4, ".", ","), '', number_format($this->deductivas['$importe_acumulado'], 4, ".", ","), '', number_format($this->deductivas['importe_porEstimar'], 4, ".", ",")]);
        }

        if($this->estimacion->configuracion->penalizacion_antes_iva == 1)
        {
            $subtotal_acum_estimado_anterior -= $this->estimacion->acumulado_penalizaciones_anteriores;
            $subtotal_acum_estimado_anterior += $this->estimacion->acumulado_penalizaciones_liberada_anteriores;
            $subtotal_estimacion -= $this->estimacion->suma_penalizaciones;
            $subtotal_estimacion += $this->estimacion->suma_penalizaciones_liberadas;
            $subtotal_acumulado -=  ($this->estimacion->acumulado_penalizaciones_anteriores + $this->estimacion->suma_penalizaciones);
            $subtotal_acumulado +=  ($this->estimacion->acumulado_penalizaciones_liberada_anteriores + $this->estimacion->suma_penalizaciones_liberadas);

            $this->SetFills(['255,255,255', '255,255,255', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->Row(['Total Penalizaciones', '', '', '', '', number_format(0, 4, ".", ","), '', number_format($this->estimacion->subcontrato->acumulado_penalizaciones_anteriores, 4, ".", ","), '', number_format($this->estimacion->suma_penalizaciones, 4, ".", ","), '',  number_format($this->estimacion->subcontrato->acumulado_penalizaciones_anteriores + $this->estimacion->suma_penalizaciones, 4, ".", ","), '', '']);
            $this->SetFills(['255,255,255', '255,255,255', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->Row(['Total Penalizaciones Liberadas', '', '', '', '', number_format(0, 4, ".", ","), '', number_format($this->estimacion->subcontrato->acumulado_penalizaciones_liberada_anteriores, 4, ".", ","), '', number_format($this->estimacion->suma_penalizaciones_liberadas, 4, ".", ","), '',  number_format( $this->estimacion->subcontrato->acumulado_penalizaciones_liberada_anteriores + $this->estimacion->suma_penalizaciones_liberadas, 4, ".", ","), '', '']);
        }

        $total_contrato = $subtotal_contrato;
        $total_acum_estimado_anterior = $subtotal_acum_estimado_anterior;
        $total_estimacion = $subtotal_estimacion;
        $total_acumulado = $subtotal_acumulado;
        $total_a_estimar = $subtotal_a_estimar;

        $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
        $this->Row(['Sub-total valor de los trabajos', ' ', $this->conceptos_ordenados['moneda'], ' ', ' ', number_format($subtotal_contrato, 4, ".", ","), ' ', number_format($subtotal_acum_estimado_anterior, 4, ".", ","), ' ',  number_format($subtotal_estimacion, 4, ".", ","), ' ', number_format($subtotal_acumulado, 4, ".", ","), ' ', number_format($subtotal_a_estimar, 4, ".", ",")]);
        $this->SetFills(['255,255,255', '255,255,255', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        if($this->estimacion->iva_orden_pago == 0){
            $this->Row(['IVA', ' ', '%', number_format($this->estimacion->impuesto != 0 ? $this->estimacion->porcentaje_iva : 0, 4, ".", ","), ' ', number_format(0, 4, ".", ","), ' ', number_format(0, 4, ".", ","), ' ', number_format(0, 4, ".", ","), ' ', number_format(0, 4, ".", ","), ' ', number_format(0, 4, ".", ",")]);
        }else {
            $total_contrato += $subtotal_contrato * $this->estimacion->tasa_iva;
            $total_acum_estimado_anterior += $subtotal_acum_estimado_anterior * $this->estimacion->tasa_iva;
            $total_estimacion += $subtotal_estimacion * $this->estimacion->tasa_iva;
            $total_acumulado += $subtotal_acumulado * $this->estimacion->tasa_iva;
            $total_a_estimar += $subtotal_a_estimar * $this->estimacion->tasa_iva;

            $this->Row(['IVA', ' ', '%', number_format($this->estimacion->impuesto!= 0 ? $this->estimacion->porcentaje_iva : 0, 4, ".", ","), ' ', number_format($subtotal_contrato * $this->estimacion->tasa_iva, 4, ".", ","), ' ', number_format($subtotal_acum_estimado_anterior * $this->estimacion->tasa_iva, 4, ".", ","), ' ', number_format($subtotal_estimacion * $this->estimacion->tasa_iva, 4, ".", ","), ' ', number_format($subtotal_acumulado * $this->estimacion->tasa_iva, 4, ".", ","), ' ', number_format($subtotal_a_estimar * $this->estimacion->tasa_iva, 4, ".", ",")]);
        }

        $total_acum_estimado_anterior -= $this->estimacion->iva_retenido_calculado_anterior;
        $total_acum_estimado_anterior -= $this->estimacion->isr_retenido_calculado_anterior;
        $total_estimacion -= $this->estimacion->iva_retenido_calculado;
        $total_estimacion -= $this->estimacion->isr_retenido_calculado;
        $total_acumulado -= ($this->estimacion->iva_retenido_calculado_anterior + $this->estimacion->iva_retenido_calculado);
        $total_acumulado -= ($this->estimacion->isr_retenido_calculado_anterior + $this->estimacion->isr_retenido_calculado);


        $this->Row([
            utf8_decode('Retención IVA '), ' ', '', '', ' ', '', ' '
            , number_format($this->estimacion->iva_retenido_calculado_anterior, 4, ".", ","),''
            , number_format($this->estimacion->iva_retenido_calculado, 4, ".", ","), ' '
            , number_format(($this->estimacion->iva_retenido_calculado_anterior + $this->estimacion->iva_retenido_calculado), 4, ".", ",")
            , ' ', '']);

        $this->Row([
            utf8_decode('Retención ISR '), ' ', '', '', ' ', '', ' '
            , number_format($this->estimacion->isr_retenido_calculado_anterior, 4, ".", ","),''
            , number_format($this->estimacion->isr_retenido_calculado, 4, ".", ","), ' '
            , number_format(($this->estimacion->isr_retenido_calculado_anterior + $this->estimacion->isr_retenido_calculado), 4, ".", ",")
            , ' ', '']);

        $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
        $this->Row(['Total','', $this->conceptos_ordenados['moneda'], ' ', ' ',
            number_format($total_contrato, 4, ".", ","), ' ',
            number_format($total_acum_estimado_anterior, 4, ".", ","), ' ',
            number_format($total_estimacion, 4, ".", ","), ' ',
            number_format($total_acumulado, 4, ".", ","), ' ',
            number_format($total_a_estimar, 4, ".", ",")]);

        if($this->estimacion->configuracion->ret_fon_gar_antes_iva==0)
        {
            $total_contrato -= $fondo_garantia_contrato;
            $total_acum_estimado_anterior -= $fondo_garantia_anterior;
            $total_estimacion -= $fondo_garantia;
            $total_acumulado -= $fondo_garantia_actual;
            $total_a_estimar -= $fondo_garantia_saldo;

            $this->SetFills(['255,255,255', '255,255,255', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->Row([ $this->estimacion->configuracion->ret_fon_gar_con_iva == 1 ? utf8_decode('Fondo de Garantía + IVA') : utf8_decode('Fondo de Garantía') , ' ', '%', $this->estimacion->subcontratoEstimacion ? $this->estimacion->subcontratoEstimacion->PorcentajeFondoGarantia : '', ' ', number_format($fondo_garantia_contrato, 4, ".", ",") , ' ', number_format($fondo_garantia_anterior, 4, ".", ",") , ' ', number_format($fondo_garantia, 4, ".", ",") , ' ', number_format($fondo_garantia_actual, 4, ".", ",") , ' ', number_format($fondo_garantia_saldo, 4, ".", ",") ]);
        }

        if($this->estimacion->configuracion->desc_pres_mat_antes_iva==0)
        {
            $total_contrato -= $this->deductivas['importe_total_original'];
            $total_acum_estimado_anterior -= $this->deductivas['importe_descuento_anterior'];
            $total_estimacion -= $this->deductivas['importe_descuento'];
            $total_acumulado -= $this->deductivas['$importe_acumulado'];
            $total_a_estimar -= $this->deductivas['importe_porEstimar'];

            $this->SetFills(['255,255,255', '255,255,255', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->Row(['Deductivas y Descuentos', '', '', '', '', number_format($this->deductivas['importe_total_original'], 4, ".", ","), '', number_format($this->deductivas['importe_descuento_anterior'], 4, ".", ","),'', number_format($this->deductivas['importe_descuento'], 4, ".", ","), '', number_format($this->deductivas['$importe_acumulado'], 4, ".", ","), '', number_format($this->deductivas['importe_porEstimar'], 4, ".", ",")]);
        }

        if($this->estimacion->configuracion->retenciones_antes_iva==0) {

            $total_acum_estimado_anterior -= $this->estimacion->acumulado_retencion_anteriores;
            $total_acum_estimado_anterior += $this->estimacion->acumulado_liberacion_anteriores;
            $total_estimacion -= $this->estimacion->suma_retenciones;
            $total_estimacion += $this->estimacion->suma_liberaciones;
            $total_acumulado -= ($this->estimacion->acumulado_retencion_anteriores + $this->estimacion->suma_retenciones);
            $total_acumulado += ($this->estimacion->acumulado_liberacion_anteriores + $this->estimacion->suma_liberaciones);

            $this->Row(['Total Retenciones', '', '', '', '', number_format(0, 4, ".", ","), '', number_format($this->estimacion->acumulado_retencion_anteriores, 4, ".", ","), '', number_format($this->estimacion->suma_retenciones, 4, ".", ","), '', number_format(($this->estimacion->acumulado_retencion_anteriores + $this->estimacion->suma_retenciones), 4, ".", ","), '', '']);
            $this->Row(['Total Retenciones Liberadas', '', '', '', '', number_format(0, 4, ".", ","), '', number_format($this->estimacion->acumulado_liberacion_anteriores, 4, ".", ","), '', number_format($this->estimacion->suma_liberaciones, 4, ".", ","), '', number_format(($this->estimacion->acumulado_liberacion_anteriores + $this->estimacion->suma_liberaciones), 4, ".", ","), '', '']);
        }

            if($this->estimacion->configuracion->penalizacion_antes_iva == 0)
        {
            $total_acum_estimado_anterior -= $this->estimacion->subcontrato->acumulado_penalizaciones_anteriores;
            $total_acum_estimado_anterior += $this->estimacion->subcontrato->acumulado_penalizaciones_liberada_anteriores;
            $total_estimacion -= $this->estimacion->suma_penalizaciones;
            $total_estimacion += $this->estimacion->suma_penalizaciones_liberadas;
            $total_acumulado -= $this->estimacion->subcontrato->acumulado_penalizaciones_anteriores + $this->estimacion->suma_penalizaciones;
            $total_acumulado += $this->estimacion->subcontrato->acumulado_penalizaciones_liberada_anteriores + $this->estimacion->suma_penalizaciones_liberadas;

            $this->SetFills(['255,255,255', '255,255,255', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->Row(['Total Penalizaciones', '', '', '', '', number_format(0, 4, ".", ","), '', number_format($this->estimacion->subcontrato->acumulado_penalizaciones_anteriores, 4, ".", ","), '', number_format($this->estimacion->suma_penalizaciones, 4, ".", ","), '',  number_format($this->estimacion->subcontrato->acumulado_penalizaciones_anteriores + $this->estimacion->suma_penalizaciones, 4, ".", ","), '', '']);
            $this->SetFills(['255,255,255', '255,255,255', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->Row(['Total Penalizaciones Liberadas', '', '', '', '', number_format(0, 4, ".", ","), '', number_format($this->estimacion->subcontrato->acumulado_penalizaciones_liberada_anteriores, 4, ".", ","), '', number_format($this->estimacion->suma_penalizaciones_liberadas, 4, ".", ","), '',  number_format( $this->estimacion->subcontrato->acumulado_penalizaciones_liberada_anteriores + $this->estimacion->suma_penalizaciones_liberadas, 4, ".", ","), '', '']);
        }

        $total_contrato += $this->estimacion->subcontrato->anticipo_monto;
        $this->SetFills(['255,255,255', '255,255,255', '255,255,255','255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->Row(['Anticipo Solicitado', '', '%', $this->estimacion->subcontrato->anticipo, '', number_format($this->estimacion->subcontrato->anticipo_monto, 4, ".", ","), '', '', '', '', '', '', '', '']);

        $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
        $this->Row(['Total a pagar','', $this->conceptos_ordenados['moneda'], ' ', ' ', number_format($total_contrato, 4, ".", ","), ' ', number_format($total_acum_estimado_anterior, 4, ".", ","), ' ', number_format($total_estimacion, 4, ".", ","), ' ', number_format($total_acumulado, 4, ".", ","), ' ', number_format($total_a_estimar, 4, ".", ",")]);
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
        /*if (Context::getDatabase() == "SAO1814_PISTA_AEROPUERTO") {
            $this->SetY(-3.5);
            $this->SetTextColor('0', '0', '0');
            $this->SetFillColor(180, 180, 180);
            $this->SetFont('Arial', 'B', 4.5);
            $this->Cell(1);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode(''), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('Avaló'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('Vo.Bo.'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('AUTORIZÓ'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('REVISÓ/RECIBIÓ'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(1);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 0, 'C');

            $this->SetXY(7.89, -2.3);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('Ing. Victor Luis Gurrola Deras'), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('Ing. Aurelio Aguilar Ramos'), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('Ing. Francisco Resendiz Flores'), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('Ing. José Luis Gaona Aburto'), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('Ing. Victor Manuel Orozco Muñoz'), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('C.P. Eleazar Ortega Valerio'), '', 0, 'C', 0);

            $this->Ln();
            $this->Cell(1);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('CONCILIADOR'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('DEPARTAMENTO'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('SUPERINTENDENCIA DE OBRA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('CALIDAD'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('SUBCONTRATOS'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('SUPERINTENDENCIA TÉCNICA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('GERENCIA DE PROYECTO'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('ADMINISTRACIÓN'), 'TRLB', 0, 'C', 1);



        }
        else if (Context::getDatabase() == "SAO1814_TUNEL_DRENAJE_PRO") {
            /*Firmas en Gral*/
        /*
            $this->SetY(-3.5);
            $this->SetTextColor('0', '0', '0');
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);

            $this->SetFont('Arial', 'B', 4.2);
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('AVALÓ'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Vo.Bo.'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('AUTORIZÓ'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('RECIBE'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 1.2, '', 'TRLB', 0, 'C');

            $this->SetFont('Arial', 'B', 4.2);
            $this->Ln();
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('CONTRATISTA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('CONTROL DE ESTIMACIONES'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('SUPERINTENDENCIA DE OBRA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('CALIDAD'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('CONTROL DE PLANEACIÓN'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('CONTROL DE SEGUIMIENTO'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('SUBCONTRATOS'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('DIRECTOR DE PROYECTO'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('ADMINISTRADOR'), 'TRLB', 0, 'C', 1);


            $this->SetFont('Arial', 'B', 3.6);
            $this->SetY(-2.3);
            // $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode(''), '', 0, 'C', 0);
            $this->Cell(0.7);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode(''), '', 0, 'C', 0);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode(''), '', 0, 'C', 0);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode(''), '', 0, 'C', 0);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Ing. Giselle Belran Baños'), '', 0, 'C', 0);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Ing. Lazaro Romero Zamora'), '', 0, 'C', 0);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Ing. Guadalupe Moreno Hernández'), '', 0, 'C', 0);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Ing. Luis Alfonso Hernández Reding'), '', 0, 'C', 0);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Ing. Francisco Javier Bay Ortuazar'), '', 0, 'C', 0);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 9, 0.4, utf8_decode('Ing. Martin Morales Sanchéz o'), '', 0, 'R', 0);

        }
        */
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
            $this->Cell(0.4);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(0.4);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRL', 0, 'C');

            $this->SetFont('Arial', 'B', 4.2);
            $this->Ln();
            $this->Cell(0.4);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode("Ing. Anayeli Marcial Basurto"), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Ing. Moisés Martínez'), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Ing. Jonathan Vergara Sánchez'), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('C.P. Luis Alberto García Chaires'), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Ing. Edgar J. Rodríguez Gómez'), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Ing. Miguel Angel Bonola Chacon'), 'RLB', 0, 'C', 0);
            $this->Ln();
            $this->Cell(0.4);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de Subcontratos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de Construcción'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de ACSMA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de Personal'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de Control de Proyectos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Responsable de Administración'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.3, utf8_decode('Jefe de Trituración'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
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
        else if(Context::getDatabase() == "SAO1814_CUTZAMALA" && Context::getIdObra() == 6 ){
            /*Firmas Presa Madin*/
            $this->SetY(-3.5);
            $this->SetTextColor('0', '0', '0');
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);

            $this->SetFont('Arial', 'B', 4.2);
            $this->Cell(0.01);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(0.01);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 1.2, '', 'TRLB', 0, 'C');

            $this->SetFont('Arial', 'B', 4.2);
            $this->Ln();
            $this->Cell(0.01);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de Subcontratos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de Construcción'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de ACSMA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de Personal'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de Control de Proyectos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable de Administración'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Gerencia Técnica / Ingeniería'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.285);
            $this->Cell(($this->GetPageWidth() - 6) / 7, 0.4, utf8_decode('Responsable del Proyecto'), 'TRLB', 0, 'C', 1);
        }
        else if(Context::getDatabase() == "SAO1814_CHIMALHUACAN" && Context::getIdObra() == 4 )
        {
            /*Firmas en Gral*/
            $this->SetY(-3.5);
            $this->SetTextColor('0', '0', '0');
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);

            $this->SetFont('Arial', 'B', 4.2);
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Salida Aplicada'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 1.2, '', 'TRLB', 0, 'C');

            $this->SetFont('Arial', 'B', 4.2);
            $this->Ln();
            $this->Cell(0.6);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Responsable de Subcontratos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Responsable de Construcción'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Responsable de ACSMA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Responsable de Almacén'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Responsable de Personal'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Responsable de Control de Proyectos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Responsable de Administración'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 6) / 7.8, 0.4, utf8_decode('Responsable del Proyecto'), 'TRLB', 0, 'C', 1);
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
        $this->Cell(10, .3, utf8_decode('Formato generado desde el sistema de contratos. Fecha de registro: ' . date("d-m-Y", strtotime($this->fecha))).' Fecha de consulta: '.date("d-m-Y H:i:s").'  Estado: '.$this->estimacion->estado_descripcion, 0, 0, 'L');
        $this->SetXY(22.6,-0.9);
        $this->Cell(5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
       // $this->estatus(); /* Marca de agua : "Propuesta de estimación"
    }

    public function estatus()
    {
        if ($this->estimacion->estado === '0') {
            $this->SetFont('Arial', 'B', 91);
            $this->SetTextColor(155, 155, 155);
            $this->SetAlpha(0.30);
            $this->RotatedText(4, 17, utf8_decode("PROPUESTA DE "), 35);
            $this->RotatedText(9, 17, utf8_decode("ESTIMACIÓN"), 35);
            $this->SetTextColor('0,0,0');
        }
    }

    public function create()
    {
        $this->SetMargins(0.4, 0.5, 0.4);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,3.75);

        $this->partidas();
        $this->partidaDeductiva($this->estimacion->descuentosPartidas());
        $this->partidaRetenciones();
        $this->partidaRetencionesLiberadas();
        $this->partidaPenalizaciones();
        $this->partidaPenalizacionesLiberadas();
        $this->tablaResumen();
        try {
            $this->Output('I', "Formato - Estimacion_".$this->conceptos_ordenados['folio'].".pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    public  function SetAlpha($alpha, $bm='Normal')
    {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
        $this->SetExtGState($gs);
    }

    public function AddExtGState($parms)
    {
        $n = count($this->extgstates)+1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    public function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
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
