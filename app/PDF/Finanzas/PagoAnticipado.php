<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 11/06/2019
 * Time: 11:01 AM
 */

namespace App\PDF\Finanzas;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\TipoTransaccion;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\SolicitudPagoAnticipado;
use Illuminate\Support\Facades\App;



class PagoAnticipado extends Rotation
{
    protected $obra;
    protected $pagoAnticipado;
    private $encabezado_pdf = '';
    var $encola = '';


    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     * PagoAnticipado constructor.
     * @param $pagoAnticipado
     */

    public function __construct($id)
    {

        parent::__construct('P', 'cm', 'A4');

        $this->obra = Obra::find(Context::getIdObra());
        $this->pagoAnticipado = SolicitudPagoAnticipado::with("subcontrato", "empresa", "usuario", "orden_compra")->find($id);

        /*Header*/
        $this->folio = $this->pagoAnticipado->numero_folio_format;
        $this->fechaCompleta = str_replace("/", "-", substr($this->pagoAnticipado->fecha_format, 0, 10));
        $this->hora = substr($this->pagoAnticipado->FechaHoraRegistro, 11, 18);
        $this->fecha_limite = substr($this->pagoAnticipado->vencimiento, 0, 10);
        $this->fecha_solicitud = $this->pagoAnticipado->cumplimiento;
        $this->empresa_razon = $this->pagoAnticipado->empresa->razon_social;
        $this->observaciones = $this->pagoAnticipado->observaciones;



        if (!empty($this->pagoAnticipado->subcontrato)){

            $this->id_antecedente=$this->pagoAnticipado->id_antecedente;
            $this->aux=$this->pagoAnticipado->subcontrato->tipo_transaccion;
            $this->id_tipoAntecedente=TipoTransaccion::where("Tipo_Transaccion",$this->pagoAnticipado->subcontrato->tipo_transaccion)->get();
            $this->transaccion_antecedente=$this->id_tipoAntecedente[0]->Descripcion;
            $this->folio_antecedente=str_pad($this->pagoAnticipado->subcontrato->numero_folio, 5, "0", STR_PAD_LEFT);
            $this->fecha_antecedente=substr($this->pagoAnticipado->subcontrato->fecha,0,10);
            $this->observaciones_antecedente=$this->pagoAnticipado->subcontrato->observaciones;
            $this->referencia=$this->pagoAnticipado->subcontrato->referencia;
            $this->iva=number_format( $this->pagoAnticipado->subcontrato->impuesto,2,'.',',');
            $this->monto= number_format( $this->pagoAnticipado->subcontrato->monto,2,'.',',');
            $this->subtotal=number_format(doubleval(str_replace(",","",$this->monto))-doubleval(str_replace(",","",$this->iva)),2,".",",");


            $this->total_format="$ ".$this->monto;

        }


        if(!empty($this->pagoAnticipado->orden_compra)){
            $this->id_antecedente=$this->pagoAnticipado->id_antecedente;
            $this->aux=$this->pagoAnticipado->tipo_transaccion;
            $this->id_tipoAntecedente=TipoTransaccion::where("Tipo_Transaccion",$this->pagoAnticipado->tipo_transaccion)->get();
            $this->transaccion_antecedente=$this->id_tipoAntecedente[0]->Descripcion;



            $this->folio_antecedente=str_pad($this->pagoAnticipado->orden_compra->numero_folio, 5, "0", STR_PAD_LEFT);
            $this->fecha_antecedente=substr($this->pagoAnticipado->orden_compra->fecha,0,10);
            $this->observaciones_antecedente=$this->pagoAnticipado->orden_compra->observaciones;
            $this->referencia=$this->pagoAnticipado->orden_compra->referencia;

            $this->iva=number_format($this->pagoAnticipado->orden_compra->impuesto,2,".",",");
            $this->monto=$this->pagoAnticipado->orden_compra->monto;
            $this->subtotal=number_format(doubleval($this->monto)-doubleval($this->iva),2,".",",");

            $this->total_format="$ ".number_format($this->monto,2,".", ",");

        }


        /*Costos*/
        $this->monto=$this->pagoAnticipado->monto;
        $this->total_format=$this->pagoAnticipado->monto_format;
        $this->subtotal=number_format( doubleval($this->monto)*0.84,2,'.',',');
        $this->iva=number_format(doubleval($this->monto)*0.16,2,'.',',');

        $this->encabezado_pdf = utf8_decode('Solicitud de Pago Anticipado');


        $this->SetAutoPageBreak(true, 5);
        $this->WidthTotal = $this->GetPageWidth() - 2;
        $this->txtTitleTam = 18;
        $this->txtSubtitleTam = 13;
        $this->txtSeccionTam = 9;
        $this->txtContenidoTam = 11;
        $this->txtFooterTam = 6;


        $this->nombre_obra = $this->obra->descripcion;
        $this->razon_social = $this->obra->facturar;

        $this->empresa = $this->obra->facturar;


        $this->empresa_direccion = $this->obra->direccion;
        $this->rfc = strtoupper($this->obra->rfc);

        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,3.75);
        $this->fechas();
        $this->EmpresaPagoAnticipado();
        $this->antecedente();
        $this->observaciones();
        if($this->y > 18.05) {
            $this->AddPage();
            $this->Ln(.8);
        }
        $this->costos();
    }
    function Header()
    {
        $this->setXY(1, 2);
        $this->SetFont('Arial', 'B', 20);
        $this->CellFitScale(1* $this->WidthTotal, 0.1, $this->encabezado_pdf, '', 'CB');

        //Obtener Posiciones despues de los títulos
        $y_inicial = $this->getY() - 1;
        $x_inicial = $this->GetPageWidth() / 1.48;
        $this->setY($y_inicial);
        $this->setX($x_inicial);

        //Tabla Detalles de la Asignación
        $this->detallesSolicitudPagoAnticipado($x_inicial);

        //Posiciones despues de la primera tabla
        $y_final = $this->getY();

        $this->setXY($x_inicial, $y_inicial);

        $alto = abs($y_final - $y_inicial);

        //Redondear Bordes Detalles Asignacion
        $this->SetWidths(array(0.3 * $this->WidthTotal));

        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array($alto));
        $this->SetStyles(array('DF'));
        $this->SetAligns("L");
        $this->SetFont('Arial', '', $this->txtContenidoTam);
        $this->setXY($x_inicial, $y_inicial);


        $this->setXY($x_inicial, $y_inicial);


        //Tabla Detalles de la Asignacion
        $this->setY($y_inicial);
        $this->setX($x_inicial);
        $this->detallesSolicitudPagoAnticipado($x_inicial);

        //Obtener Y después de la tabla
        $this->setY($y_final);
        $this->Ln(1);

        $this->SetFont('Arial', 'B', 13);

        $this->SetWidths(array(19.5));
        $this->SetRounds(array('1234'));
        $this->SetRadius(array(0.2));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(0.7));
        $this->SetRounds(array('1234'));
        $this->SetRadius(array(0.2));
        $this->SetAligns("C");
        $this->Row(array(utf8_decode($this->nombre_obra)));
        $this->Ln(.2);
        $this->SetFont('Arial', '', 10);
        $this->Cell(10);
        $this->Cell(9.5, .7, utf8_decode('EMPRESA'), 0, 0, 'L');
        $this->Ln(.6);
        $y_inicial = $this->getY();
        $this->Cell(10);
        $this->SetFont('Arial', 'B', 10);
        $this->CellFitScale(9.5, .5, utf8_decode($this->empresa), '', 'J');
        $this->Ln(.5);
        $this->Cell(10);
        $this->SetFont('Arial', '', 10);
        $this->Multicell(9.5, .5, utf8_decode($this->empresa_direccion) . '
RFC: ' . $this->rfc, '', 'J');
        $y_final = $this->getY();
        $alto = $y_final - $y_inicial;

        $this->SetWidths(array(10, 9.5));
        $this->SetRounds(array('1234', '1234'));
        $this->SetRadius(array(0.2, 0.2));
        $this->SetFills(array('255,255,255', '255,255,255'));
        $this->SetTextColors(array('0,0,0', '0,0,0'));
        $this->SetHeights(array($alto));
        $this->SetStyles(array('F', 'DF'));
        $this->SetAligns("L");
        $this->SetFont('Arial', 'B', 10);
        $this->setY($y_inicial);
        $this->Row(array("", ""));
        $this->setY($y_inicial);
        /* se sobre escribe la información */
        $this->Cell(10);
        $this->SetFont('Arial', 'B', 10);
        $this->CellFitScale(9.5, .5, utf8_decode($this->empresa), '', 'J');
        $this->Ln(.5);
        $this->Cell(10);
        $this->SetFont('Arial', '', 10);
        $this->Multicell(9.5, .5, utf8_decode($this->empresa_direccion . '
RFC: ' . $this->rfc), '', 'J');


        $this->Ln(.2);
        $this->SetWidths(array(19.5));
        $this->SetRounds(array('12'));
        $this->SetRadius(array(0.2));
        $this->SetFills(array('180,180,180'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetStyles(array('DF'));
        $this->SetHeights(array(0.5));
        $this->SetFont('Arial', '', 6);
        $this->SetAligns(array('C'));
        $this->Ln(.5);


      if ($this->encola == "observaciones_encabezado") {
            $this->SetWidths(array(19.5));
            $this->SetRounds(array('12'));
            $this->SetRadius(array(0.2));
            $this->SetFills(array('180,180,180'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetHeights(array(0.3));
            $this->SetFont('Arial', '', 6);
            $this->SetAligns(array('C'));
        } else if ($this->encola == "observaciones") {
            $this->SetRounds(array('34'));
            $this->SetRadius(array(0.2));
            $this->SetAligns(array('J'));
            $this->SetStyles(array('DF'));
            $this->SetFills(array('255,255,255'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetHeights(array(0.3));
            $this->SetFont('Arial', '', 6);
            $this->SetWidths(array(19.5));
        }


    }


    function fechas(){
        $this->Ln(.3);
        $this->setX(12.5);
        $this->SetWidths(array( 4, 4));
        $this->SetFont('Arial', '', 6);
        $this->SetStyles(array('DF', 'DF'));
        $this->SetWidths(array(4,  4));
        $this->SetRounds(array('1', '2'));
        $this->SetRadius(array(0.2,  0.2));
        $this->SetFills(array('180,180,180',  '180,180,180'));
        $this->SetTextColors(array('0,0,0', '0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetAligns(array('C',  'C'));

        $this->Row(array("Fecha de Solicitud", utf8_decode("Fecha Límite de Pago")));

        $this->setX(12.5);
        $this->SetFont('Arial', '', 6);
        $this->SetWidths(array(4, 4));
        $this->SetRounds(array('4', '3'));
        $this->SetRadius(array(0.2, 0.2));
        $this->SetFills(array('255,255,255', '255,255,255', ));
        $this->SetTextColors(array('0,0,0', '0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetAligns(array('C', 'C'));

        $this->Row(array(date("d-m-Y",strtotime($this->fecha_solicitud)), date("d-m-Y",strtotime($this->fecha_limite))));
    }


    function EmpresaPagoAnticipado(){


            $this->Ln(1);
            $this->SetWidths(array(19.5));
            $this->SetRounds(array('12'));
            $this->SetRadius(array(0.2));
            $this->SetFills(array('180,180,180'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetStyles(array('DF'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 6);
            $this->SetAligns(array('C'));
            $this->Row(array("Empresa"));
            $this->SetRounds(array('34'));
            $this->SetRadius(array(0.2));
            $this->SetAligns(array('C'));
            $this->SetStyles(array('DF'));
            $this->SetFills(array('255,255,255'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 6);
            $this->Row(array(utf8_decode(str_replace(array("\r", "\n"), '', $this->empresa_razon))));


    }
    function detallesSolicitudPagoAnticipado($x)
    {

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FOLIO'), 'LT', 0, 'L');
        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->Cell(0.207 * $this->WidthTotal, 0.5, utf8_decode("# ".$this->folio), 'RT', 1, 'L');

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FECHA'), 'L', 0, 'L');
        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->Cell(0.207 * $this->WidthTotal, 0.5, date($this->fechaCompleta), 'R', 1, 'L');

        $this->SetFont('Arial', 'B', 9);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('HORA'), 'LB', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.207 * $this->WidthTotal, 0.5,utf8_decode($this->hora), 'RB', 1, 'L');

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


    function observaciones()
    {

        if(strlen($this->observaciones)>2000){
            $this->AddPage();
            $this->Ln(.8);
        }
        $this->Ln(.8);
        $this->SetWidths(array(19.5));
        $this->SetRounds(array('12'));
        $this->SetRadius(array(0.2));
        $this->SetFills(array('180,180,180'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetFont('Arial', '', 6);
        $this->SetAligns(array('C'));
        $this->encola = "observaciones_encabezado";
        $this->Row(array(utf8_decode("Motivo de Solicitud")));
        $this->SetRounds(array('34'));
        $this->SetRadius(array(0.2));
        $this->SetAligns(array('J'));
        $this->SetStyles(array('DF'));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetFont('Arial', '', 6);
        $this->SetWidths(array(19.5));
        $this->encola = "observaciones";
        if ($this->observaciones != null) {
            $this->Row(array(utf8_decode(str_replace(array("\r", "\n"), '',  $this->observaciones))));
        }else {
            $this->Row(array(""));
        }
    }
    function antecedente()
    {

        if(!empty($this->id_tipoAntecedente)){
            $this->Ln(.8);
            $this->SetFont('Arial', '', 10);
            $this->Cell(9.5, .7, utf8_decode('TRANSACCIÓN ANTECEDENTE'), 0, 0, 'L');
            $this->Ln(.8);

            $this->SetWidths(array(3,2,2,12.5));
            $this->SetRounds(array('1','','','2',));
            $this->SetRadius(array(0.2,0,0,0.2));
            $this->SetFills(array('180,180,180','180,180,180','180,180,180','180,180,180'));
            $this->SetTextColors(array('0,0,0','0,0,0','0,0,0','0,0,0'));
            $this->SetStyles(array('DF','DF','DF','DF'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 6);
            $this->SetAligns(array('C','C','C','C'));
            $this->Row(array("Tipo","Folio","Fecha","Referencia"));




            $this->SetRounds(array('4','','','3'));
            $this->SetRadius(array(0.2,0,0,0.2));
            $this->SetAligns(array('L','L','L','L'));
            $this->SetStyles(array('DF','DF','DF','DF'));
            $this->SetFills(array('255,255,255','255,255,255','255,255,255','255,255,255'));
            $this->SetTextColors(array('0,0,0','0,0,0','0,0,0','0,0,0'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 6);

            $this->Row(array(utf8_decode($this->transaccion_antecedente),"# ".$this->folio_antecedente,date("d-m-Y",strtotime($this->fecha_antecedente)),utf8_decode($this->referencia)));




            $this->Ln(1);
            $this->SetWidths(array(19.5));
            $this->SetRounds(array('12'));
            $this->SetRadius(array(0.2));
            $this->SetFills(array('180,180,180'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetStyles(array('DF'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 6);
            $this->SetAligns(array('C'));
            $this->Row(array(utf8_decode("Observaciones transacción antecedente")));
            $this->SetRounds(array('34'));
            $this->SetRadius(array(0.2));
            $this->SetAligns(array('L'));
            $this->SetStyles(array('DF'));
            $this->SetFills(array('255,255,255'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 6);
            $this->Row(array(utf8_decode($this->observaciones_antecedente)));
        }

    }


    function costos(){
//        $this->Ln(.8);
//        $this->setX(12.5);
//        $this->SetWidths(array( 4, 4));
//        $this->SetFont('Arial', '', 6);
//        $this->SetStyles(array('DF', 'DF'));
//        $this->SetWidths(array(4,  4));
//        $this->SetRounds(array('1', '2'));
//        $this->SetRadius(array(0.2,  0.2));
//        $this->SetFills(array('180,180,180',  '255,255,255'));
//        $this->SetTextColors(array('0,0,0', '0,0,0'));
//        $this->SetHeights(array(0.5));
//        $this->SetAligns(array('R',  'R'));
//        $this->Row(array("Subtotal:", "$ ".(string)$this->subtotal));


//        $this->setX(12.5);
//        $this->SetWidths(array( 4, 4));
//        $this->SetFont('Arial', '', 6);
//        $this->SetStyles(array('DF', 'DF'));
//        $this->SetWidths(array(4,  4));
//        $this->SetRounds(array('', ''));
//        $this->SetRadius(array(0.2,  0.2));
//        $this->SetFills(array('180,180,180',  '255,255,255'));
//        $this->SetTextColors(array('0,0,0', '0,0,0'));
//        $this->SetHeights(array(0.5));
//        $this->SetAligns(array('R',  'R'));
//        $this->Row(array("IVA:", "$ ".(string)$this->iva));

        $this->Ln(.8);
        $this->setX(12.5);
        $this->SetWidths(array( 4, 4));
        $this->SetFont('Arial', '', 6);
        $this->SetStyles(array('DF', 'DF'));
        $this->SetWidths(array(4,  4));
        $this->SetRounds(array('', ''));
        $this->SetRadius(array(0.2,  0.2));
        $this->SetFills(array('180,180,180',  '255,255,255'));
        $this->SetTextColors(array('0,0,0', '0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetAligns(array('R',  'R'));
        $this->Row(array("Monto Solicitado:", $this->total_format));
    }


    function firmas() {

        $this->SetY(-3.5);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);




            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Realizó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Recibió'), 'TRLB', 1, 'C', 1);


            $this->Cell(($this->GetPageWidth() - 4) / 4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 1.2, '', 'TRLB', 1, 'C');


            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4,  utf8_decode('RESPONSABLE DE ÁREA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4,  utf8_decode('GERENCIA DE ÁREA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4,  utf8_decode('DIRECCIÓN DE ÁREA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4,  utf8_decode('ADMINISTRACIÓN'), 'TRLB', 0, 'C', 1);




    }

    function Footer(){
        if (!App::environment('production')) {
            $this->SetFont('Arial','B',80);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(5,20,utf8_decode("MUESTRA"),45);
            $this->RotatedText(6,26,utf8_decode("SIN VALOR"),45);
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
        $this->Cell(7, .4, utf8_decode('Formato generado desde el módulo de Solicitud de Pagos Anticipados. Fecha de registro: '.date("Y-m-d H:m:s", strtotime($this->fecha_solicitud))), 0, 0, 'L');

        $this->Ln(.5);
        $this->SetY(-0.9);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(19.5, .5, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create() {
        try {
            $this->Output('I', 'Formato - Pago Anticipado.pdf', 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }
}
