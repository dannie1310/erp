<?php


namespace App\PDF\Contratos;

use App\Facades\Context;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Item;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\TipoTransaccion;
use Carbon\Carbon;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;


class EstimacionFormato extends Rotation
{
    protected $obra;
    protected $estimacion;
    private $encabezado_pdf = '';
    protected $extgstates = array();


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

        parent::__construct('L', 'cm', 'A4');

         $this->obra = Obra::find(Context::getIdObra());
         $this->id=$id;
         $this->encabezado_pdf = utf8_decode($this->obra->facturar);
//         $this->estimacion = Estimacion::with('empresa','subcontratoEstimacion','moneda','item','item.concepto', 'item.contrato')->find($id);

        $this->estimacion = Estimacion::query()->find($id);
         $this->contratista =$this->estimacion->empresa->razon_social;
         $this->folio_consecutivo=$this->estimacion->subcontratoEstimacion->NumeroFolioConsecutivo;

         $this->id_antecedente=$this->estimacion->id_antecedente;
         $this->subcontrato = Subcontrato::find($this->id_antecedente);
         $this->no_contrato = $this->subcontrato->referencia;


         $this->moneda = $this->estimacion->moneda->nombre;
         $this->items = $this->estimacion->items;



         $this->numero_folio= str_pad($this->estimacion->numero_folio,6, 0, STR_PAD_LEFT);
         $this->fecha=Carbon::parse($this->estimacion->fecha)->format('d-m-Y');
         $this->fecha_inicial=Carbon::parse($this->estimacion->cumplimiento)->format('d-m-Y');
         $this->fecha_final =Carbon::parse($this->estimacion->vencimiento)->format('d-m-Y');


         $this->tran_antecedentes=0;
         $this->suma_contrato=0;
         $this->suma_estimacionAnterior=0;
         $this->suma_estimacion=0;
         $this->suma_acumulada=0;
         $this->suma_porEstimar=0;
         $this->WidthTotal = $this->GetPageWidth() - 2;

    }


    function logo() {
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
        $this->MultiCell(14, 0.5, $this->encabezado_pdf, '', 'C');

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
        $this->Cell(0.207 * $this->WidthTotal, 0.5, utf8_decode("#".$this->numero_folio), 'RT, RB', 1, 'R');

        $this->SetFont('Arial', 'B', 12);
        $this->SetX($x_inicial);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('No. Estimación'), 'L, LR, LB', 0, 'L');
        $this->SetFont('Arial', 'B', "");
        $this->Cell(0.207 * $this->WidthTotal, 0.5, $this->folio_consecutivo, 'RB', 1, 'R');

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
        $this->setXY(1, 4.2);
        $this->Cell(0.250 * $this->WidthTotal, 0.5, utf8_decode('Organización:'), 'LB, LR, LT', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.757 * $this->WidthTotal, 0.5,utf8_decode($this->obra->nombre), 'RB, LT', 1, 'C');

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0.250 * $this->WidthTotal, 0.5, utf8_decode('Contratista:'), 'LB, LR, LT', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.757* $this->WidthTotal, 0.5,utf8_decode($this->contratista), 'RB, LT', 1, 'C');

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0.250 * $this->WidthTotal, 0.5, utf8_decode('No. de Contrato:'), 'LB, LR, LT', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.757 * $this->WidthTotal, 0.5,utf8_decode($this->no_contrato), 'RB, LT', 1, 'C');

        $this->tableHeader();
        $currentPage = $this->PageNo();

        if($currentPage>1){

            $this->Ln();

        }



    }

    public function tableHeader(){


        $this->Ln();

        $this->SetFills(180,180,180);
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(180,180,180);
        $this->Cell(0.250 * $this->WidthTotal,0.8,'Concepto','RTLB',0,'C',180);   // empty cell with left,top, and right borders
        $this->Cell(0.05 * $this->WidthTotal,0.8,'U.M.','RTLB',0,'C',180);
        $this->Cell(0.06 * $this->WidthTotal,0.8,'P.U.','RTLB',0,'C',180);
        $this->Cell(0.130 * $this->WidthTotal,0.4,'Contrato y Aditamentos ','BTLR',0,'C',180);
        $this->Cell(0.130 * $this->WidthTotal,0.4,utf8_decode("Acum. A Estimación Anterior"),'BTLR',0,'C',180);
        $this->Cell(0.130 * $this->WidthTotal,0.4,utf8_decode("Esta Estimación "),'BTLR',0,'C',180);
        $this->Cell(0.130 * $this->WidthTotal,0.4,utf8_decode("Acum. A Esta Estimación "),'BTLR',0,'C',180);
        $this->Cell(0.128 * $this->WidthTotal,0.4,utf8_decode("Saldo por Estimar "),'BTLR',0,'C',180);


        $this->setXY(10.97, 6.6);
        $this->Cell((0.130 * $this->WidthTotal)/2,0.4,'Cantidad','LRBT',0,'C',180);
        $this->Cell((0.130 * $this->WidthTotal)/2,0.4,'Importe','LRBT',0,'C',180);


        $this->setXY(10.97+(0.130 * $this->WidthTotal), 6.6);
        $this->Cell((0.130 * $this->WidthTotal)/2,0.4,'Cantidad','LRBT',0,'C',180);
        $this->Cell((0.130 * $this->WidthTotal)/2,0.4,'Importe','LRBT',0,'C',180);


        $this->setXY(10.97+(0.130 * $this->WidthTotal)*2, 6.6);
        $this->Cell((0.130 * $this->WidthTotal)/2,0.4,'Cantidad','LRBT',0,'C',180);
        $this->Cell((0.130 * $this->WidthTotal)/2,0.4,'Importe','LRBT',0,'C',180);


        $this->setXY(10.97+(0.130 * $this->WidthTotal)*3, 6.6);
        $this->Cell((0.130 * $this->WidthTotal)/2,0.4,'Cantidad','LRBT',0,'C',180);
        $this->Cell((0.130 * $this->WidthTotal)/2,0.4,'Importe','LRBT',0,'C',180);

        $this->setXY(10.97+(0.130 * $this->WidthTotal)*4, 6.6);
        $this->Cell((0.128 * $this->WidthTotal)/2,0.4,'Cantidad','LRBT',0,'C',180);
        $this->Cell((0.128 * $this->WidthTotal)/2,0.4,'Importe','LRBT',0,'C',180);



        $this->setXY(1, 7);
        $this->Cell(0.250 * $this->WidthTotal,0.4,'OBRA EJECUTADA',0,0,'L',0);

        $this->setXY(1+(0.300 * $this->WidthTotal), 7);
        $this->Cell(0.06 * $this->WidthTotal,0.4,$this->moneda,0,0,'C',0);

        $this->setXY(1+(0.428* $this->WidthTotal), 7);
        $this->Cell(0.06 * $this->WidthTotal,0.4,$this->moneda,0,0,'C',0);


        $this->setXY(1+(0.558* $this->WidthTotal), 7);
        $this->Cell(0.06 * $this->WidthTotal,0.4,$this->moneda,0,0,'C',0);

        $this->setXY(1+(0.688* $this->WidthTotal), 7);
        $this->Cell(0.06 * $this->WidthTotal,0.4,$this->moneda,0,0,'C',0);

        $this->setXY(1+(0.818* $this->WidthTotal), 7);
        $this->Cell(0.06 * $this->WidthTotal,0.4, $this->moneda,0,0,'C',0);

        $this->setXY(1+(0.948* $this->WidthTotal), 7);
        $this->Cell(0.06 * $this->WidthTotal,0.4,$this->moneda,0,0,'C',0);
        $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);


    }


    public function setWaterText($txt1="fdsgfd", $txt2="dfgsfds"){
        $this->_outerText1 = $txt1;
        $this->_outerText2 = $txt2;
    }





public function partidas(){

    $this->Ln();
    $this->SetFont('Arial', '', 5);
    $this->SetFillColor(180, 180, 180);
    $this->SetWidths([6.92, 1.39, 1.67, 1.80, 1.80, 1.80, 1.80, 1.80, 1.80, 1.80, 1.80, 1.78, 1.78]);
    $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF', 'DF', 'FD', 'FD', 'DF']);
    $this->SetRounds(['1', '', '', '', '', '', '', '', '', '', '', '', '2']);
    $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.0]);
    $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
    $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
    $this->SetHeights([0.4]);
    $this->SetAligns(['L', 'C', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R']);


    foreach ($this->estimacion->subcontrato->partidasOrdenadas as $i => $p) {
        $item_antecedente = $p->contrato->id_concepto;


        $this->contrato_importe = $p->precio_unitario * $p->cantidad;
        $this->suma_contrato += $this->contrato_importe;

        $this->tran_antecedentes = $p->cantidad_total_estimada;


        $this->importe_antecedentes = $this->tran_antecedentes * $p->precio_unitario;



        $estimacionItem=$p->getEstimacionPartidaAttribute($this->id);
        $aux=0;



        if($estimacionItem) {
            $this->suma_estimacion += $estimacionItem->importe;
            $this->cantidad_acumulada = $this->tran_antecedentes + $estimacionItem->cantidad;
            $this->importe_acumulado = $this->importe_antecedentes + $estimacionItem->importe;
            $this->suma_acumulada += $this->importe_acumulado;
            $this->cantidad_restante = $p->cantidad- $this->cantidad_acumulada;
            $this->importe_restante = $p->precio_unitario * $this->cantidad_restante;
            $this->suma_porEstimar+=$this->importe_restante;
            $this->Row([
                mb_strtoupper($p->contrato->descripcion),
                mb_strtoupper($p->contrato->unidad),
                number_format($p->precio_unitario, 3, ".", ","),
                number_format($p->cantidad, 3, ".", ","),
                number_format($this->contrato_importe,3, ".", ","),
                number_format($this->tran_antecedentes, 3, ".", ","),
                number_format($this->importe_antecedentes, 3, ".", ","),
                number_format($estimacionItem->cantidad, 3, ".", ","),
                number_format($estimacionItem->importe, 3, ".", ","),
                number_format($this->cantidad_acumulada, 3, ".", ","),
                number_format($this->importe_acumulado, 3, ".", ","),
                number_format($this->cantidad_restante, 3, ".", ","),
                number_format($this->importe_restante, 3, ".", ","),
            ]);



        }else{
            $this->cantidad_acumulada = $this->tran_antecedentes + $aux;
            $this->importe_acumulado = $this->importe_antecedentes + $p->importe;
            $this->suma_acumulada += $this->importe_acumulado;
            $this->cantidad_restante = $p->cantidad - $this->cantidad_acumulada;
            $this->importe_restante = $p->precio_unitario * $this->cantidad_restante;
            $this->suma_porEstimar+=$this->importe_restante;
            $this->Row([
                mb_strtoupper($p->contrato->descripcion),
                mb_strtoupper($p->contrato->unidad),
                number_format($p->precio_unitario, 3, ".", ","),
                number_format($p->cantidad, 3, ".", ","),
                number_format($this->contrato_importe,3, ".", ","),
                number_format($this->tran_antecedentes, 3, ".", ","),
                number_format($this->importe_antecedentes, 3, ".", ","),
                number_format($aux, 3, ".", ","),
                number_format($aux, 3, ".", ","),
                number_format($this->cantidad_acumulada, 3, ".", ","),
                number_format($this->importe_acumulado, 3, ".", ","),
                number_format($this->cantidad_restante, 3, ".", ","),
                number_format($this->importe_restante, 3, ".", ","),
            ]);
        }
    }




    /*Footer partidas*/

    $this->SetFills(180,180,180);
    $this->SetFont('Arial', '', 5);
    $this->SetFillColor(180,180,180);


    $this->Cell( 6.92,0.3,"Sub-totales Obra Ejecutada",'RTLB',0,'R',180);   // empty cell with left,top, and right borders
    $this->Cell(1.39,0.3,"",'RTLB',0,'C',180);
    $this->Cell(1.67,0.3,'','RTLB',0,'R',180);
    $this->Cell(1.80,0.3,' ','BTLR',0,'R',180);
    $this->Cell(1.80,0.3,number_format($this->suma_contrato,3, ".",","),'BTLR',0,'R',180);
    $this->Cell(1.80,0.3,'','BTLR',0,'R',180);
    $this->Cell(1.80,0.3,number_format($this->suma_estimacionAnterior,3, ".",","),'BTLR',0,'R',180);
    $this->Cell(1.80,0.3,' ','BTLR',0,'R',180);
    $this->Cell(1.80,0.3,number_format($this->suma_estimacion,3, ".",","),'BTLR',0,'R',180);
    $this->Cell(1.80,0.3,' ','BTLR',0,'R',180);
    $this->Cell(1.80,0.3,number_format($this->suma_acumulada,3, ".",","),'BTLR',0,'R',180);
    $this->Cell(1.78,0.3,'','BTLR',0,'R',180);
    $this->Cell(1.78,0.3,number_format($this->suma_porEstimar,3, ".",","),'BTLR',0,'R',180);




    }



    function pixelsToCM($val) {
        return ($val * self::MM_IN_INCH / self::DPI) / 10;
    }


    function resizeToFit($imgFilename) {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);
        return [
            round($this->pixelsToCM($scale * $width)),
            round($this->pixelsToCM($scale * $height))
        ];
    }



    function firmas() {


        if (Context::getDatabase() == "SAO1814_PISTA_AEROPUERTO"){

        $this->SetY(-3.5);
        $this->SetTextColor('0', '0', '0');
        $this->SetFillColor(180, 180, 180);
        $this->SetFont('Arial', 'B', 4.5);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode(''), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('ELABORÓ'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('AVALÓ'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('REVISÓ'), 'TRLB', 1, 'C', 1);


        $this->SetXY(14.75,-3.5);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('Vo.Bo.'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('REVISÓ'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('AUTORIZÓ'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4, utf8_decode('REVISÓ/RECIBIÓ'), 'TRLB', 0, 'C', 1);

        $this->Ln();
        $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 0, 'C');
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 0, 'C');
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRL', 0, 'C');

        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 1, 'C');

        $this->SetXY(14.75,-3.1);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 1, 'C');
        $this->SetXY(18.19, -3.1);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 1, 'C');
        $this->SetXY(18.19+0.73+($this->GetPageWidth() - 8) / 8, -3.1);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 1, 'C');
        $this->SetXY(18.19+(0.73+($this->GetPageWidth() - 8) / 8)*2, -3.1);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 1.2, '', 'TRLB', 1, 'C');






        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4,  utf8_decode('CONCILIADOR'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4,  utf8_decode('DEPARTAMENTO'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4,  utf8_decode('SUPERINTENDENCIA DE OBRA'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4,  utf8_decode('CALIDAD'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.71);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4,  utf8_decode('SUBCONTRATOS'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4,  utf8_decode('SUPERINTENDENCIA TÉCNICA'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4,  utf8_decode('GERENCIA DE PROYECTO'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 8) / 8, 0.4,  utf8_decode('ADMINISTRACIÓN'), 'TRLB', 0, 'C', 1);



        $this->SetXY(7.89,-2.3);
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



        } else if(Context::getDatabase() == "SAO1814_TUNEL_DRENAJE_PRO") {
            /*Firmas en Gral*/
            $this->SetY(-3.5);
            $this->SetTextColor('0', '0', '0');
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);



            $this->SetFont('Arial', 'B', 4.2);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('ELABORÓ'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('REVISÓ'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('AVALÓ'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 1, 'C', 1);


            $this->SetXY(13.1,-3.5);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Vo.Bo.'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('AUTORIZÓ'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('RECIBE'), 'TRLB', 0, 'C', 1);



            $this->Ln();
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 0, 'C');

            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');

            $this->SetXY(13.1,-3.1);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');
            $this->SetXY(16.13, -3.1);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');
            $this->SetXY(16.13 +0.73+($this->GetPageWidth() - 9) / 9, -3.1);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');
            $this->SetXY(16.13+(0.73+($this->GetPageWidth() - 9) / 9)*2, -3.1);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');
            $this->SetXY(16.13+(0.73+($this->GetPageWidth() - 9) / 9)*3, -3.1);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');





            $this->SetFont('Arial', 'B', 4.2);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('CONTRATISTA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('CONTROL DE ESTIMACIONES'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('SUPERINTENDENCIA DE OBRA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('CALIDAD'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.71);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('CONTROL DE PLANEACIÓN'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('CONTROL DE SEGUIMIENTO'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('SUBCONTRATOS'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('DIRECTOR DE PROYECTO'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('ADMINISTRADOR'), 'TRLB', 0, 'C', 1);


            $this->SetXY(1,-2.3);
            $this->SetFont('Arial', 'B', 3.6);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode(''), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode(''), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode(''), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode(''), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Ing. Giselle Belran Baños'), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Ing. Lazaro Romero Zamora'), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Ing. Guadalupe Moreno Hernández'), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Ing. Luis Alfonso Hernández Reding'), '', 0, 'C', 0);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Ing. Francisco Javier Bay Ortuazar'), '', 0, 'C', 0);

            $this->SetXY(16,-2.5);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Ing. Martin Morales Sanchéz o'), '', 0, 'R', 0);


        }else if(Context::getDatabase() == "SAO1814_TERMINAL_NAICM"){
            $this->SetY(-3.5);
            $this->SetTextColor('0', '0', '0');
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);



            $this->SetFont('Arial', 'B', 4.5);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 1, 'C', 1);


            $this->SetXY(23.75,-3.5);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4, utf8_decode('RECIBE'), 'TRLB', 0, 'C', 1);


            $this->Ln();
            $this->Cell(($this->GetPageWidth() - 5) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 1.2, '', 'TRLB', 1, 'C');

            $this->SetXY(23.75,-3.1);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 1.2, '', 'TRLB', 1, 'C');
            $this->SetXY(18.19, -3.1);


            $this->SetXY(1, -1.9);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4,  utf8_decode('CONTROL DE ESTIMACIONES'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4,  utf8_decode('CALIDAD'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4,  utf8_decode('CONTROL DE PROYECTOS'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4,  utf8_decode('DIRECTOR DE ÁREA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.79);
            $this->Cell(($this->GetPageWidth() - 5) / 5, 0.4,  utf8_decode('ADMINISTRACIÓN'), 'TRLB', 0, 'C', 1);

        } else{
            /*Firmas en Gral*/
            $this->SetY(-3.5);
            $this->SetTextColor('0', '0', '0');
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);



            $this->SetFont('Arial', 'B', 4.2);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('ELABORÓ'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('REVISÓ'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('AVALÓ'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 1, 'C', 1);


            $this->SetXY(13.1,-3.5);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Vo.Bo.'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('Vo.Bo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('AUTORIZÓ'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4, utf8_decode('RECIBE'), 'TRLB', 0, 'C', 1);



            $this->Ln();
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 0, 'C');

            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');

            $this->SetXY(13.1,-3.1);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');
            $this->SetXY(16.13, -3.1);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');
            $this->SetXY(16.13 +0.73+($this->GetPageWidth() - 9) / 9, -3.1);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');
            $this->SetXY(16.13+(0.73+($this->GetPageWidth() - 9) / 9)*2, -3.1);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');
            $this->SetXY(16.13+(0.73+($this->GetPageWidth() - 9) / 9)*3, -3.1);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 1.2, '', 'TRLB', 1, 'C');





            $this->SetFont('Arial', 'B', 4.2);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('CONTRATISTA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('CONTROL DE ESTIMACIONES'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('SUPERINTENDENCIA DE OBRA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('CALIDAD'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.71);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('CONTROL DE PLANEACIÓN'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('CONTROL DE SEGUIMIENTO'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('SUBCONTRATOS'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('DIRECTOR DE PROYECTO'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 9) / 9, 0.4,  utf8_decode('ADMINISTRACIÓN'), 'TRLB', 0, 'C', 1);


        }

    }



    function Footer(){
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
        $this->Cell(10, .3, utf8_decode('Formato generado desde el módulo de estimaciones. Fecha de registro: ' . date("d-m-Y", strtotime($this->fecha))), 0, 0, 'L');
        $this->SetXY(24,-0.9);
        $this->Cell(5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
        $this->estatus();
    }





public function estatus(){

        if($this->estimacion->estado ==='0') {

            $this->SetFont('Arial', 'B', 91);
            $this->SetTextColor(155, 155, 155);
            $this->SetAlpha(0.30);
            $this->RotatedText(4, 17, utf8_decode("PROPUESTA DE "), 35);
            $this->RotatedText(9, 17, utf8_decode("ESTIMACIÓN"), 35);
            $this->SetTextColor('0,0,0');
        }
}

    public function create() {
        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();



        $this->SetAutoPageBreak(true,3.75);

        $this->partidas();
        try {
            $this->Output('I', "Formato - Estimacion_#$this->numero_folio.pdf", 1);
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
