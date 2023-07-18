<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 01/07/2019
 * Time: 10:35 AM
 */


namespace App\PDF\Compras;


use App\Models\CADECO\Almacen;
use App\Models\CADECO\OrdenCompraPartida;
use App\Models\CADECO\Compras\OrdenCompraPartidaComplemento;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\Concepto;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Entrega;
use App\Models\CADECO\Item;
use App\Models\CADECO\Material;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Sucursal;
use Carbon\Carbon;
use App\Facades\Context;
use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\Moneda;
use App\Models\CADECO\Cambio;
use App\Utils\PDF\FPDI\FPDI;
//use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\App;


class OrdenCompraFormato extends FPDI
{

    protected $obra;
    protected $ordenCompra;
    protected $objeto_ordenCompra;

    private $encola = '',
        $archivo='',
        $clausulado = '',
        $sin_texto= '',
        $con_fianza = 0,
        $tipo_orden = null,
        $encabezado_pdf,
        $conFirmaDAF = false,
        $id_tipo_fianza = 0,
        $folio_sao,
        $NuevoClausulado = 0,
        $dim=0,
        $dim_aux=0,
        $obs_item='',
        $id_antecedente;


    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     * OrdenCompraFormato constructor.
     * @param $ordenCompra
     */
    public function __construct($ordenCompra)
    {
        parent::__construct('P', 'cm', 'Letter');

        $this->obra = Obra::find(Context::getIdObra());

        $this->id_oc=$ordenCompra;


        $this->ordenCompra = OrdenCompra::with('solicitud','partidas','complemento')->where('id_transaccion', '=', $ordenCompra)->first();

        if($this->ordenCompra->complemento){
            $this->domicilio=$this->ordenCompra->complemento->domicilio_entrega;
            $this->plazo=$this->ordenCompra->complemento->plazos_entrega_ejecucion;
            $this->condiciones=$this->ordenCompra->complemento->otras_condiciones;
            $this->descuento=$this->ordenCompra->complemento->descuento;
        }else{
            $this->domicilio='';
            $this->plazo='';
            $this->condiciones='';
            $this->descuento='';
        }

        $this->fecha = substr($this->ordenCompra->fecha, 0, 10);
        $this->id_antecedente=$this->ordenCompra->id_antecedente;

        $this->sucursal=Sucursal::where('id_sucursal','=',$this->ordenCompra->id_sucursal )->get();
        $this->sucursal_direccion=$this->sucursal[0]->direccion;
        $this->id_empresa=$this->sucursal[0]->id_empresa;
        $this->empresa=Empresa::where('id_empresa','=',$this->id_empresa)->get();
        $this->empresa_nombre=$this->empresa[0]->razon_social;
        $this->empresa_rfc=$this->empresa[0]->rfc;

        $versiones = OrdenCompra::where('id_transaccion', '=', $this->ordenCompra->id_transaccion)->count();
        $this->folio_sao = $this->ordenCompra->numero_folio_format;
        $this->requisicion_folio_sao =str_pad($this->ordenCompra->solicitud->numero_folio_format, 5, '0', STR_PAD_LEFT);
        $this->obra_nombre = $this->obra->descripcion != null ? $this->obra->descripcion : $this->obra->nombre;



        // @ TODO clausulado provisional hasta definir estructura nueva -Gsus-
        $this->NuevoClausulado = 0;
        $this->archivo='';



        if (strtotime($this->fecha) >= '2019-04-08' and Context::getDatabase() <> "SAO1814_TERMINAL_NAICM" and  Context::getDatabase() <> "SAO1814_TUNEL_MANZANILLO" and  Context::getDatabase() <> "SAO1814_TROLEBUS" and  Context::getDatabase() <> "SAO1814_CUTZAMALA") {
            $this->NuevoClausulado = 1;
            $this->archivo = 'Clausulado_2019.pdf';
        } // fin if comparación de fecha
        else {
            if (Context::getDatabase() == "SAO_CORP") {
                $this->conFirmaDAF = true;
            } else {
                $this->conFirmaDAF = false;
            }
            switch (Context::getDatabase()) {
                case "SAO1814_SPM_MOBILIARIO":
                    $this->archivo = $this->ordenCompra->complemento->con_fianza == 0 ? 'ClausuladoHSPMSF.jpg' : 'ClausuladoHSPM.jpg';
                    break;
                case "SAO1814_MUSEO_BARROCO":
                    $this->archivo = $this->ordenCompra->complemento->con_fianza == 0 ? 'ClausuladoMIBSF.jpg' : 'ClausuladoMIB.jpg';
                    break;
                case "SAO1814_HOTEL_DREAMS_PM":
                    if (Context::getId() == 1) {
                        switch ($this->ordenCompra->complemento->con_fianza) {
                            case 0:
                                $this->archivo = "ClausuladoDreamsSF_COD.jpg";
                                break;
                            case 1:
                                $this->archivo = "ClausuladoDreams_COD.jpg";
                                break;
                            case 2:
                                $this->archivo = "ClausuladoDreams3F_COD.jpg";
                                break;
                            case 3:
                                $this->archivo = "ClausuladoDreams2FSVO_COD.jpg";
                                break;
                            case 4:
                                $this->archivo = "ClausuladoDreamsFA_COD.jpg";
                                break;
                            case 5:
                                $this->archivo = "ClausuladoDreamsPagare_COD.jpg";
                                break;
                            case 6:
                                $this->archivo = "ClausuladoDreamsPagYFBC_COD.jpg";
                                break;
                        }
                    } else {
                        switch ($this->ordenCompra->complemento->con_fianza) {
                            case 0:
                                $this->archivo = "ClausuladoDreamsSF.jpg";
                                break;
                            case 1:
                                $this->archivo = "ClausuladoDreams.jpg";
                                break;
                            case 2:
                                $this->archivo = "ClausuladoDreams3F.jpg";
                                break;
                            case 3:
                                $this->archivo = "ClausuladoDreams2FSVO.jpg";
                                break;
                            case 4:
                                $this->archivo = "ClausuladoDreamsFA.jpg";
                                break;
                            case 5:
                                $this->archivo = "ClausuladoDreamsPagare.jpg";
                                break;
                            case 6:
                                $this->archivo = "ClausuladoDreamsPagYFBC.jpg";
                                break;
                            case 7:
                                $this->archivo = "ClausuladoDreamsEspecialCintas.jpg";
                                break;
                        }
                    }
                    break;
                case "SAO1814_TERMINAL_NAICM":
                    $this->archivo = "Clausulado_ctvm.jpg";
                    break;
                case "SAO1814_TUNEL_DRENAJE_PRO":
                    $this->archivo = "Clausulado_tunel_drenaje_pro.jpg";
                    break;
                case "SAO1814_TUNEL_MANZANILLO":
                    if($this->ordenCompra->obra->id_obra == 3){
                        $this->archivo = "ClausuladoTransitsmico.pdf";
                    }else{
                        $this->archivo = "Clausulado_2019.pdf";
                    }
                    break;
                case "SAO1814_TROLEBUS":
                    if($this->ordenCompra->obra->id_obra == 1){
                        $this->archivo = "ClausuladoTrolebus.pdf";
                    }else{
                        $this->archivo = "Clausulado_2019.pdf";
                    }
                    break;
                case "SAO1814_CUTZAMALA":
                    if($this->ordenCompra->obra->id_obra == 4){
                        $this->archivo = "ClausuladoCutzamala.pdf";
                    }else{
                        $this->archivo = "Clausulado_2019.pdf";
                    }
                    break;
                default:
                    $this->archivo = "Clausulado_2019.pdf";
            }
        }

        //$this->clausulado_page=public_path('pdf/clausulados/'.$this->archivo);
        $this->SetAutoPageBreak(true, 3);

        $this->setSourceFile(public_path('pdf/ClausuladosPDF/'.$this->archivo));
        $this->clausulado = $this->importPage(1);
        $this->setSourceFile(public_path('pdf/ClausuladosPDF/SinTexto.pdf'));
        $this->sin_texto =  $this->importPage(1);

    }

    function Header()
    {
        $residuo = $this->PageNo() % 2;

        if ($residuo > 0) {
            //Es non

            $x_f = 13.5;

            $this->setXY(1, 1);
            $this->SetFont('Arial', 'B', 18);
            $this->MultiCell(13,"0.7",utf8_decode($this->ordenCompra->encabezado_pdf),0,"C");

            $this->setXY(13.5, 1);


            $this->SetTextColor(0,0,0);
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(3.5, .7, utf8_decode('NÚMERO '), 'LT', 0, 'L');
            $this->Cell(3.5, .7, $this->folio_sao, 'RT', 0, 'L');
            $this->Ln(.7);
            $y_f = $this->GetY();

            $this->SetY($y_f);
            $this->SetX($x_f);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(3.5, .7, 'FECHA ', 'L', 0, 'L');
            $this->Cell(3.5, .7, date("d-m-Y", strtotime($this->fecha)) . ' ', 'R', 0, 'L');
            $this->Ln(.7);

            $this->SetX($x_f);

            $this->Cell(3.5, .7, 'SOLICITUD ', 'LB', 0, 'L');
            $this->Cell(3.5, .7, $this->requisicion_folio_sao . ' ', 'RB', 1, 'L');
            $this->Ln(.5);

            $this->SetFont('Arial', 'B', 13);
            $this->SetWidths([19.5]);
            $this->SetRounds(['1234']);
            $this->SetRadius([0.2]);
            $this->SetFills(['255,255,255']);
            $this->SetTextColors(['0,0,0']);
            $this->SetHeights([0.7]);
            $this->SetRounds(['1234']);
            $this->SetRadius([0.2]);
            $this->SetAligns("C");

            $this->Row([utf8_decode($this->obra_nombre . '  ' . " ")]);
            $this->Ln(.5);
            $this->SetFont('Arial', '', 10);
            $this->Cell(9.5, .7, 'Proveedor', 0, 0, 'L');
            $this->Cell(.5);
            $this->Cell(9.5, .7, 'Cliente (Facturar a)', 0, 0, 'L');
            $this->Ln(.8);
            $y_inicial = $this->getY();
            $x_inicial = $this->getX();
            $this->MultiCell(9.5, .5,
                "" . utf8_decode($this->empresa_nombre).'
' . utf8_decode($this->sucursal_direccion) . '
' . $this->empresa_rfc, '', 'L');
            $y_final_1 = $this->getY();
            $this->setY($y_inicial);
            $this->setX($x_inicial + 10);
            $this->MultiCell(9.8, .5,
                utf8_decode($this->obra->facturar) . '
' . $this->obra->direccion . '
' . $this->obra->rfc, '', 'L');
            $y_final_2 = $this->getY();

            if ($y_final_1 > $y_final_2)
                $y_alto = $y_final_1;

            else
                $y_alto = $y_final_2;

            $alto = abs($y_inicial - $y_alto);
            $this->SetWidths([9.5]);
            $this->SetRounds(['1234']);
            $this->SetRadius([0.2]);
            $this->SetFills(['255,255,255']);
            $this->SetTextColors(['0,0,0']);
            $this->SetHeights([$alto]);
            $this->SetStyles(['DF']);
            $this->SetAligns("L");
            $this->SetFont('Arial', '', 10);
            $this->setY($y_inicial);
            $this->Row([""]);
            $this->setY($y_inicial);
            $this->setX($x_inicial);
            $this->MultiCell(9.5, .5,
                "" . utf8_decode($this->empresa_nombre).'
' . utf8_decode($this->sucursal_direccion) . '
' . $this->empresa_rfc, '', 'L');

            $this->setY($y_inicial);
            $this->setX($x_inicial + 10);
            $this->Row([""]);

            $this->setY($y_inicial);
            $this->setX($x_inicial + 10);
            $this->MultiCell(9.8, .5,
                utf8_decode($this->obra->facturar) . '
' . $this->obra->direccion . '
' . $this->obra->rfc, '', 'L');

            $this->setY($y_alto);
            $this->Ln(.5);

            $this->SetFont('Arial', '', 6);
            $this->SetHeights([0.8]);

            // Cuadro partidas
            if ($this->encola == "partida") {
                $this->SetFillColor(180, 180, 180);
                $this->SetWidths([0.5, 1.5, 1.5, 2, 7, 2, 1, 2, 2]);
                $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF']);
                $this->SetRounds(['1', '', '', '', '', '', '', '', '2']);
                $this->SetRadius([0.2, 0, 0, 0, 0, 0, 0, 0, 0.2]);
                $this->SetFills(['180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
                $this->SetHeights([0.4]);
                $this->SetAligns(['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C']);
                $this->Row(["#", "Cantidad", "Unidad", "No. Parte", utf8_decode("Descripción"), "Precio", "% Descto.", "Precio Neto", "Importe"]);
                $this->SetRounds(['', '', '', '', '', '', '', '', '']);
                $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
                $this->SetAligns(['C', 'R', 'C', 'L', 'L', 'R', 'R', 'R', 'R']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            } else if ($this->encola == "observaciones_partida") {
                $this->SetRadius([0]);
                $this->SetTextColors(['150,150,150']);
                $this->SetWidths([19.5]);
                $this->SetAligns(['J']);
            }

            else if ($this->encola == "observaciones_encabezado") {
                $this->SetWidths([19.5]);
                $this->SetRounds(['12']);
                $this->SetRadius([0.2]);
                $this->SetFills(['180,180,180']);
                $this->SetTextColors(['0,0,0']);
                $this->SetHeights([0.5]);
                $this->SetFont('Arial', '', 9);
                $this->SetAligns(['C']);
            } else if ($this->encola == "observaciones") {
                $this->SetRounds(['34']);
                $this->SetRadius([0.2]);
                $this->SetAligns(['J']);
                $this->SetStyles(['DF']);
                $this->SetFills(['255,255,255']);
                $this->SetTextColors(['0,0,0']);
                $this->SetHeights([0.5]);
                $this->SetFont('Arial', '', 9);
                $this->SetWidths([19.5]);
            }
        } else {
            //Es par y lleva encabezado corto

            if (Context::getDatabase() == "SAO1814_TERMINAL_NAICM") {
                $this->SetTextColor(0,0,0);
                $this->SetFont('Arial', 'B', 10);

                $this->setX(13.5);
                $this->Cell(3, .7, utf8_decode('NO. OC: '), 'LT', 0, 'L');
                $this->Cell(5, .7, $this->ordenCompra->numero_folio . ' ', 'RT', 0, 'L');
                $this->Ln(.5);

                $this->setX(13.5);
                $this->Cell(3, .7, utf8_decode('OBRA: '), 'L', 0, 'L');
                $this->Cell(5, .7, $this->obra_nombre  . ' ', 'R', 0, 'L');
                $this->Ln(.5);

                $this->SetFont('Arial', 'B', 10);
                $this->setX(13.5);
                $this->Cell(3, .5, 'FECHA: ', 'L', 0, 'L');
                $this->Cell(5, .5, date("d-m-Y", strtotime($this->fecha))  . ' ', 'R', 0, 'L');
                $this->Ln(.5);

                $this->SetFont('Arial', 'B', 10);
                $this->setX(13.5);
                $this->Cell(3, .5, 'NO. SOLICITUD: ', 'L', 0, 'L');
                $this->Cell(5, .5, $this->folio_sao  . ' ', 'R', 0, 'L');
                $this->Ln(.5);

                $this->setX(13.5);
                $this->Cell(4.5, .5, 'TOTAL: ', 'LB', 0, 'L');
                $this->Cell(3.5, .5, "$ " . number_format($this->ordenCompra->monto, 2, '.', ','), 'RB', 1, 'L');
                $this->Ln(.5);
            } else {
                $this->SetTextColor(0,0,0);
                $this->SetFont('Arial', 'B', 9);

                $this->setX(14);
                $this->Cell(2.5, .4, utf8_decode('NÚMERO: '), 'LT', 0, 'L');
                $this->Cell(4, .4, $this->folio_sao . ' ', 'RT', 0, 'L');
                $this->Ln(.4);

                $this->setX(14);
                $this->Cell(2.5, .4, utf8_decode('OBRA: '), 'L', 0, 'L');
                $this->Cell(4, .4, $this->obra_nombre  . ' ', 'R', 0, 'L');
                $this->Ln(.4);

                $this->SetFont('Arial', 'B', 9);
                $this->setX(14);
                $this->Cell(2.5, .4, 'FECHA: ', 'L', 0, 'L');
                $this->Cell(4, .4, date("d/m/Y", strtotime($this->fecha))  . ' ', 'R', 0, 'L');
                $this->Ln(.4);

                $this->SetFont('Arial', 'B', 9);
                $this->setX(14);
                $this->Cell(2.5, .4, 'SOLICITUD: ', 'L', 0, 'L');
                $this->Cell(4, .4, $this->requisicion_folio_sao   . ' ', 'R', 0, 'L');
                $this->Ln(.4);

                $this->setX(14);
                $this->Cell(2.5, .4, 'TOTAL: ', 'LB', 0, 'L');
                $this->Cell(4, .4, "$ " . number_format($this->ordenCompra->monto, 2, '.', ','), 'RB', 1, 'L');
                $this->Ln(.4);

                switch (Context::getDatabase()) {

                    case "SAO1814_TUNEL_MANZANILLO":
                        if($this->ordenCompra->obra->id_obra == 3){
                            $this->Ln(22);
                        }else{
                            $this->Ln(20);
                        }
                        break;
                    case "SAO1814_TROLEBUS":
                        if($this->ordenCompra->obra->id_obra == 1){
                            $this->Ln(21);
                        }else{
                            $this->Ln(20);
                        }
                        break;
                    case "SAO1814_CUTZAMALA":
                        if($this->ordenCompra->obra->id_obra == 4){
                            $this->Ln(21.25);
                        }else{
                            $this->Ln(20);
                        }
                        break;
                    default:
                        $this->Ln(20);
                }

                $this->SetFont('Arial', 'B', 4);

                $this->Cell(10.2);
                $this->Cell(4.6,.5, utf8_decode('"EL CLIENTE"') ,'LT',0,'C');
                $this->Cell(5,.5, utf8_decode('"EL PROVEEDOR"'),'LRT',0,'C');
                $this->Ln(.4);

                $this->Cell(10.2);
                $this->CellFitScale(4.6,1.6, '      ' ,'LT',0,'C');
                $this->CellFitScale(5,1.6,    '      ','LTR',0,'C');
                $this->Ln(1);


                $this->Cell(10.2);
                $this->CellFitScale(4.6,.4,utf8_decode($this->obra->facturar),'LT',0,'C');
                $this->CellFitScale(5,.4,utf8_decode($this->empresa_nombre) ,'LTR',0,'C');
                $this->Ln(.4);


                $this->Cell(10.2);
                $this->Cell(4.6,.2, 'APODERADO LEGAL, FACTOR o','LTR',0,'C');
                $this->Cell(5,.2, 'APODERADO LEGAL, FACTOR o','RT',0,'C');
                $this->Ln(.2);

                $this->Cell(10.2);

                $this->Cell(4.6,.2, 'DEPENDIENTE','LB',0,'C');
                $this->Cell(5,.2, 'DEPENDIENTE','LRB',0,'C');
                $this->Ln(.2);
            }

        }
        $this->y_subtotal = $this->GetY();
        $this->SetY(8.5);
    }

    public function totales()
    {

        // Declara variables a usar.
        $id_costo="";
        $id_costo = $this->ordenCompra->id_costo;
        if(empty($id_costo)){
            $id_costo='';
        }
        $total = $this->ordenCompra->monto;
        $moneda = Moneda::where('id_moneda', '=', $this->ordenCompra->id_moneda)->first()->nombre;
        $cambio = Cambio::where('id_moneda', '=', $this->ordenCompra->id_moneda)->where('fecha', '=',$this->ordenCompra->fecha)->first();
        if(is_null($cambio)){
            $cambio = Cambio::where('id_moneda','=',  $this->ordenCompra->id_moneda)->orderByDesc('fecha')->first();
        }
        $tipo_cambio = $this->ordenCompra->id_moneda != 1 ? $cambio->cambio : 1;
        $total_pesos = ($total * $tipo_cambio);
        $anticipo_monto = $this->ordenCompra->anticipo_monto;
        $iva = $this->ordenCompra->impuesto;
        $subtotal = $total - $iva;
        $anticipo_pactado_monetario = $total * $this->ordenCompra->porcentaje_anticipo_pactado / 100;

        $descuento= 0;
        $subtotal_antes_descuento = (100 * $subtotal) / (100 - (float) $descuento);
        $descuento_monetario = $subtotal_antes_descuento - $subtotal;

        $this->encola="";
        $this->y_subtotal = $this->GetY();
        $this->setY($this->y_subtotal+0.3);

        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(4, .5, 'Anticipo ('. $this->ordenCompra->cotizacion->complemento->anticipo .' %): ', 0, 0,'L');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(2, .5, number_format($anticipo_monto, 2, '.', ','), 1, 0,'R');

        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(11.5, .5, 'Subtotal Antes Descuento:', 0, 0,'R');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(2, .5, number_format($subtotal_antes_descuento, 2, '.', ','), 1, 1,'R');

        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(4, .5, 'Fecha de entrega: ', 0, 0,'L');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(2, .5,$this->ordenCompra->complemento->fecha_entrega_format, 1, 0,'R');

        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(11.5, .5, 'Descuento Global ('. $descuento .'%):', 0, 0,'R');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(2, .5, number_format($descuento_monetario, 2, '.', ','), 1, 1,'R');

        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(4, .5, 'Forma de Pago:', 0, 0,'L');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(5, .5, utf8_decode(($this->ordenCompra->complemento->formaPago)?$this->ordenCompra->complemento->formaPago->descripcion:""), 1, 0,'L');

        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(8.5, .5, 'Subtotal:', 0, 0,'R');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(2, .5, number_format($subtotal, 2, '.', ','), 1, 1,'R');
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(17.5, .5, 'IVA('.$this->ordenCompra->tasa_iva_format.'%):', 0, 0,'R');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(2, .5, number_format($iva, 2, '.', ','), 1, 1,'R');
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(17.5, .5, 'Total:', 0, 0,'R');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(2, .5, number_format($total, 2, '.', ','), 1, 1,'R');
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(17.5, .5, 'Moneda:', 0, 0,'R');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(2, .5, $moneda, 1, 1,'R');
        $this->Ln(.5);

        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(4, .5, 'Domicilio Entrega:', 0, 0,'L');

        $this->SetFont('Arial', '', 9);

        $this->MultiCell(15.5, .5, utf8_decode($this->domicilio), 1, 'J');
        $this->Ln(.2);

        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 7);

        $this->CellFitScale(4, .5, utf8_decode('Plazos de entrega / ejecución:'), 0, 0,'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(15.5, .5, utf8_decode($this->plazo), 1, 'J');
        $this->Ln(.2);
        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);

        $this->CellFitScale(4, .5, 'Otras Condiciones:', 0, 0,'L');
        $this->SetFont('Arial', '', 9);

        $this->MultiCell(15.5, .5, utf8_decode($this->condiciones), 1, 'J');

        if (in_array(Context::getDatabase(), ["SAO1814_PISTA_AEROPUERTO", "SAO1814_DEV_PISTA_AEROPUERTO"]))
        {
            $tipo_gasto = 'NO REGISTRADO';

            $this->Ln(.2);
            $this->SetTextColor(0,0,0);
            $this->SetFont('Arial', 'B', 9);
            $this->CellFitScale(4, .5, 'Tipo de Gasto:', 0, 0,'L');
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(15.5, .5, utf8_decode($tipo_gasto), 1, 'J');
        }

        $this->Ln(.7);
        $this->SetWidths([19.5]);
        $this->SetRounds(['12']);
        $this->SetRadius([0.2]);
        $this->SetFills(['180,180,180']);
        $this->SetTextColors(['0,0,0']);
        $this->SetHeights([0.5]);
        $this->SetFont('Arial', '', 9);
        $this->SetAligns(['C']);
        $this->encola="observaciones_encabezado";
        $this->Row(["Observaciones"]);
        $this->SetRounds(['34']);
        $this->SetRadius([0.2]);
        $this->SetAligns(['J']);
        $this->SetStyles(['DF']);
        $this->SetFills(['255,255,255']);
        $this->SetTextColors(['0,0,0']);
        $this->SetHeights([0.5]);
        $this->SetFont('Arial', '', 9);
        $this->SetWidths([19.5]);
        $this->encola="observaciones";

        $this->Row([utf8_decode($this->ordenCompra->observaciones)]);

    }



    public function partidas()
    {
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180,180,180);
        $this->SetWidths([0.5,1.5,1.5,2.5,6.5,2,1,2,2]);
        $this->SetStyles(['DF','DF','DF','DF','DF','FD','FD','DF']);
        $this->SetRounds(['1','','','','','','','','2']);
        $this->SetRadius([0.2,0,0,0,0,0,0,0,0.2]);
        $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
        $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C','C','C','C','C','C','C','C','C']);
        $this->Row(["#","Cantidad", "Unidad", "No. Parte", utf8_decode("Descripción"), "Precio", "% Descto.","Precio Neto", "Importe"]);

        $this->item_ante=Item::where('id_transaccion','=',$this->id_antecedente)->get();

        $count_partidas = count($this->ordenCompra->partidas);

        $ac = 0;

        foreach ($this->ordenCompra->partidas as $i=> $p)
        {
            $this->destino_item=Entrega::where('id_item','=',$this->item_ante[$i]->id_item)->get();

            if(!empty($this->destino_item[0]->id_concepto)){
                $item_arr= Concepto::where('id_concepto','=',$this->destino_item[0]->id_concepto)->where('id_obra','=',$this->ordenCompra->obra->id_obra)->get();

                $this->obs_item=$item_arr[0]->descripcion;
            }else{
                if(!empty($this->destino_item[0]->id_almacen)){
                    $item_arr= Almacen::where('id_almacen','=',$this->destino_item[0]->id_almacen)->where('id_obra','=',$this->ordenCompra->obra->id_obra)->get();
                    $this->obs_item=$item_arr[0]->descripcion;
                }else{
                    $this->obs_item='';
                }
            }


            $this->material=Material::where('id_material','=',$p->id_material)->get();

            $ac++;
            $this->SetWidths([0.5,1.5,1.5,2.5,6.5,2,1,2,2]);
            $this->encola="partida";
            $this->SetRounds(['','','','','','','','','']);
            $this->SetFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
            $this->SetAligns(['C','R','C','L','L','R','R','R','R']);
            $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);

            $this->complemento=OrdenCompraPartidaComplemento::where("id_item","=",$this->material[0]->id_item)->get();

            if(!in_array(Context::getDatabase(), ['SAO1814_TERMINAL_NAICM']))
                if($count_partidas == ($i+1) && empty($p->orden_partida_complemento->observaciones));
            {
                $this->SetRounds(['','','','','','','','','']);
                $this->SetRadius([0,0,0,0,0,0,0,0,0]);
            }

            $precio_unitario = ($p->descuento>0) ? $p->precio_material : $p->precio_unitario;
            $precio_neto = $p->precio_unitario;

            $this->Row([$ac,
                number_format($p->cantidad,2, '.', ','),
                $this->material[0]->unidad,
                $this->material[0]->numero_parte,
                utf8_decode($this->material[0]->descripcion),
                number_format($precio_unitario,2, '.', ','),
                (($p->descuento>0) ? number_format($p->descuento,2, '.', ',')."%" : '-'),
                number_format($precio_neto,2, '.', ','),
                number_format($precio_neto * $p->cantidad,2, '.', ',')
            ]);



            if($count_partidas == ($i+1) && (!is_null($p->itemSolicitudCompra->complemento) && $p->itemSolicitudCompra->complemento->observaciones ==''))
            {
                $this->SetRounds(['4','','','','','','','','3']);
                $this->SetRadius([0.2,0,0,0,0,0,0,0,0]);
            }

            // Centro de costo
            $this->encola="centro_costo";

            $this->SetWidths([19.5]);
            $this->SetAligns(['L']);
            $this->Row([utf8_decode($p->itemSolicitudCompra->entrega->destino_txt)]);

            if(!empty($p->itemSolicitudCompra->complemento->observaciones))
            {
                $this->SetTextColors(['150,150,150']);
                $this->SetWidths([19.5]);
                $this->SetAligns(['J']);
                if($count_partidas == ($i+1))
                {
                    $this->SetRounds(['4']);
                    $this->SetRadius([0.2]);
                }

                $this->encola="observaciones_partida";
                $this->Row([utf8_decode($p->itemSolicitudCompra->complemento->observaciones)]);
            }
        }
        $this->encola="";
        $this->y_subtotal = $this->GetY();
        /*if($this->dim>19.8) {
            $this->AddPage();
            $this->dim_aux=1;
        }*/
    }

    public function Footer()
    {
        if (!App::environment('production')) {
            $this->SetFont('Arial','B',80);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(5,15,utf8_decode("MUESTRA"),45);
            $this->RotatedText(6,21,utf8_decode("SIN VALOR"),45);
            $this->SetTextColor(0,0,0);
        }
        $residuo = $this->PageNo() % 2;

        $this->SetTextColor(0,0,0);
        // Firmas.
        if ($residuo > 0) {
            if (Context::getDatabase() == "SAO1814" && $this->ordenCompra->obra->id_obra == 41)
            {
                $this->SetY(-4.5);
                $this->SetFont('Arial', '', 6);
                $this->SetFillColor(180, 180, 180);
                $this->Cell(3.92, .4, utf8_decode(''), 0, 0, 'C');
                $this->Cell(3.92, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
                $this->Cell(7.84, .4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
                $this->Ln();
                $this->Cell(3.92, .4, utf8_decode('Proveedor'), 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'Jefe Compras', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'Gerente Administrativo', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, utf8_decode('Control de Costos'), 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'Director de proyecto', 'TRLB', 0, 'C', 1);
                $this->Ln();

                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Ln();

                $this->Cell(3.92, .4, '', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'LIC. BRENDA ELIZABETH ESQUIVEL ESPINOZA', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'C.P. ROGELIO HERNANDEZ BELTRAN', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'ING. JUAN CARLOS MARTINEZ ANTUNA', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'ING. PEDRO ALFONSO MIRANDA REYES', 'TRLB', 0, 'C', 1);

            }
            else if (Context::getDatabase() == "SAO1814_SPM_MOBILIARIO" && $this->ordenCompra->obra->id_obra == 1) {
                $this->SetY(-3.5);
                $this->SetFont('Arial', '', 6);
                $this->SetFillColor(180, 180, 180);
                $this->CellFitScale(6.53, .4, utf8_decode('Proveedor'), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(6.53, .4, utf8_decode('Gerente de Procuración'), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(6.53, .4, utf8_decode('Facturar a:'), 'TRLB', 0, 'C', 1);
                $this->Ln();
                $this->CellFitScale(6.53, .4, utf8_decode($this->ordenCompra->empresa->razon_social), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(6.53, .4, '', 'TRLB', 0, 'C', 1);
                $this->CellFitScale(6.53, .4, utf8_decode($this->ordenCompra->obra->facturar), 'TRLB', 0, 'C', 1);
                $this->Ln();

                $this->CellFitScale(6.53, 1.2, '', 'TRLB', 0, 'C');
                $this->CellFitScale(6.53, 1.2, '', 'TRLB', 0, 'C');
                $this->CellFitScale(6.53, 1.2, '', 'TRLB', 0, 'C');
                $this->Ln();

                $this->CellFitScale(6.53, .4, '', 'TRLB', 0, 'C', 1);

                // Harcodeo intensifies!!!
                if ($this->ordenCompra->id_transaccion >= 42544)
                    $this->CellFitScale(6.53, .4, utf8_decode('SANDRA MOSQUEDA ALVARADO'), 'TRLB', 0, 'C', 1);

                else
                    $this->CellFitScale(6.53, .4, utf8_decode('LIC. KARLA HAYDE LÓPEZ-NIETO FÉLIX-DÍAZ'), 'TRLB', 0, 'C', 1);


                $this->CellFitScale(6.53, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'TRLB', 0, 'C', 1);


            }
            else if (Context::getDatabase() == "SAO1814_MUSEO_BARROCO") {
                $this->SetY(-3.5);
                $this->SetFont('Arial', '', 6);
                $this->SetFillColor(180, 180, 180);

                $this->CellFitScale(4.89, .4, utf8_decode('Proveedor'), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(4.89, .4, utf8_decode('Gerente de Procuración'), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(9.78, .4, utf8_decode('Facturar a:'), 'TRLB', 0, 'C', 1);
                $this->Ln();
                $this->CellFitScale(4.89, .4, utf8_decode($this->ordenCompra->empresa->razon_social), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(4.89, .4, '', 'TRLB', 0, 'C', 1);
                $this->CellFitScale(9.78, .4, utf8_decode($this->ordenCompra->obra->facturar), 'TRLB', 0, 'C', 1);
                $this->Ln();
                $this->CellFitScale(4.89, 1.2, '', 'TRLB', 0, 'C');
                $this->CellFitScale(4.89, 1.2, '', 'TRLB', 0, 'C');
                $this->CellFitScale(4.89, 1.2, '', 'TRLB', 0, 'C');
                $this->CellFitScale(4.89, 1.2, '', 'TRLB', 0, 'C');
                $this->Ln();
                $this->CellFitScale(4.89, .4, '', 'TRLB', 0, 'C', 1);

                if ($this->con_fianza == 0)
                    $this->CellFitScale(4.89, .4, utf8_decode(''), 'TRLB', 0, 'C', 1);

                else
                    $this->CellFitScale(4.89, .4, utf8_decode(''), 'TRLB', 0, 'C', 1);

                $this->CellFitScale(4.89, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(4.89, .4, utf8_decode('LIC. FERNANDO GONZÁLEZ ORTÍZ'), 'TRLB', 0, 'C', 1);

            }
            else if (Context::getDatabase() == "SAO1814_TERMINAL_NAICM")
            {
                $this->SetY(-3.5);
                $this->SetFont('Arial', 'B', 5);
                $this->SetY(-2.7);
                $this->Cell(2);
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0, 'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0, 'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0, 'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0, 'C');
                $this->Ln(.4);
                $this->Cell(2);
                $this->CellFitScale(4, .8, '', 1, 0, 'C');
                $this->CellFitScale(4, .8, '', 1, 0, 'C');
                $this->CellFitScale(4, .8, '', 1, 0, 'C');
                $this->CellFitScale(4, .8, '', 1, 0, 'C');
                $this->Ln(.8);
                $this->Cell(2);
                $this->CellFitScale(4, .3, utf8_decode('Gerente / Director de Área Solicitante'), 1, 0, 'C');
                $this->CellFitScale(4, .3, utf8_decode('Jefe de Compras'), 1, 0, 'C');
                $this->CellFitScale(4, .3, utf8_decode('Gerente / Director de Procuración'), 1, 0, 'C');
                $this->CellFitScale(4, .3, ('Director General'), 1, 0, 'C');

            }
            else if (Context::getDatabase() == "SAO1814_TUNEL_DRENAJE_PRO")
            {
                $this->SetY(-2.7);
                $this->Cell(4.5);
                $this->SetFont('Arial', '', 6);
                $this->CellFitScale(5, .5, utf8_decode('Jefe de Compras'), 1, 0, 'C');
                $this->CellFitScale(5, .5, utf8_decode('Gerente Administrativo'), 1, 0, 'C');
                $this->CellFitScale(5, .5, utf8_decode('Gerente de Proyecto'), 1, 0, 'C');
                $this->Ln(.5);
                $this->Cell(4.5);
                $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');
                $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');
                $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');

            }
            else if (Context::getDatabase() == "SAO1814_TUNEL_MANZANILLO" && Context::getIdObra() == 3 && $this->ordenCompra->solicitud->id_area_compradora != 4)
            {
                $this->SetY(-3.5);
                $this->SetFont('Arial', '', 6);
                $this->SetFillColor(255, 255, 255);
                $this->Ln();
                $this->Cell(5, .4, utf8_decode('Jefe de compras'), 'TRLB', 0, 'C', 0);
                $this->Cell(5, .4, utf8_decode('VoBo Administración'), 'TRLB', 0, 'C', 0);
                $this->Cell(5, .4, utf8_decode('VoBo Gerente de Construcción'), 'TRLB', 0, 'C', 0);
                $this->Cell(5, .4, utf8_decode('Aprobó  Director de Proyectos'), 'TRLB', 0, 'C', 0);
                $this->Ln();

                $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
                $this->Ln();
                //$this->SetFillColor(180, 180, 180);
                $this->Cell(5, .4, utf8_decode('LIC. HECTOR FERNANDEZ ROMERO'), 'TRLB', 0, 'C', 1);
                $this->Cell(5, .4, utf8_decode('C.P. MARCO A. MALDONADO HERNANDEZ'), 'TRLB', 0, 'C', 1);
                $this->Cell(5, .4, utf8_decode('ING. MIGUEL DE LA MANO URQUIZA'), 'TRLB', 0, 'C', 1);
                $this->Cell(5, .4, utf8_decode('ING. HORACIO POSADAS HUERTA'), 'TRLB', 0, 'C', 1);

            }
            else if (Context::getDatabase() == "SAO1814_TUNEL_MANZANILLO" && Context::getIdObra()== 3 && $this->ordenCompra->solicitud->id_area_compradora == 4)
            {
                $this->SetY(-3.5);
                $this->SetFont('Arial', '', 6);
                $this->SetFillColor(255, 255, 255);
                $this->Cell(3.92, .25, utf8_decode(''), 'TRL', 0, 'C', 1);
                $this->Cell(3.92, .25, '', 'TRL', 0, 'C', 1);
                $this->Cell(3.92, .25, 'VoBo', 'TRL', 0, 'C', 1);
                $this->Cell(3.92, .25, 'Representante Legal ', 'TRL', 0, 'C', 1);
                $this->Cell(3.92, .25,utf8_decode('Representante Legal '),'TRL', 0, 'C', 1);
                $this->Ln();

                $this->Cell(3.92, .4, utf8_decode('Jefe Compras'), 'RLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'VoBo', 'RLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'CALTIA CONCESIONES SA DE CV', 'RLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'CALTIA CONCESIONES SA DE CV', 'RLB', 0, 'C', 1);
                $this->CellFitScaleForce(3.92, .4,utf8_decode('LA PENINSULAR COMPAÑIA CONSTRUCTORA SA DE CV'),'RLB', 0, 'C', 1);
                $this->Ln();

                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Ln();
                $this->SetFont('Arial', '', 5);
                $this->Cell(3.92, .4, '', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, utf8_decode('DIR. PROYECTO'), 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, utf8_decode('LIC. MARIA LUCIA MARTINEZ BALADO'), 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, utf8_decode('ING. MANUEL TOBAR VIDA'), 'TRLB', 0, 'C', 1);
                $this->CellFitScaleForce(3.92, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNANDEZ'), 'TRLB', 0, 'C', 1);
            }
            else {

                if ($this->conFirmaDAF) {
                    $this->SetFont('Arial', '', 6);
                    $this->SetY(-3.7);
                    $this->Cell(19, .5, utf8_decode("Orden de Compra no válida sin la firma de la Dirección de Administración y Finanzas."), 0, 1, "L");
                    $this->Ln(0.3);
                    $y_idaf = $this->GetY();
                    $this->CellFitScale(4.9, .5, ('Proveedor'), 1, 0, 'C');
                    $this->CellFitScale(4.9, .5, ('Jefe Compras'), 1, 0, 'C');
                    $this->CellFitScale(4.9, .5, utf8_decode('Dirección de Administración y Finanzas'), 1, 0, 'C');
                    $this->Ln(.5);
                    $this->CellFitScale(4.9, 1.2, ' ', 1, 0, 'R');
                    $this->CellFitScale(4.9, 1.2, ' ', 1, 0, 'R');
                    $this->CellFitScale(4.9, 1.2, ' ', 1, 0, 'R');
                    $this->setY($y_idaf);
                    $this->Cell(14.7);
                    $this->CellFitScale(4.9, .5, utf8_decode('Aprobó'), 1, 0, 'C');
                    $this->Ln(.5);
                    $this->Cell(14.7);
                    $this->CellFitScale(4.9, 1.2, ' ', 1, 0, 'R');
                } else {
                    $this->SetFont('Arial', '', 6);
                    $this->SetY(-2.7);
                    $this->Cell(4.5);
                    $this->CellFitScale(5, .5, utf8_decode('Proveedor'), 1, 0, 'C');
                    $this->CellFitScale(5, .5, utf8_decode('Jefe Compras'), 1, 0, 'C');
                    $this->CellFitScale(5, .5, utf8_decode('Aprobó'), 1, 0, 'C');
                    $this->Ln(.5);
                    $this->Cell(4.5);
                    $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');
                    $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');
                    $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');
                }

            }
        }
        $this->SetY(-0.8);
        $this->SetFont('Arial', 'B', 8);

        if ($residuo > 0)
            $this->Cell(10, .3, utf8_decode('Términos y condiciones adicionales al reverso.'), 0, 1, 'L');
        else
            $this->Cell(10, .3, (''), 0, 1, 'L');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(10, .3, utf8_decode('Formato generado desde el sistema de compras del SAO ERP. Fecha y hora de registro: ') . $this->ordenCompra->fecha_hora_registro_format, 0, 0, 'L');
        $this->Cell(9.5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }


    public function agregaPagina()
    {
        $this->AddPageSH($this->CurOrientation, $this->CurPageSize, $this->CurRotation);
        $this->useTemplate($this->sin_texto, 0, -0.5, 22);
        $this->AddPage($this->CurOrientation,  $this->CurPageSize, $this->CurRotation);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->agregaPagina();
    }
    function Close()
    {
        //Terminate document
        if($this->state==3)
            return;
        if($this->page==0)
            $this->agregaPagina();
        //Page footer
        $this->InFooter=true;
        $this->Footer();
        $this->InFooter=false;
        //Close page
        $this->_endpage();
        //Close document
        $this->_enddoc();
    }

    function AddPageSH($orientation='',$size='', $rotation=0)
    {
        if($this->state==3)
            $this->Error('The document is closed');
        $family = $this->FontFamily;
        $style = $this->FontStyle.($this->underline ? 'U' : '');
        $fontsize = $this->FontSizePt;
        $lw = $this->LineWidth;
        $dc = $this->DrawColor;
        $fc = $this->FillColor;
        $tc = $this->TextColor;
        $cf = $this->ColorFlag;
        if($this->page>0)
        {
            // Page footer
            $this->InFooter = true;
            $this->Footer();
            $this->InFooter = false;
            // Close page
            $this->_endpage();
        }
        // Start new page
        $this->_beginpage($orientation,$size,$rotation);
        // Set line cap style to square
        $this->_out('2 J');
        // Set line width
        $this->LineWidth = $lw;
        $this->_out(sprintf('%.2F w',$lw*$this->k));
        // Set font
        if($family)
            $this->SetFont($family,$style,$fontsize);
        // Set colors
        $this->DrawColor = $dc;
        if($dc!='0 G')
            $this->_out($dc);
        $this->FillColor = $fc;
        if($fc!='0 g')
            $this->_out($fc);
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
        // Page header
        $this->InHeader = true;
        //$this->Header();
        $this->InHeader = false;
        // Restore line width
        if($this->LineWidth!=$lw)
        {
            $this->LineWidth = $lw;
            $this->_out(sprintf('%.2F w',$lw*$this->k));
        }
        // Restore font
        if($family)
            $this->SetFont($family,$style,$fontsize);
        // Restore colors
        if($this->DrawColor!=$dc)
        {
            $this->DrawColor = $dc;
            $this->_out($dc);
        }
        if($this->FillColor!=$fc)
        {
            $this->FillColor = $fc;
            $this->_out($fc);
        }
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
    }

    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        // Output a cell
        $k = $this->k;
        if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
        {
            // Automatic page break
            $x = $this->x;
            $ws = $this->ws;
            if($ws>0)
            {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            $this->agregaPagina();
            //$this->AddPage($this->CurOrientation,$this->CurPageSize,$this->CurRotation);
            $this->x = $x;
            if($ws>0)
            {
                $this->ws = $ws;
                $this->_out(sprintf('%.3F Tw',$ws*$k));
            }
        }
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $s = '';
        if($fill || $border==1)
        {
            if($fill)
                $op = ($border==1) ? 'B' : 'f';
            else
                $op = 'S';
            $s = sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
        }
        if(is_string($border))
        {
            $x = $this->x;
            $y = $this->y;
            if(strpos($border,'L')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'T')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
            if(strpos($border,'R')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'B')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        }
        if($txt!=='')
        {
            if(!isset($this->CurrentFont))
                $this->Error('No font has been set');
            if($align=='R')
                $dx = $w-$this->cMargin-$this->GetStringWidth($txt);
            elseif($align=='C')
                $dx = ($w-$this->GetStringWidth($txt))/2;
            else
                $dx = $this->cMargin;
            if($this->ColorFlag)
                $s .= 'q '.$this->TextColor.' ';
            $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$this->_escape($txt));
            if($this->underline)
                $s .= ' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
            if($this->ColorFlag)
                $s .= ' Q';
            if($link)
                $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
        }
        if($s)
            $this->_out($s);
        $this->lasth = $h;
        if($ln>0)
        {
            // Go to next line
            $this->y += $h;
            if($ln==1)
                $this->x = $this->lMargin;
        }
        else
            $this->x += $w;
    }

    function Cell1($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
    {
        if(is_numeric($txt)){
            if(!($txt>0) && !($txt<0)){
                $txt = "-";
            }
        }

        //Output a cell
        $k=$this->k;
        if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
        {
            //Automatic page break
            $x=$this->x;
            $ws=$this->ws;
            if($ws>0)
            {
                $this->ws=0;
                $this->_out('0 Tw');
            }
            $this->agregaPagina();
            /*$this->AddPage($this->CurOrientation);*/
            $this->x=$x;
            if($ws>0)
            {
                $this->ws=$ws;
                $this->_out(sprintf('%.3f Tw',$ws*$k));
            }
        }
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $s='';
        if($fill==1 || $border==1)
        {
            if($fill==1)
                $op=($border==1) ? 'B' : 'f';
            else
                $op='S';
            $s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
        }
        if(is_string($border))
        {
            $x=$this->x;
            $y=$this->y;
            if(strpos($border,'L')!==false)
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'T')!==false)
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
            if(strpos($border,'R')!==false)
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'B')!==false)
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        }
        if($txt!=='')
        {
            if($align=='R')
                $dx=$w-$this->cMargin-$this->GetStringWidth($txt);
            elseif($align=='C')
                $dx=($w-$this->GetStringWidth($txt))/2;
            else
                $dx=$this->cMargin;
            if($this->ColorFlag)
                $s.='q '.$this->TextColor.' ';
            $txt2=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
            $s.=sprintf('BT %.2f %.2f Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt2);
            if($this->underline)
                $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
            if($this->ColorFlag)
                $s.=' Q';
            if($link)
                $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
        }
        if($s)
            $this->_out($s);
        $this->lasth=$h;
        if($ln>0)
        {
            //Go to next line
            $this->y+=$h;
            if($ln==1)
                $this->x=$this->lMargin;
        }
        else
            $this->x+=$w;
    }


    function create()
    {
        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 3.75);
        // Partidas.
        $this->partidas();
        $this->totales();

        $this->AddPage();
        $this->useTemplate($this->clausulado,0, 0.5, 22);

        try {
            $this->Output('I', 'Formato - Orden de Compra.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }
}
