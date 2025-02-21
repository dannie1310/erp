<?php


namespace App\PDF\Compras;


use App\Facades\Context;
use App\Models\CADECO\Cambio;
use App\Models\CADECO\Compras\AsignacionProveedor;
use App\Models\CADECO\Compras\AsignacionProveedorPartida;
use App\Models\CADECO\CotizacionCompraPartida;
use App\Models\CADECO\Moneda;
use App\Models\CADECO\Obra;
use App\Utils\ValidacionSistema;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AsignacionFormato extends Rotation
{
    private $asignacion;
    private $obra;
    var $encola = '';
    private $cadena_qr = '';
    private $cadena = '';
    private $dato = '';
    private $qr_name = '';

    public function __construct(AsignacionProveedor $asignacion)
    {
        $this->asignacion = $asignacion;
       parent::__construct('L', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());

        $this->createQR();
    }

    private function createQR()
    {
        $verifica = new ValidacionSistema();
        $datos_qr2['titulo'] = "Formato TABLA DE ASIGNACIÓN DE PROVEEDORES_".date("d-m-Y")."_".$this->asignacion."_".($this->asignacion->solicitud->complemento ? $this->asignacion->solicitud->complemento->folio_compuesto : '')."_".$this->asignacion->solicitud->numero_folio_format;
        $datos_qr2["base"] = Context::getDatabase();
        $datos_qr2["obra"] = $this->obra->nombre;
        $datos_qr2["tabla"] = "Compras.asignacion_proveedores";
        $datos_qr2["campo_id"] = "id";
        $datos_qr2["id"] = $this->asignacion->id;
        $cadena_json_id = json_encode($datos_qr2);

        $firmada = $verifica->encripta($cadena_json_id);
        $this->cadena_qr = "http://".$_SERVER['SERVER_NAME'].":". $_SERVER['SERVER_PORT']."/api/compras/solicitud-compra/leerQR?data=" . urlencode($firmada);
        $this->cadena = $firmada;

        $this->dato = $verifica->encripta($cadena_json_id);

        $this->qr_name = 'qrcode_'. mt_rand() .'.png';
    }

    public function Header()
    {
        $this->Ln(.5);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 15); /* 20 */ /* 19,05 */

        $this->Cell(17.5, 2.1, utf8_decode('AUTORIZACIÓN DE ASIGNACIÓN DE PROVEEDORES'), 0, 'C', 0);
        $this->SetFont('Arial', 'B', 7);
        $this->SetX(20);

        $this->Cell(4, .5, 'FOLIO:', 'L T', 0, 'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(3, .5, $this->asignacion->folio_format, 'R T', 0, 'L');


        $this->Ln(.5);
        $this->Cell(19);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(4, .5, 'FECHA: ', 'L B', 0, 'L');
        $this->Cell(3, .5, $this->asignacion->fecha_format, 'R B', 0, 'L');
        $this->SetFont('Arial', '', 6);
        $this->Ln(.5);
        $this->Cell(19);
        $this->Cell(4, .5, 'SOLICITUD ORIGEN: ', 'L', 0, 'L');
        $this->Cell(3, .5, $this->asignacion->solicitud->complemento ? $this->asignacion->solicitud->complemento->folio_compuesto : '' , 'R', 0, 'L');

        if($this->asignacion->asignacion_parcial){
            $this->Ln(.25);
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(21, 0.5, '(PARCIAL)', '', 0, 'C');
            $this->SetFont('Arial', '', 6);
            $this->Ln(-.25);
        }

        $this->Ln(.5);
        $this->Cell(19);
        $this->Cell(4, .5, utf8_decode('FOLIO SAO SOLICITUD: '), 'LB', 0, 'L');
        $this->Cell(3, .5, $this->asignacion->solicitud->numero_folio_format, 'RB', 0, 'L');

        $this->Ln(.7);
        $this->y_para_descripcion = $this->GetY();
        $this->y_para_descripcion_arr = array();
        $this->y_para_obs_partidas = $this->GetY();
        $this->y_para_obs_partidas_arr = array();
        $this->y_fin_obs_par_sol = $this->GetY();
        $this->y_fin_obs_par_sol_arr = array();
        $this->y_fin_og = $this->GetY();
        $this->y_fin_og_arr = array();

    }

    public function partidas()
    {
        $no_cotizaciones = count($this->asignacion->solicitud->cotizaciones);
        $moneda_dolar = Cambio::where('id_moneda','=', 2)->where('fecha', '=', $this->asignacion->timestamp_registro)->first();
        if(is_null($moneda_dolar)){
            $moneda_dolar = Cambio::where('id_moneda','=', 2)->orderByDesc('fecha')->first()->cambio;
        }else{
            $moneda_dolar = $moneda_dolar->cambio;
        }
        $moneda_euro = Cambio::where('id_moneda','=', 3)->where('fecha', '=', $this->asignacion->timestamp_registro)->first();
        if(is_null($moneda_euro)){
            $moneda_euro = Cambio::where('id_moneda','=', 3)->orderByDesc('fecha')->first()->cambio;
        }else{
            $moneda_euro = $moneda_euro->cambio;
        }
        $moneda_libra = Cambio::where('id_moneda','=', 4)->where('fecha', '=', $this->asignacion->timestamp_registro)->first();
        if(!is_null($moneda_libra)){
            $moneda_libra = $moneda_libra->cambio;
        }

        $cotizaciones = $this->asignacion->solicitud->cotizaciones;

        $datos_partidas_globales = [];

        $font = 5;
        $font2 = 4;
        $font_importes = 5;
        $heigth = 0.3;
        $cotizacinesXFila = 3;
        $bandera_asignacion = 0;
        $anchos["des"] = 4.75;
        $anchos["u"] = $anchos["ca"] = 0.5;
        $anchos["c"] = $anchos["ca"] = 0.92;
        $anchos["aesp"] = $anchos["u"] + $anchos["c"];
        $anchos["espacio_detalles_globales"] = ($anchos["aesp"] + $anchos["des"]) / 2;

        $anchos["pu"] = $anchos["fe"] = 1.1;
        $anchos["d"] = $anchos["ant"] = 0.4;
        $anchos["tc"] = $anchos["it"] = $anchos["ita"] = 0.9;
        $anchos["m"] = 0.4;
        $anchos["ic"] = 0.9;

        $anchos["dg"] = $anchos["pu"] + $anchos["d"];
        $anchos["op"] = $anchos["og"] = $anchos["p"] = $anchos["pu"] + $anchos["d"] + $anchos["it"] + $anchos["m"] + $anchos["ic"] + $anchos["ca"] + $anchos["ita"];
        $anchos["op"] = $anchos["og"] = $anchos["p"] = 6.6;
        $anchos["fe"] = $anchos["vig"] = $anchos["og"] / 6;
        $anchos["ant"] = $anchos["cre"] = $anchos["ent"] = $anchos["ivg"] = $anchos["og"] / 6;
        $anchos["ar"] = $anchos["m"] + $anchos["ic"];
        $no_arreglos = ceil($no_cotizaciones / $cotizacinesXFila);
        $i_e = 0;
        $total_mejor_opcion = 0;
        $partidas_comprobacion_mejor_opcion = array();

        for ($x = 0; $x < $no_arreglos; $x++) {
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            if (($no_cotizaciones - $i_e) > $cotizacinesXFila) {
                $inc_ie = $cotizacinesXFila;
            } else {
                $inc_ie = abs($no_cotizaciones - $i_e);
            }

            $this->SetDrawColor('200', '200', '200');
            $x_head = $this->GetX();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetX($x_head);
                $this->SetFillColor(0, 0, 0);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, utf8_decode($cotizaciones[$i]->empresa->razon_social), 1, 0, 'C', 1);
                $x_head = $this->GetX()+0.015;
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            $x_head = $this->GetX();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetX($x_head);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, "CONDICIONES GENERALES", 1, 0, 'C', 0);
                $x_head = $this->GetX()+0.015;
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            $asignados = array();
            $x_head = $this->GetX();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetX($x_head);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', '', $font);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["fe"] + $anchos["fe"], $heigth, utf8_decode("Fecha Cotización:"), 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["fe"], $heigth, $cotizaciones[$i]->fecha_format, 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["vig"], $heigth, "Vigencia:", 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["vig"], $heigth, $cotizaciones[$i]->complemento ? $cotizaciones[$i]->complemento->vigencia : '-', 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["vig"], $heigth, utf8_decode("Días"), 1, 0, 'C', 1);
                $x_head = $this->GetX()+0.015;
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            $x_head = $this->GetX();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetX($x_head);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', '', $font);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ant"], $heigth, "Anticipo:", 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["ant"], $heigth, $cotizaciones[$i]->complemento ? $cotizaciones[$i]->complemento->anticipo : '-', 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ant"], $heigth, "%", 1, 0, 'C', 1);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["cre"], $heigth, utf8_decode("Crédito:"), 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["cre"], $heigth, $cotizaciones[$i]->complemento ? $cotizaciones[$i]->complemento->dias_credito : '-', 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["cre"], $heigth, utf8_decode("Días"), 1, 0, 'C', 1);
                $x_head = $this->GetX()+0.015;
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            $x_head = $this->GetX();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetX($x_head);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', '', $font);
                $this->CellFitScale($anchos["ent"] * 2, $heigth, "", 1, 0, 'C', 1);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ent"] * 2, $heigth, "Plazo de Entrega:", 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["ent"], $heigth,  $cotizaciones[$i]->complemento ? $cotizaciones[$i]->complemento->plazo_entrega : '-', 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ent"], $heigth, utf8_decode("Días"), 1, 0, 'C', 1);
                $x_head = $this->GetX()+0.015;
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, "", 0, 0, 'C', 0);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            $x_head = $this->GetX();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetX($x_head);
                $this->SetTextColor(255, 255, 255);
                $this->SetFillColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"] * 4, $heigth, "Cotizado", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"] * 2, $heigth, "Asignado", 1, 0, 'C', 1);
                $x_head = $this->GetX()+0.015;
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);

            $x_head = $this->GetX();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetX($x_head);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"], $heigth, "Precio", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Importe Moneda", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
                $x_head = $this->GetX()+0.015;
            }
            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["des"], $heigth, utf8_decode("Descripción"), 1, 0, 'C', 1);
            $this->Cell($anchos["u"], $heigth, "U.M.", 1, 0, 'C', 1);
            $this->Cell($anchos["c"], $heigth, "Cantidad", 1, 0, 'C', 1);
            $x_head = $this->GetX();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetX($x_head);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"], $heigth, "Unitario", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Importe", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Moneda", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Comparable", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Cantidad", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Importe", 1, 0, 'C', 1);
                $x_head = $this->GetX()+0.015;
            }
            $this->Ln();
            $this->y_para_descripcion = $this->GetY();
            $this->y_para_descripcion_arr[] = $this->GetY();
            $partidas_solicitud = $this->asignacion->solicitud->partidas;
            $mejor_opcion_partida = $this->asignacion->mejores_opciones_encapsulado_por_material;
            foreach ($partidas_solicitud as $key => $partida_solicitud){

                asort($this->y_para_descripcion_arr);
                $this->y_para_descripcion = array_pop($this->y_para_descripcion_arr);
                $this->SetY($this->y_para_descripcion);
                $this->SetFillColor(255, 255, 255);
                $this->SetDrawColor('200', '200', '200');
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font2);
                $this->CellFitScale($anchos["des"], $heigth, utf8_decode($partida_solicitud->material->descripcion), 1, 0, 'L', 0, '');
                $this->SetFont('Arial', 'B', $font2-0.5);
                $this->CellFitScale($anchos["u"], $heigth, $partida_solicitud->unidad, 1, 0, 'L', 0, '');
                $this->SetFont('Arial', 'B', $font_importes);
                $this->CellFitScale($anchos["c"], $heigth,number_format($partida_solicitud->cantidad, '2', '.', ','), 1, 0, 'R', 0, '');
                $this->SetFont('Arial', 'B', $font2);
                $xca_ini = $this->getX();

                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $partida_cotizacion = CotizacionCompraPartida::where('id_transaccion', '=', $cotizaciones[$i]->id_transaccion)
                        ->where('id_material', '=', $partida_solicitud->id_material)
                        ->where('precio_unitario', '!=', 0)
                        ->first();

                    $this->SetDrawColor('200', '200', '200');
                    if($partida_cotizacion) {
                        if (array_key_exists($partida_solicitud->id_material, $mejor_opcion_partida) && $mejor_opcion_partida[$partida_solicitud->id_material] == $cotizaciones[$i]->id_transaccion) {
                            $this->SetFillColor(190, 190, 190);
                            $this->SetTextColor(0, 0, 0);
                            $this->SetDrawColor('255', '255', '255');
                        } else {
                            $this->SetFillColor(255, 255, 255);
                            $this->SetTextColor(0, 0, 0);
                        }
                        $this->SetX($xca_ini);
                        $x_prov = $this->GetX();
                        $this->SetFont('Arial', '', $font_importes);
                        $asignacion_partida = AsignacionProveedorPartida::where('id_transaccion_cotizacion', '=', $cotizaciones[$i]->id_transaccion)
                            ->where('id_material', '=', $partida_cotizacion->id_material)
                            ->where('id_asignacion_proveedores', '=', $this->asignacion->id)->first();

                        $this->Cell($anchos["pu"], $heigth, $partida_cotizacion ? number_format($partida_cotizacion->precio_compuesto, 2, '.', ',') : '', "T B L R", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, $partida_cotizacion ? number_format($partida_cotizacion->cantidad * $partida_cotizacion->precio_compuesto, 2, '.', ',') : '', "T B L R", 0, "R", 1);
                        $this->CellFitScale($anchos["pu"], $heigth, $partida_cotizacion ? $partida_cotizacion->moneda ? $partida_cotizacion->moneda->nombre : '' : '', "T B L R", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, number_format($partida_cotizacion->total_precio_moneda, 2, '.', ','), "B L R T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, $asignacion_partida ? number_format($asignacion_partida->cantidad_asignada,2, '.', ',')  : '-', "B L R T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, $asignacion_partida ? number_format($asignacion_partida->total_precio_moneda, 2, '.', ',') : '-', "B L R T", 0, "R", 1);
                        if($asignacion_partida) {
                            $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['porMoneda'][$partida_cotizacion->id_moneda][$key] = ($asignacion_partida->total_precio_moneda);
                            if(!array_key_exists('subtotal',$datos_partidas_globales[$cotizaciones[$i]->id_transaccion])) {
                                $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal'] = $this->asignacion->subtotalPorCotizacion($cotizaciones[$i]->id_transaccion);
                            }
                            $this->SetX($x_prov);
                            $this->SetDrawColor('20', '20', '20');
                            $this->Cell($anchos["pu"]*6, $heigth, '', "L R T", 0, "R", 0);
                        }
                    }else {
                        $this->SetX($xca_ini);
                        $this->SetFillColor(220, 220, 220);
                        $this->SetTextColor(220, 220, 220);
                        $this->SetFont('Arial', '', $font_importes);
                        $this->Cell($anchos["pu"]*6, $heigth, '', "L T R", 0, "R", 1);
                    }
                    $xca_ini = $this->getX() + 0.015;
                }
                $this->Ln();
                $this->SetTextColor(0, 0, 0);
                $this->SetDrawColor('200', '200', '200');
                $this->y_para_obs_partidas = $this->getY();
                $yos_ini = $this->getY();
                $xos_ini = $this->getX();
                $this->SetFont('Arial', 'B', $font2);
                $this->MultiCell($anchos["des"], $heigth,   '', 1, 'L', 0, 1);
                $this->y_para_descripcion_arr[] = $this->GetY();
                $this->y_fin_obs_par_sol_arr[] = $this->GetY() +0.015;
                $xos_ini += $anchos["des"];
                $this->setY($this->y_para_obs_partidas);
                $this->setX($xos_ini);
                $this->SetFont('Arial', 'B', $font2);
                $this->CellFitScale($anchos["aesp"], $heigth, 'Observaciones de Partida:', 'B', 0, 'R', 0);

                $yop_ini = $this->getY();
                $xop_ini = $this->getX();
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $partida_cotizacion = CotizacionCompraPartida::where('id_transaccion', '=', $cotizaciones[$i]->id_transaccion)
                        ->where('id_material', '=', $partida_solicitud->id_material)
                        ->where('precio_unitario', '!=', 0)
                        ->first();

                    $this->SetDrawColor('200', '200', '200');
                    if ($partida_cotizacion) {
                        if (array_key_exists($partida_solicitud->id_material, $mejor_opcion_partida) && $mejor_opcion_partida[$partida_solicitud->id_material] == $cotizaciones[$i]->id_transaccion) {
                            $this->SetFillColor(190, 190, 190);
                            $this->SetTextColor(0, 0, 0);
                            $this->SetDrawColor('255', '255', '255');
                        } else {
                            $this->SetFillColor(255, 255, 255);
                            $this->SetTextColor(0, 0, 0);
                        }

                        $this->SetFont('Arial', '', $font2);
                        $this->setY($yop_ini);
                        $this->setX($xop_ini);

                        $border = "T R L B";

                        $asignacion_partida = AsignacionProveedorPartida::where('id_transaccion_cotizacion', '=', $cotizaciones[$i]->id_transaccion)
                            ->where('id_material', '=', $partida_cotizacion->id_material)
                            ->where('id_asignacion_proveedores', '=', $this->asignacion->id)->first();

                        if($asignacion_partida) {
                            $this->SetDrawColor('20', '20', '20');
                            $border = "R L B";
                        }
                        $this->MultiCell($anchos["op"], $heigth, utf8_decode($partida_cotizacion->partida ? $partida_cotizacion->partida->observaciones : '-'), $border, "L", 1);

                        $this->y_para_descripcion_arr[] = $this->GetY()+0.015;
                        $xop_ini += $anchos["op"]+0.015;
                    } else {
                        $this->SetFillColor(220, 220, 220);
                        $this->SetTextColor(220, 220, 220);
                        $this->SetFont('Arial', '', $font2);
                        $this->setY($yop_ini);
                        $this->setX($xop_ini);
                        $this->MultiCell($anchos["op"], $heigth, '', "L B R", "L", 1);
                        $this->y_para_descripcion_arr[] = $this->GetY()+0.015;
                        $xop_ini += $anchos["op"]+0.015;
                    }
                }
                $this->Ln();
            }
            $asignacion_descuento = 0;
            $total_asignado = 0;
            asort($this->y_fin_obs_par_sol_arr);
            $this->y_fin_obs_par_sol = array_pop($this->y_fin_obs_par_sol_arr);
            $this->SetY($this->y_fin_obs_par_sol);
            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetDrawColor('200', '200', '200');
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Subtotal Antes Descuentos", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["pu"] * 2, $heigth, "", 1, 0, "", 1);
                $this->Cell($anchos["pu"], $heigth, 'PESO (MX)', 1, 0, 'R', 1);
                $this->Cell($anchos["pu"], $heigth, number_format($cotizaciones[$i]->suma_subtotal_partidas, 2, ".", ","), 1, 0, 'R', 1);
                if(array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales)){
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->Cell($anchos["pu"] * 2, $heigth, array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales) ? number_format($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal'], 3, ".", ",") : '-', 1, 0, 'R', 1);
            }

            $this->Ln();
            $this->Ln(0.1);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "% Descuento Global", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["pu"] * 2, $heigth, $cotizaciones[$i]->complemento ? $cotizaciones[$i]->complemento->descuento : '-', 1, 0, 'R', 1);
                $this->Cell($anchos["pu"], $heigth, "%", 1, 0, "C");
                $this->Cell($anchos["pu"], $heigth, $cotizaciones[$i]->descuento != 0 ? number_format($cotizaciones[$i]->descuento, 2, ".", ",") : '-', 1, 0, 'R', 1);
                if(array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales))
                {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                    $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['descuento'] = ($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal'] * $cotizaciones[$i]->complemento->descuento/100);
                }
                $this->Cell($anchos["pu"] * 2, $heigth, array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales) ? number_format($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['descuento'], 3, ".", ",") : '-', 1, 0, 'R', 1);
            }

            $this->Ln();
            $this->Ln(0.1);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Subtotal:", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetDrawColor(199, 199, 199);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["pu"] * 2, $heigth);
                $this->Cell($anchos["pu"], $heigth, 'PESO (MX)', 1, 0, 'R', 1);
                $this->Cell($anchos["pu"], $heigth, number_format($cotizaciones[$i]->subtotal_con_descuento, 2, ".", ","), 1, 0, 'R', 1);

                if(array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales)) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                    $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal_con_descuento'] = $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal'] - $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['descuento'];
                }
                $this->Cell($anchos["pu"] * 2, $heigth, array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales) ? number_format($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal_con_descuento'], 3, ".", ",") : '-', 1, 0, 'R', 1);
            }
            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetDrawColor(0, 0, 0);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "IVA:", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetDrawColor(199, 199, 199);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["pu"] * 2, $heigth,$cotizaciones[$i]->tasa_iva_format.' %', 1,0,'R',1);
                $this->Cell($anchos["pu"], $heigth, 'PESO (MX)', 1, 0, 'R', 1);
                $this->Cell($anchos["pu"], $heigth, number_format($cotizaciones[$i]->IVA_con_descuento, 2, ".", ","), 1, 0, 'R', 1);
                if(array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales)) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->Cell($anchos["pu"] * 2, $heigth,array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales) ? number_format(($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal_con_descuento'] * $cotizaciones[$i]->tasa_iva), 2, ".", ",") : '-', 1, 0, 'R', 1);
            }
            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetDrawColor(0, 0, 0);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Total:", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetDrawColor(199, 199, 199);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["pu"] * 2, $heigth);
                $this->Cell($anchos["pu"], $heigth, 'PESO (MX)', 1, 0, 'R', 1);
                if(array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales))
                {
                    $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['total']  = $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal_con_descuento'] + ($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal_con_descuento']  * $cotizaciones[$i]->tasa_iva);
                }
                $this->Cell($anchos["pu"], $heigth, number_format($cotizaciones[$i]->total_con_descuento, 2, ".", ","), 1, 0, 'R', 1);
                if(array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales)) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->Cell($anchos["pu"] * 2, $heigth, array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales) ? number_format($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['total'], 3, ".", ",") : '-', 1, 0, 'R', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetTextColor(255, 255, 255);
                $this->SetFillColor(100, 100, 100);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"] * 6, $heigth, "Observaciones Globales", 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            $y_ini = $this->getY();
            $x_ini = $this->getX();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->setY($y_ini);
                $this->setX($x_ini);
                $this->MultiCell($anchos["og"], $heigth, utf8_decode($cotizaciones[$i]->observaciones), 1, 'J', false);
                $this->y_fin_og_arr[] = $this->getY();
                $x_ini += $anchos["og"];
            }

            asort($this->y_fin_og_arr);
            $this->y_fin_og = array_pop($this->y_fin_og_arr);
            $this->SetY($this->y_fin_og);
            $this->Ln();
            $total_asignado_ic = array();
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell($anchos["p"]*3,.5, utf8_decode("RESUMEN DE ASIGNACIÓN POR MONEDA"), "B", 1, 'C', 0);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "", 0, 0, 'C', 0);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(0, 0, 0);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, utf8_decode($cotizaciones[$i]->empresa->razon_social), 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "TIPO DE CAMBIO", 1, 0, 'C', 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"] * 3, $heigth, "TOTAL EN PESOS(MX)", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Pagaderos en:", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"] * 2, $heigth, "", 1, 0, 'C', 1);
            }

            $this->Ln();
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(0, 0, 0);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "1", 1, 0, 'C', 0);
            $pesos = 0;
            $dolar = 0;
            $euro = 0;
            $libra = 0;
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                if(array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales)) {
                    if(array_key_exists('1',$datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['porMoneda'])) {
                        $pesos = array_sum($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['porMoneda']['1']);
                    }
                }

                $this->SetFont('Arial', 'B', $font);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 3, $heigth, array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales) && $pesos != 0 ? number_format($pesos, 3, ".", ",") : '-', 1, 0, 'R', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["pu"], $heigth, "PESOS (MX)", 1, 0, 'C', 1);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 2, $heigth, array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales) && $pesos != 0 ? number_format($pesos, 3, ".", ",") : '-', 1, 0, 'R', 0);
                $pesos = 0;
            }
            $mon_extranjeras = [2,3,4];
            foreach ($mon_extranjeras as $key => $moneda_e) {
                $tipo_cambio = Cambio::where('id_moneda','=',$moneda_e)->orderByDesc('fecha', 'desc')->first();
                $this->Ln();
                $this->Cell($anchos["espacio_detalles_globales"]);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(0, 0, 0);
                $this->Cell($anchos["espacio_detalles_globales"], $heigth, $moneda_e == 1 ? number_format($moneda_dolar, 4, ".", ",") : $moneda_e == 2 ? number_format($moneda_euro, 4, ".", ",") : $tipo_cambio ? number_format($tipo_cambio->cambio, 4, ".", ",") : '-', 1, 0, 'C', 0);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    if(array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales)) {
                        if(array_key_exists('2',$datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['porMoneda'])) {
                            $dolar = array_sum($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['porMoneda']['2']);
                        }
                        if(array_key_exists('3',$datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['porMoneda'])) {
                            $euro = array_sum($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['porMoneda']['3']);
                        }
                        if(array_key_exists('4',$datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['porMoneda']))
                        {
                            $libra = array_sum($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['porMoneda']['4']);
                        }
                    }
                    $this->SetFont('Arial', 'B', $font);
                    $this->SetTextColor(0, 0, 0);
                    if(array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales))
                    {
                        if($moneda_e == 2)
                        {
                            $this->CellFitScale($anchos["pu"] * 3, $heigth, $dolar != 0 ? number_format($dolar, 3, ".", ",") : '-', 1, 0, 'R', 0);
                            $this->SetTextColor(255, 255, 255);
                            $this->CellFitScale($anchos["pu"], $heigth, 'DOLAR(USD)', 1, 0, 'C', 1);
                            $this->SetTextColor(0, 0, 0);
                            $this->CellFitScale($anchos["pu"] * 2, $heigth, $dolar != 0 ? number_format($dolar / $moneda_dolar, 2, ".", ",") : '-', 1, 0, 'R', 0);
                        }
                        if($moneda_e == 3)
                        {
                            $this->CellFitScale($anchos["pu"] * 3, $heigth,  $euro != 0 ? number_format($euro, 2, ".", ",") : '-', 1, 0, 'R', 0);
                            $this->SetTextColor(255, 255, 255);
                            $this->CellFitScale($anchos["pu"], $heigth, 'EUROS', 1, 0, 'C', 1);
                            $this->SetTextColor(0, 0, 0);
                            $this->CellFitScale($anchos["pu"] * 2, $heigth, $euro != 0 ? number_format($euro /$moneda_euro, 2, ".", ",") : '-', 1, 0, 'R', 0);
                        }
                        if($moneda_e == 4)
                        {
                            $this->CellFitScale($anchos["pu"] * 3, $heigth, $libra != 0 ? number_format($libra, 2, ".", ",") : '-', 1, 0, 'R', 0);
                            $this->SetTextColor(255, 255, 255);
                            $this->CellFitScale($anchos["pu"], $heigth, 'LIBRAS', 1, 0, 'C', 1);
                            $this->SetTextColor(0, 0, 0);
                            $this->CellFitScale($anchos["pu"] * 2, $heigth, $libra != 0 ? number_format($libra / $tipo_cambio->cambio, 2, ".", ",") : '-', 1, 0, 'R', 0);
                        }
                    }else {
                        $this->SetTextColor(255, 255, 255);
                        $this->CellFitScale($anchos["pu"] * 3, $heigth, '-', 1, 0, 'R', 0);
                        if ($moneda_e == 2) {
                            $this->CellFitScale($anchos["pu"], $heigth, 'DOLAR(USD)', 1, 0, 'C', 1);
                        }
                        if ($moneda_e == 3) {
                            $this->CellFitScale($anchos["pu"], $heigth, 'EUROS', 1, 0, 'C', 1);
                        }
                        if ($moneda_e == 4) {
                            $this->CellFitScale($anchos["pu"], $heigth, 'LIBRAS', 1, 0, 'C', 1);
                        }
                        $this->SetTextColor(0, 0, 0);
                        $this->CellFitScale($anchos["pu"] * 2, $heigth, '-', 1, 0, 'R', 0);
                    }
                    $dolar = 0;
                    $euro = 0;
                    $libra = 0;
                }
            }

            $this->Ln();
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetDrawColor(0,0,0);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "TOTAL IVA INCLUIDO", 1, 0, 'R', 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetDrawColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 3, $heigth, array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales) ? number_format($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['total'], 3, ".", ",") : '-', 1, 0, 'R', 0);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"], $heigth, "PESOS (MX)", 1, 0, 'C', 0);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 2, $heigth, "", 0, 0, 'C', 0);
                $this->SetDrawColor(200, 200, 200);
            }
            $this->Ln();
            $this->Ln();
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->SetFillColor(0, 0, 0);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->SetX(16.5);
            $this->Cell($anchos["espacio_detalles_globales"]+0.19, $heigth, utf8_decode("Comprobación Mejor Opción"), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth, utf8_decode("Total Asignado"), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth, utf8_decode("Total Mejor Opción"), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth, utf8_decode("Diferencia"), 1, 0, 'R', 1);

            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', '', $font2);
            $this->SetX(16.5);
            $this->Cell(($anchos["espacio_detalles_globales"]/2)+0.19, $heigth, "SUBTOTAL", 1, 0, 'C', 1);
            $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(($anchos["espacio_detalles_globales"]/2), $heigth, "PESO MXN", 1, 0, 'C', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth,  number_format($this->asignacion->suma_total_con_descuento, 3, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth,  number_format($this->asignacion->mejor_asignado, 3, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth,  number_format($this->asignacion->diferencia, 3, ".", ","), 1, 0, 'R', 1);

            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', '', $font2);
            $this->SetX(16.5);
            $this->Cell(($anchos["espacio_detalles_globales"]/2)+0.19, $heigth, "IVA", 1, 0, 'C', 1);
            $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(($anchos["espacio_detalles_globales"]/2), $heigth, "PESO MXN", 1, 0, 'C', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth,  number_format($this->asignacion->suma_subtotal_partidas_iva, 3, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth,  number_format($this->asignacion->mejor_asignado_iva, 3, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth,  number_format($this->asignacion->diferencia_iva, 3, ".", ","), 1, 0, 'R', 1);
            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', '', $font2);
            $this->SetX(16.5);
            $this->Cell(($anchos["espacio_detalles_globales"]/2)+0.19, $heigth, "TOTAL", 1, 0, 'C', 1);
            $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(($anchos["espacio_detalles_globales"]/2), $heigth, "PESO MXN", 1, 0, 'C', 1);
            $this->Cell(($anchos["espacio_detalles_globales"]-0.8), $heigth, number_format($this->asignacion->suma_subtotal_partidas_total, 3, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth,  number_format($this->asignacion->mejor_asignado_total, 3, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth,  number_format($this->asignacion->diferencia_total, 3, ".", ","), 1, 0, 'R', 1);
            $this->Ln(2);
            $i_e+=$cotizacinesXFila;
        }
    }

    public function Footer()
    {
        if (!App::environment('production')) {
            $this->SetFont('Arial','B',90);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(5,15,utf8_decode("MUESTRA"),45);
            $this->RotatedText(10,20,utf8_decode("SIN VALOR"),45);
            $this->SetTextColor('0,0,0');
        }

        $this->firmas();

        $this->SetY(-3.8);
        $this->image("data:image/png;base64,".base64_encode(QrCode::format('png')->generate($this->cadena_qr)), $this->GetX(), $this->GetY(), 3.5, 3.5,'PNG');
        $this->SetY(-3.6);
        $this->setX(4.5);
        $this->SetFont('Arial', '', 4.5);
        $this->MultiCell(22.5, .3, utf8_decode($this->cadena), 0, 'L');
        $this->Ln(.2);
        $this->SetY(16.5);
        $this->setX(4.5);

        $this->SetFont('Arial', 'B', 6.5);
        $this->SetTextColor('100,100,100');
        $this->SetY(20.5);
        $this->Cell(26.5, .4, utf8_decode('Sistema de Administración de Obra'), 0, 0, 'R');
        $this->SetY(-0.9);
        $this->SetFont('Arial', 'BI', 6.5);
        $this->SetTextColor('0,0,0');
        $this->SetX(4.5);
        $this->Cell(11.5, .4, utf8_decode('Formato generado desde el sistema de compras. Fecha de registro: ') . $this->asignacion->fecha_format.' Fecha de consulta: '.date("d-m-Y H:i:s"), 0, 0, 'L');
        $this->Cell(15, .4, (utf8_decode('Página ')) . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    private function firmas()
    {
        $this->SetTextColor(0, 0, 0);
        $this->SetY(-5.9);
        $this->SetFont('Arial', '', 6);

        if (Context::getDatabase() == "SAO1814_TUNEL_MANZANILLO" && Context::getIdObra() == 3 && $this->asignacion->solicitud->id_area_compradora != 4)
        {
            $this->Cell(5.2, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, 'Control de Proyectos', 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, utf8_decode('Gerente de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, utf8_decode('VoBo Administración'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, utf8_decode('Aprobó '), 'TRLB', 0, 'C', 0);
            $this->Ln();
            $this->Cell(5.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5.2, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(5.2, .4, utf8_decode($this->asignacion->usuario), 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, utf8_decode('ING. ALEJANDRO PONCE RAMÍREZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, utf8_decode('ING. MIGUEL DE LA MANO URQUIZA'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, utf8_decode('C.P. MARCO A. MALDONADO HERNANDEZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, utf8_decode('ING. HORACIO POSADAS HUERTA'), 'TRLB', 0, 'C', 0);
        }
        else if (Context::getDatabase() == "SAO1814_TUNEL_MANZANILLO" && Context::getIdObra() == 3 && $this->asignacion->solicitud->id_area_compradora == 4)
        {
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.1, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(4, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.2, .4, utf8_decode('Dir. General de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(5, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.2, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(4.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(4.2, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'TRLB', 0, 'C', 0);
            $this->Cell(4.1, .4, utf8_decode('ING. JOSE MARTÍN ORTIZ VAZQUEZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(4, .4, utf8_decode('DIR. PROYECTO'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.2, .4, utf8_decode('ING. LUIS HORCASITAS MANJARREZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(5, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.2, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);

        }
        else if (Context::getDatabase() == "SAO1814_QUERETARO_SAN_LUIS" && (Context::getIdObra() == 2 || Context::getIdObra() == 5) && $this->asignacion->solicitud->id_area_compradora == 4){
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Dir. Ejec. de Oper. e Infra.'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.8, .4, utf8_decode('Autoriza Dir. Admon y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);
            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('LIC. ANTONIO MAGALLANES GARCÍA'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.8, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if (Context::getDatabase() == "SAO1814_MUSEO_BARROCO" && (Context::getIdObra() == 2 || Context::getIdObra() == 4) && $this->asignacion->solicitud->id_area_compradora == 4){
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Dir. Ejec. de Oper. e Infra.'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.8, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('LIC. ANTONIO MAGALLANES GARCÍA'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.8, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if (Context::getDatabase() == "SAO1814_CHIMALHUACAN" && Context::getIdObra() == 3 && $this->asignacion->solicitud->id_area_compradora == 4)
        {
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('VoBo'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.8, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode(''), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.8, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode(''), 'TRLB', 0, 'C', 0);
        }
        else if (((Context::getDatabase() == "SAO1814_CHIMALHUACAN" && (Context::getIdObra() == 5 || Context::getIdObra() == 6)) || (Context::getDatabase() == "SAO1814_CUTZAMALA" && Context::getIdObra() == 7)) && $this->asignacion->solicitud->id_area_compradora == 4)
        {
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Dir. General de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. DANIEL CANSANÇÃO OLIVEIRA'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if (Context::getDatabase() == "SAO1814_CUTZAMALA" && (Context::getIdObra() == 5 || Context::getIdObra() == 6) && $this->asignacion->solicitud->id_area_compradora == 4)
        {
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Dir. General de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.9, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRL', 0, 'C');

            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. LUIS HORCASITAS MANJARREZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if (Context::getDatabase() == "SAO1814" && Context::getIdObra() == 10 && $this->asignacion->solicitud->id_area_compradora == 4)
        {
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Dir. General de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.9, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRL', 0, 'C');

            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. LUIS HORCASITAS MANJARREZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if (Context::getDatabase() == "SAO1814_MEXICO_TOLUCA" && Context::getIdObra() == 5 && $this->asignacion->solicitud->id_area_compradora == 4)
        {

            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Dir. General de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.9, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRL', 0, 'C');

            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. LUIS HORCASITAS MANJARREZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if (Context::getDatabase() == "SAO1814_TROLEBUS" && Context::getIdObra() == 1 && $this->asignacion->solicitud->id_area_compradora == 4)
        {
            $this->Cell(2.4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(3.7, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(2.5, .4, utf8_decode('VoBo Admon. Proyecto'), 'TRLB', 0, 'C', 0);
            $this->Cell(4, .4, utf8_decode('VoBo Dir. Proyecto'), 'TRLB', 0, 'C', 0);
            $this->Cell(4, .4, utf8_decode('Dir. General de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.9, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.3, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(2.4, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(3.7, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(2.5, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(4, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(4.9, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(2.4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(3.7, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(2.5, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell( 4, .4, utf8_decode('ING. CARLOS CID'), 'TRLB', 0, 'C', 0);
            $this->Cell(4, .4, utf8_decode('ING. DANIEL CANSANÇÃO OLIVEIRA'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.9, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.3, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);

        }
        else if($this->asignacion->solicitud->id_area_compradora == 4 && (Context::getDatabase() == "SAO_CORP" && Context::getIdObra() == 1))
        {
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('VoBo'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if($this->asignacion->solicitud->id_area_compradora == 4 &&
            ((Context::getDatabase() == "SAO1814" && in_array(Context::getIdObra(), array(52,53,60,96,97))) || (Context::getDatabase() == "SAO_CORP" && Context::getIdObra() == 12))
        )
        {
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Dir. Ejec. de Oper. e Infra.'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('LIC. ANTONIO MAGALLANES GARCIA'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if($this->asignacion->solicitud->id_area_compradora == 4 && Context::getDatabase() == "SAO_CORP" && Context::getIdObra() == 8)
        {
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('VoBo'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode(''), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if(Context::getDatabase() == "SAO1814_SANEAMIENTO" && Context::getIdObra() == 10 && $this->asignacion->solicitud->id_area_compradora == 4){
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Dir. General de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('Autoriza Dir. Admon y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.1, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 1.1, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 1.1, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.1, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.9, 1.1, '', 'TRL', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.1, '', 'TRL', 0, 'C');

            $this->Ln();
            $this->Cell(4, 0.4, '', 'RLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.3, 0.4, '', 'RLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 0.4, '', 'RLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 0.4, utf8_decode('ING. DANIEL CANSANÇÃO OLIVEIRA'), 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.9, 0.4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if(Context::getDatabase() == "SAO1814_SANEAMIENTO" && Context::getIdObra() == 10 && $this->asignacion->solicitud->id_area_compradora != 4 ){
            $this->Cell(5, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.25);
            $this->Cell(5, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.25);
            $this->Cell(5, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.25);
            $this->Cell(5, .4, utf8_decode('VoBo Dir. General de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(.25);
            $this->Cell(5, .4, utf8_decode('Autoriza Dir. Ejec. Admon. y Fianzs '), 'TRLB', 0, 'C', 0);
            $this->Ln();
            $this->Cell(5, 1.1, '', 'TRL', 0, 'C');
            $this->Cell(.25);
            $this->Cell(5, 1.1, '', 'TRL', 0, 'C');
            $this->Cell(.25);
            $this->Cell(5, 1.1, '', 'TRL', 0, 'C');
            $this->Cell(.25);
            $this->Cell(5, 1.1, '', 'TRLB', 0, 'C');
            $this->Cell(.25);
            $this->Cell(5, 1.1, '', 'TRL', 0, 'C');
            $this->Ln();
            $this->Cell(5, 0.4, '', 'RLB', 0, 'C');
            $this->Cell(.25);
            $this->Cell(5, 0.4, '', 'RLB', 0, 'C');
            $this->Cell(.25);
            $this->Cell(5, 0.4, '', 'RLB', 0, 'C');
            $this->Cell(.25);
            $this->Cell(5, 0.4, 'ING. LUIS HORCASITAS MANJARREZ', 'TRLB', 0, 'C');
            $this->Cell(.25);
            $this->Cell(5, 0.4, '', 'RLB', 0, 'C');
        }
        else if(Context::getDatabase() == "SAO1814_CUTZAMALA" && Context::getIdObra() == 8 && $this->asignacion->solicitud->id_area_compradora == 4){
            $this->Cell(4, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('Dir. General de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.9, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.1);
            $this->Cell(4.2, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('ING. DANIEL CANSANÇÃO OLIVEIRA'), 'TRLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.9, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.1);
            $this->Cell(4.2, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if (Context::getDatabase() == "SAO1814_OPERADORA_ACTIVOS" && Context::getIdObra() == 16)
        {
            $this->Cell(5.2, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.8, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, utf8_decode('Autoriza Dir. Gral. De Hermes Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.6, .4, utf8_decode('Autoriza Dir. Admón. y Finanzas Const y Operac de Inf'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, utf8_decode('Autoriza Dir. Ejec. Admón y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Ln();
            $this->Cell(5.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5.6, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5.2, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(5.2, .4, utf8_decode($this->asignacion->usuario), 'TRLB', 0, 'C', 0);
            $this->Cell(4.8, .4, utf8_decode('ING. RAFAEL COLMENERO VEGA'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, utf8_decode('ING. LUIS HORCASITAS MANJARREZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.6, .4, utf8_decode('Lic. FRANCISCO MANUEL OSUNA Y PEREZ DE CELIS'), 'TRLB', 0, 'C', 0);
            $this->Cell(5.2, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'TRLB', 0, 'C', 0);
        }
        else if(Context::getDatabase() == "SAO1814_MUSEO_MUNET" && Context::getIdObra() == 1 && $this->asignacion->solicitud->id_area_compradora == 4){
            $this->Cell(4.1, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(3.8, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.1, .4, utf8_decode('Dir. General de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(3.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4.1, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(3.8, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.1, .4, utf8_decode('ING. DANIEL CANSANÇÃO OLIVEIRA'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if(Context::getDatabase() == "SAO1814_MUSEO_MUNET" && $this->asignacion->solicitud->id_area_compradora == 4){
            $this->Cell(4.1, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Dir. General de Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4.1, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('ING. LUIS HORCASITAS MANJARREZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('ING. GASPAR GUERREIRO GONZÁLEZ'), 'TRLB', 0, 'C', 0);
        }
        else if(Context::getDatabase() == "SAO1814_PTAR_SUR_QRO" && Context::getIdObra() == 1 && $this->asignacion->solicitud->id_area_compradora == 4){
            $this->Cell(4.1, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.1, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode('Dir. Gral. Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4.1, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.1, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode('ING. DANIEL CANSANÇÃO OLIVEIRA'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('ING GASPAR GUERREIRO  GONZALEZ'), 'TRLB', 0, 'C', 0);
        }
        else if(Context::getDatabase() == "SAO_CORP" && Context::getIdObra() == 5 && $this->asignacion->solicitud->id_area_compradora == 4)
        {
            $this->Cell(4.1, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.1, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.1, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode('VoBo'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4.1, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.1, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.1, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode(''), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('ING GASPAR GUERREIRO  GONZALEZ'), 'TRLB', 0, 'C', 0);
        }
        else if(Context::getDatabase() == "SAO1814_AUTODROMO" && Context::getIdObra() == 11 && $this->asignacion->solicitud->id_area_compradora == 4){
            $this->Cell(4.1, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode('Dir. General Construcción'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode('VoBo'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4.1, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode(''), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('ING. GASPAR GUERREIRO  GONZALEZ'), 'TRLB', 0, 'C', 0);
        }
        else if(Context::getDatabase() == "SAO1814" && Context::getIdObra() == 62 && $this->asignacion->solicitud->id_area_compradora == 4){
            $this->Cell(4.1, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.2, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode('VoBo'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.2, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4.1, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.2, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode(''), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode(''), 'TRLB', 0, 'C', 0);
        }
        else if($this->asignacion->solicitud->id_area_compradora == 4){
            $this->Cell(4.1, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.1, .4, utf8_decode('Validó Gerencia Responsable Compra'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode('Gerencia Solicitante'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode('VoBo'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('Autoriza Dir. Admon. y Finanzas'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode('Autoriza Dir. General'), 'TRLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.1, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(.2);
            $this->Cell(4.3, 1.2, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(4.1, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.1, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode(''), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4, .4, utf8_decode('ING. LUIS HORCASITAS MANJARREZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(5, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'RLB', 0, 'C', 0);
            $this->Cell(.2);
            $this->Cell(4.3, .4, utf8_decode(''), 'TRLB', 0, 'C', 0);
        }
        else {
            $encabezados[0] = utf8_decode("Elaboró");
            $encabezados[1] = utf8_decode("Validó Gerencia Responsable Compra");
            $encabezados[2] = "Gerencia Solicitante";
            $encabezados[3] = "Autoriza Dir. Ejec. Admon. y Finanzas";
            for ($i = 0; $i <= 3; $i++) {
                $this->Cell(6.2, .5, $encabezados[$i], 1, 0, 'C', 0, '');
                $this->Cell(.4);
            }
            $this->Ln(.5);
            for ($i = 0; $i <= 3; $i++) {
                $this->Cell(6.2, 1, '', 1, 0, 'R', 0, '');
                $this->Cell(.4);
            }
        }
    }

    function create()
    {
        $this->SetMargins(1, .5, 2);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 6);
        $this->partidas();

        try {
            $this->Output('I', 'Asignación de Proveedores.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }
}
