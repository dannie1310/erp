<?php


namespace App\PDF\Compras;


use App\Facades\Context;
use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\Obra;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\Transaccion;
use App\Utils\ValidacionSistema;
use Ghidev\Fpdf\Rotation;

class CotizacionTablaComparativaFormato extends Rotation
{
    protected $obra;
    protected $cotizacion;
    protected $solicitud_compra;
    private $encabezado_pdf = '';
    protected $extgstates = array();


    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct($id)
    {
        parent::__construct('L', 'cm', 'A4');
        $this->obra = Obra::find(Context::getIdObra());
        $this->id=$id;
        $this->encabezado_pdf = utf8_decode($this->obra->facturar);
        $this->solicitud_compra = SolicitudCompra::query()->find( $id);

        $verifica = new ValidacionSistema();
        $datos_qr[] = "TABLA COMPARATIVA DE COTIZACIONES";
        $datos_qr[] = date("d-m-Y");
//        $datos_qr[] = $this->solicitud_compra->complemento->folio_compuesto;
        $datos_qr[] = $this->solicitud_compra->numero_folio_format;
        $datos_qr2["tipo"] = "tabla_comparativa";
        $datos_qr2["base"] = Context::getDatabase();
        $datos_qr2["tabla"] = "transacciones";
        $datos_qr2["campo_id"] = "id_transaccion";
        $datos_qr2["id"] = $this->solicitud_compra->id_transaccion;
        $cadena_json_id = json_encode($datos_qr2);
        $cadena_encriptar = $cadena_json_id . ">";
        $cadena_encriptar .= implode("_",$datos_qr);
        $firmada = $verifica->encripta($cadena_encriptar);
        $this->cadena_qr = "http://portal-aplicaciones.grupohi.mx/sao/api/compras/solicitud-compra" . urlencode($firmada);
        $this->cadena = $firmada;

        $this->dato = $verifica->encripta($cadena_encriptar);

        $this->qr_name = 'qrcode_'. mt_rand() .'.png';

    }

    function Header() {
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 15.2);
        $this->Cell(23, 1.5, 'TABLA COMPARATIVA DE COTIZACIONES', 0, 0, 'C', 0);
        $this->SetFont('Arial', 'B', 7);
        $this->SetX(20.4);
        $this->Cell(4, .5, 'FECHA DE CONSULTA ', 'L T', 0, 'L');
        $this->Cell(3, .5, date("d-m-Y"), 'R T', 0, 'L');
        $this->Ln(.5);
        $this->Cell(19.7);
        $this->Cell(4, .5, 'SOLICITUD DE COMPRA ', 'L', 0, 'L');
        $this->Cell(3, .5, '$this->solicitud_compra->complemento->folio_compuesto', 'R', 0, 'L');

        $this->Ln(.5);
        $this->Cell(19.7);
        $this->Cell(4, .5, 'FOLIO SAO SOLICITUD ', 'LB', 0, 'L');
        $this->Cell(3, .5, $this->solicitud_compra->numero_folio_format, 'RB', 0, 'L');

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
        {
            $partidas = [];
            $cotizaciones = $this->solicitud_compra->cotizaciones;
            foreach ($this->solicitud_compra->items as $key => $item) {
                if (array_key_exists($item->id_material, $partidas)) {
                    $partidas[$item->id_material]->cantidad = $partidas[$item->id_material]['cantidad'] + $item->cantidad;
                } else {
                    $partidas[$item->id_material] = $item;
                }
            }

            $precios = [];
            foreach ($cotizaciones as $key => $cotizacion){
                foreach ($cotizacion->cotizaciones as $llave => $cot_partida){
                    $tipo_cambio = 1;
                    $cot_partida->id_moneda == 2?$tipo_cambio = $cotizacion->complemento->tc_usd:'';
                    $cot_partida->id_moneda == 3?$tipo_cambio = $cotizacion->complemento->tc_eur:'';
                    if(key_exists($cot_partida->id_material,$precios)){
                        ( ($cot_partida->precio_unitario - ($cot_partida->precio_unitario * $cot_partida->descuento /100)) * $tipo_cambio) > 0 && $precios[$cot_partida->id_material] > (  ($cot_partida->precio_unitario - ($cot_partida->precio_unitario * $cot_partida->descuento /100)) * $tipo_cambio)?
                            $precios[$cot_partida->id_material] =  ($cot_partida->precio_unitario - ($cot_partida->precio_unitario * $cot_partida->descuento /100)) * $tipo_cambio:'';
                    }else{
                        ($cot_partida->precio_unitario - ($cot_partida->precio_unitario * $cot_partida->descuento /100)) * $tipo_cambio >0 ?
                            $precios[$cot_partida->id_material] =  ($cot_partida->precio_unitario - ($cot_partida->precio_unitario * $cot_partida->descuento /100)) * $tipo_cambio:'';
                    }
                }

            }
//dd($precios);
            $monedas = [
                1 => [
                    'nombre' => 'PESO (MX)',
                    'nombre_corto' => 'MXP'
                ],
                2 => [
                    'nombre' => 'DOLAR (USD)',
                    'nombre_corto' => 'USD'
                ],
                3 => [
                    'nombre' => 'EURO (EUR)',
                    'nombre_corto' => 'EUR'
                ]
            ];

            $subtotal_moneda_conversion = [];

            $no_cotizaciones = count($cotizaciones);
            $font = 5;
            $font2 = 4;
            $heigth = 0.306;
            $cotizacinesXFila = 5;
            $anchos["des"] = 4.7;
            $anchos["u"] = $anchos["c"] = 0.77;
            $anchos["aesp"] = $anchos["u"] + $anchos["c"];
            $anchos["espacio_detalles_globales"] = ($anchos["aesp"] + $anchos["des"]) / 2;

            $anchos["pu"] = $anchos["fe"] = 1;
            $anchos["d"] = $anchos["ant"] = 0.5;
            $anchos["tc"] = $anchos["it"] = 1.12;
            $anchos["m"] = 0.5;
            $anchos["ic"] = 1.13;

            $anchos["dg"] = $anchos["pu"] + $anchos["d"];
            $anchos["op"] = $anchos["og"] = $anchos["p"] = $anchos["pu"] + $anchos["d"] + $anchos["it"] + $anchos["m"] + $anchos["ic"];
            $anchos["fe"] = $anchos["vig"] = $anchos["og"] / 4;
            $anchos["ant"] = $anchos["cre"] = $anchos["ent"] = $anchos["ivg"] = $anchos["og"] / 4;
            $anchos["ar"] = $anchos["m"] + $anchos["ic"];

            $no_arreglos = ceil($no_cotizaciones / $cotizacinesXFila);
            $i_e = 0;

            for ($x = 0; $x < $no_arreglos; $x++) {
                $this->SetDrawColor('200', '200', '200');
                $this->Cell($anchos["aesp"] + $anchos["des"]);
                if (($no_cotizaciones - $i_e) > $cotizacinesXFila) {
                    $inc_ie = $cotizacinesXFila;
                } else {
                    $inc_ie = abs($no_cotizaciones - $i_e);
                }

                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $ivg_partida = $this->calcular_ivg($precios, $cotizaciones[$i]->cotizaciones->toArray(), $cotizaciones[$i]->complemento->tc_eur, $cotizaciones[$i]->complemento->tc_usd);
                    if ($ivg_partida == 0) {
                        $this->SetFillColor(0, 0, 0);
                        $this->SetTextColor(255, 255, 255);
                    } else {
                        $this->SetFillColor(150, 150, 150);
                        $this->SetTextColor(255, 255, 255);
                    }

                    $this->SetFont('Arial', 'B', $font);
                    $this->CellFitScale($anchos["p"], $heigth, $cotizaciones[$i]->empresa->razon_social, 1, 0, 'C', 1);
                }
                $this->Ln();
                $this->Cell($anchos["aesp"] + $anchos["des"]);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $ivg_partida = $this->calcular_ivg($precios, $cotizaciones[$i]->cotizaciones->toArray(), $cotizaciones[$i]->complemento->tc_eur, $cotizaciones[$i]->complemento->tc_usd);
                    if ($ivg_partida == 0) {
                        $this->SetFillColor(0, 0, 0);
                        $this->SetTextColor(255, 255, 255);
                    } else {
                        $this->SetFillColor(150, 150, 150);
                        $this->SetTextColor(255, 255, 255);
                    }
                    $this->SetFont('Arial', 'B', $font);
                    $this->CellFitScale($anchos["fe"], $heigth, "Fecha:", 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["fe"], $heigth, $cotizaciones[$i]->fecha_format, 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["vig"], $heigth, "Vigencia:", 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["vig"], $heigth, $cotizaciones[$i]->complemento->vigencia, 1, 0, 'C', 1);
                }
                $this->Ln();
                $this->Cell($anchos["aesp"] + $anchos["des"]);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $ivg_partida = $this->calcular_ivg($precios, $cotizaciones[$i]->cotizaciones->toArray(), $cotizaciones[$i]->complemento->tc_eur, $cotizaciones[$i]->complemento->tc_usd);
                    if ($ivg_partida == 0) {
                        $this->SetFillColor(0, 0, 0);
                        $this->SetTextColor(255, 255, 255);
                    } else {
                        $this->SetFillColor(150, 150, 150);
                        $this->SetTextColor(255, 255, 255);
                    }
                    $this->SetFont('Arial', 'B', $font);
                    $this->CellFitScale($anchos["ant"], $heigth, "Anticipo", 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["cre"], $heigth, utf8_decode("Crédito"), 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["ent"], $heigth, "Plazo Ent.", 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["ivg"], $heigth, "IVG", 1, 0, 'C', 1);
                }
                $this->Ln();
                $this->Cell($anchos["aesp"] + $anchos["des"]);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $ivg_partida = $this->calcular_ivg($precios, $cotizaciones[$i]->cotizaciones->toArray(), $cotizaciones[$i]->complemento->tc_eur, $cotizaciones[$i]->complemento->tc_usd);
                    if ($ivg_partida == 0) {
                        $this->SetFillColor(0, 0, 0);
                        $this->SetTextColor(255, 255, 255);
                    } else {
                        $this->SetFillColor(150, 150, 150);
                        $this->SetTextColor(255, 255, 255);
                    }
                    $this->SetFont('Arial', 'B', $font);
                    $this->CellFitScale($anchos["ant"], $heigth, $cotizaciones[$i]->complemento->anticipo, 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["cre"], $heigth, $cotizaciones[$i]->complemento->dias_credito, 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["ent"], $heigth, $cotizaciones[$i]->complemento->plazo_entrega, 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["ivg"], $heigth, number_format($ivg_partida * 100, '2', '.', ',') . '%', 1, 0, 'C', 1);
                }
                $this->Ln();

                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["des"], $heigth, utf8_decode("Descripción"), 1, 0, 'C', 1);
                $this->Cell($anchos["u"], $heigth, "Unidad", 1, 0, 'C', 1);
                $this->Cell($anchos["c"], $heigth, "Cantidad", 1, 0, 'C', 1);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $this->SetFillColor(100, 100, 100);
                    $this->SetTextColor(255, 255, 255);
                    $this->SetFont('Arial', 'B', $font);
                    $this->CellFitScale($anchos["pu"], $heigth, "Precio U.", 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["d"], $heigth, "I V", 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["it"], $heigth, "Importe T.", 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["m"], $heigth, "M", 1, 0, 'C', 1);
                    $this->CellFitScale($anchos["ic"], $heigth, "Importe T. Conv.", 1, 0, 'C', 1);
                }
                $this->Ln();
                $this->y_para_descripcion = $this->GetY();
                $this->y_para_descripcion_arr[] = $this->GetY();
                foreach ($partidas as $key => $partida) {

                    $ki = -1;
                    asort($this->y_para_descripcion_arr);
                    $this->y_para_descripcion = array_pop($this->y_para_descripcion_arr);
                    $this->SetY($this->y_para_descripcion);
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetFont('Arial', 'B', $font2);
                    $this->CellFitScale($anchos["des"], $heigth, utf8_decode($partida->material->descripcion) . ' ', 1, 0, 'L', 0, '');
                    $this->Cell($anchos["c"], $heigth, $partida->unidad, 1, 0, 'L', 0, '');
                    $this->Cell($anchos["u"], $heigth, number_format($partida->cantidad, '2', '.', ','), 1, 0, 'L', 0, '');
                    for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                        $cot_llave = array_search($partida->id_material, array_column($cotizaciones[$i]->cotizaciones->toArray(), 'id_material'));
                        $imp_t_conver = 0;
                        $precio_unitario_compuesto = 0;
                        $p_total = 0;
                        !key_exists($cotizaciones[$i]->id_transaccion, $subtotal_moneda_conversion) ? $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion] = [] : '';
                        if (is_numeric($cot_llave)) {

                            $precio_unitario_compuesto = $cotizaciones[$i]->cotizaciones[$cot_llave]->precio_unitario - ($cotizaciones[$i]->cotizaciones[$cot_llave]->precio_unitario * $cotizaciones[$i]->cotizaciones[$cot_llave]->descuento / 100);
                            $p_total = $precio_unitario_compuesto ;
                            switch ((int)$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda) {
                                case 1:
                                    $imp_t_conver = $p_total;
                                    if (key_exists($cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda, $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion])) {
                                        $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda] = $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda] + ($p_total * $cotizaciones[$i]->cotizaciones[$cot_llave]->cantidad);
                                    } else {
                                        $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda] = ($p_total * $cotizaciones[$i]->cotizaciones[$cot_llave]->cantidad);
                                    }
                                    break;
                                case 2:
                                    $imp_t_conver = $p_total * 1000;//$cotizaciones[$i]->complemento->tc_usd;

                                    if (key_exists($cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda, $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion])) {
                                        $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda] = $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda] + ($p_total * $cotizaciones[$i]->cotizaciones[$cot_llave]->cantidad);
                                    } else {
                                        $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda] = ($p_total * $cotizaciones[$i]->cotizaciones[$cot_llave]->cantidad);
                                    }
                                    break;
                                case 3:
                                    $imp_t_conver = $p_total * 1000;//$cotizaciones[$i]->complemento->tc_eur;
                                    if (key_exists($cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda, $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion])) {

                                        $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda] = $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda] + ($p_total * $cotizaciones[$i]->cotizaciones[$cot_llave]->cantidad);
                                    } else {
                                        $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda] = ($p_total * $cotizaciones[$i]->cotizaciones[$cot_llave]->cantidad);
                                    }
                                    break;
                            }
                        }
                        $ki = 0;
                        if (is_numeric($cot_llave) && $cotizaciones[$i]->cotizaciones[$cot_llave]->precio_unitario > 0 ) {   /////  ($partidas_cotizacion[$cotizaciones[$i]["idrqctoc_cotizaciones"]][$partidas_solicitud[$p]["idrqctoc_solicitudes_partidas"]]["precio_unitario"] > 0)
                            $ki = $this->calcular_ki($imp_t_conver, $precios[$cotizaciones[$i]->cotizaciones[$cot_llave]->id_material]);
                            if ($ki == 0) {  ///// ($partidas_cotizacion[$cotizaciones[$i]["idrqctoc_cotizaciones"]][$partidas_solicitud[$p]["idrqctoc_solicitudes_partidas"]]["ki"] == 0)
                                $this->SetFillColor(150, 150, 150);
                                $this->SetTextColor(0, 0, 0);
                                //$pdf->SetDrawColor(0,255,0);
                            } else {
                                $this->SetFillColor(255, 255, 255);
                                $this->SetTextColor(0, 0, 0);
                                //$pdf->SetDrawColor(200,200,200);
                            }
                        } else {
                            $this->SetFillColor(200, 200, 200);
                            $this->SetTextColor(200, 200, 200);
                            //$pdf->SetDrawColor(200,200,200);
                        }

                        $this->SetFont('Arial', '', $font2);
                        $this->Cell($anchos["pu"], $heigth, number_format($precio_unitario_compuesto, 3, '.', ','), "T B L", 0, "R", 1);
                        $this->CellFitScale($anchos["d"], $heigth, $ki == 0 ? '-' : number_format($ki, '4', '.', ','), "T B L", 0, "R", 1);
                        $this->Cell($anchos["it"], $heigth, is_numeric($cot_llave)?number_format(($p_total * $cotizaciones[$i]->cotizaciones[$cot_llave]->cantidad), 2, '.', ','):'-', "T B L", 0, "R", 1);
                        $this->CellFitScale($anchos["m"], $heigth, is_numeric($cot_llave) && $cotizaciones[$i]->cotizaciones[$cot_llave]->precio_unitario > 0 ? $monedas[$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda]['nombre_corto'] : '-', "T B L", 0, "R", 1);
                        $this->Cell($anchos["ic"], $heigth, is_numeric($cot_llave)?number_format(($imp_t_conver * $cotizaciones[$i]->cotizaciones[$cot_llave]->cantidad), 2, '.', ','):'-', "B L R T", 0, "R", 1);

                    }

                    $this->Ln();
                    $this->SetTextColor(0, 0, 0);
                    $this->y_para_obs_partidas = $this->getY();
                    $xos_ini = $this->getX();

                    $this->MultiCell($anchos["des"], $heigth, $partida->solicitudPartidasComplemento ? utf8_decode($partida->solicitudPartidasComplemento->observaciones) : '', 1, 'L', 0, 1);///$partida->solicitudPartidasComplemento?utf8_decode($partida->solicitudPartidasComplemento->observaciones):''
                    $this->y_para_descripcion_arr[] = $this->GetY();
                    $this->y_fin_obs_par_sol_arr[] = $this->GetY();
                    $xos_ini += $anchos["des"];
                    $this->setY($this->y_para_obs_partidas);
                    $this->setX($xos_ini);
                    $this->CellFitScale($anchos["aesp"], $heigth, 'Observaciones de Partida:', 'B', 0, 'R', 0);
                    $yop_ini = $this->getY();
                    $xop_ini = $this->getX();
                    for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                        $cot_llave = array_search($partida->id_material, array_column($cotizaciones[$i]->cotizaciones->toArray(), 'id_material'));
                        //!key_exists($cotizaciones[$i]->id_transaccion, $subtotal_moneda_conversion)?$subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion] = []:'';
                        if (is_numeric($cot_llave)) {
                            $precio_unitario_compuesto = $cotizaciones[$i]->cotizaciones[$cot_llave]->precio_unitario - ($cotizaciones[$i]->cotizaciones[$cot_llave]->precio_unitario * $cotizaciones[$i]->cotizaciones[$cot_llave]->descuento / 100);
                            $p_total = $precio_unitario_compuesto ;
                            $imp_t_conver = $p_total;///$subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][$cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda];
                            $cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda == 2 ? $imp_t_conver = $cotizaciones[$i]->complemento->tc_usd * $p_total : '';
                            $cotizaciones[$i]->cotizaciones[$cot_llave]->id_moneda == 3 ? $imp_t_conver = $cotizaciones[$i]->complemento->tc_eur * $p_total : '';
                        }
                        if (is_numeric($cot_llave) && $cotizaciones[$i]->cotizaciones[$cot_llave]->precio_unitario > 0 ) {
                            $ki = $this->calcular_ki($imp_t_conver, $precios[$cotizaciones[$i]->cotizaciones[$cot_llave]->id_material]);
                            if ($ki == 0) {  //// ($partidas_cotizacion[$cotizaciones[$i]["idrqctoc_cotizaciones"]][$partidas_solicitud[$p]["idrqctoc_solicitudes_partidas"]]["ki"] == 0)
                                $this->SetFillColor(150, 150, 150);
                                $this->SetTextColor(0, 0, 0);
                                //$pdf->SetDrawColor(0,255,0);
                            } else {
                                $this->SetFillColor(255, 255, 255);
                                $this->SetTextColor(0, 0, 0);
                                //$pdf->SetDrawColor(200,200,200);
                            }
                        } else {
                            $this->SetFillColor(200, 200, 200);
                            $this->SetTextColor(200, 200, 200);
                            //$pdf->SetDrawColor(200,200,200);
                        }

                        $this->SetFont('Arial', '', $font2);
                        $this->setY($yop_ini);
                        $this->setX($xop_ini);
                        $this->MultiCell($anchos["op"], $heigth,is_numeric($cot_llave) && $cotizaciones[$i]->cotizaciones[$cot_llave]->precio_unitario > 0 ?
                            utf8_decode($cotizaciones[$i]->cotizaciones[$cot_llave]->partida->observaciones) : '-', "B L R T", "L", 1);///utf8_decode($cotizaciones[$i]->cotizaciones[$cot_llave]->cotizacion_partidas_complemento->observaciones)
                        $this->y_para_descripcion_arr[] = $this->GetY();
                        $xop_ini += $anchos["op"];

                    }
                    $this->Ln();
                }

                asort($this->y_fin_obs_par_sol_arr);
                $this->y_fin_obs_par_sol = array_pop($this->y_fin_obs_par_sol_arr);
                $this->SetY($this->y_fin_obs_par_sol);
                $this->Ln();
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["espacio_detalles_globales"]);
                $this->Cell($anchos["espacio_detalles_globales"], $heigth, "% Descuento Global", 1, 0, "R", 1);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetFont('Arial', 'B', $font);
                    $this->Cell($anchos["ar"] + $anchos["it"], $heigth);
                    $this->Cell($anchos["dg"], $heigth, $cotizaciones[$i]->complemento->descuento && $cotizaciones[$i]->complemento->descuento > 0 ?$cotizaciones[$i]->complemento->descuento:'-' , 1, 0, 'R', 1);
                }
                $this->Ln();

                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["espacio_detalles_globales"]);
                $this->Cell($anchos["espacio_detalles_globales"], $heigth, utf8_decode("Subtotal Moneda  Conversión:"), 1, 0, "R", 1);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetFont('Arial', 'B', $font);
                    $this->Cell($anchos["ar"] + $anchos["it"], $heigth);
                    $this->Cell($anchos["dg"], $heigth, $cotizaciones[$i]->complemento->importe > 0? number_format($cotizaciones[$i]->complemento->importe, 2, '.', ','):'-', 1, 0, 'R', 1);
                }
                $this->Ln();
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["espacio_detalles_globales"]);
                $this->Cell($anchos["espacio_detalles_globales"], $heigth, "IVA:", 1, 0, "R", 1);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetFont('Arial', 'B', $font);
                    $this->Cell($anchos["ar"] + $anchos["it"], $heigth);
                    $this->Cell($anchos["dg"], $heigth, $cotizaciones[$i]->impuesto > 0 ? number_format($cotizaciones[$i]->impuesto, 2, '.', ','):'-', 1, 0, 'R', 1);
                }

                $this->Ln();
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["espacio_detalles_globales"]);
                $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Total:", 1, 0, "R", 1);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetFont('Arial', 'B', $font);
                    $this->Cell($anchos["ar"] + $anchos["it"], $heigth);
                    $this->Cell($anchos["dg"], $heigth, $cotizaciones[$i]->monto > 0 ?number_format($cotizaciones[$i]->monto, 2, '.', ','):'-', 1, 0, 'R', 1);
                }

                $this->Ln();
                $this->Ln();
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["espacio_detalles_globales"]);
                $this->Cell($anchos["espacio_detalles_globales"], $heigth, utf8_decode("Moneda  Conversión:"), 1, 0, "R", 1);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetFont('Arial', 'B', $font);
                    $this->Cell($anchos["ar"] + $anchos["it"], $heigth);
                    $this->Cell($anchos["dg"], $heigth, $monedas[$cotizaciones[$i]->id_moneda]['nombre'], 1, 0, 'R', 1);
                }

                $this->Ln();
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["espacio_detalles_globales"]);
                $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Subtotal Importe Peso (MXP):", 1, 0, "R", 1);
                //dd($subtotal_moneda_conversion, $cotizaciones);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetFont('Arial', 'B', $font);

                    $this->Cell($anchos["ar"] + $anchos["it"], $heigth);
                    $this->Cell($anchos["dg"], $heigth, key_exists(1, $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion]) ? number_format($subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][1], 2, '.', ',') : '-', 1, 0, 'R', 1);
                }
                $this->Ln();
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["espacio_detalles_globales"]);
                $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Subtotal Importe Dolar(USD):", 1, 0, "R", 1);

                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetFont('Arial', 'B', $font);

                    $this->Cell($anchos["ar"], $heigth, key_exists(2, $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion]) ? number_format($subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][2], 2, '.', ',') : '-', 1, 0, 'R', 1);
                    $this->Cell($anchos["tc"], $heigth, number_format($cotizaciones[$i]->complemento->tc_usd, 4, '.', ','), 1, 0, 'R', 1);

                    $this->Cell($anchos["dg"], $heigth, key_exists(2, $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion]) ? number_format(($subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][2] * 0/*$cotizaciones[$i]->complemento->tc_usd*/), 2, '.', ',') : '-', 1, 0, 'R', 1);
                }
                $this->Ln();
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["espacio_detalles_globales"]);
                $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Subtotal Importe EURO:", 1, 0, "R", 1);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetFont('Arial', 'B', $font);
                    $this->Cell($anchos["ar"], $heigth, key_exists(3, $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion]) ? number_format($subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][3], 2, '.', ',') : '-', 1, 0, 'R', 1);
                    $this->Cell($anchos["tc"], $heigth, number_format($cotizaciones[$i]->complemento->tc_eur, 4, '.', ','), 1, 0, 'R', 1);
                    $this->Cell($anchos["dg"], $heigth, key_exists(3, $subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion]) ? number_format(($subtotal_moneda_conversion[$cotizaciones[$i]->id_transaccion][3] * 0/*$cotizaciones[$i]->complemento->tc_eur*/), 2, '.', ',') : '-', 1, 0, 'R', 1);
                }
                $this->Ln();
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["espacio_detalles_globales"]);
                $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Observaciones Globales:", 1, 0, "R", 1);
                $y_ini = $this->getY();
                $x_ini = $this->getX();
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetFont('Arial', 'B', $font);
                    $this->setY($y_ini);
                    $this->setX($x_ini);
                    $this->MultiCell($anchos["og"], $heigth, utf8_decode($cotizaciones[$i]["observaciones"]), 1, 'J', false);
                    $this->y_fin_og_arr[] = $this->getY();
                    $x_ini += $anchos["og"];
                }
                asort($this->y_fin_og_arr);
                $this->y_fin_og = array_pop($this->y_fin_og_arr);
                $this->SetY($this->y_fin_og);
                $i_e += $cotizacinesXFila;
                $this->Ln();
            }
        }
    }

    public function calcular_ivg(array $precios_menores, array $partidas_cotizacion, $tc_euro, $tc_dlls){
        $ivg = 0;
        foreach ($partidas_cotizacion as $partida){
            $tc = 1;
            $partida['id_moneda'] == 2?$tc = $tc_dlls:'';
            $partida['id_moneda'] == 3?$tc = $tc_euro:'';
            $ivg += $partida['precio_unitario'] > 0? $this->calcular_ki(  ($partida['precio_unitario'] - ($partida['precio_unitario'] * $partida['descuento'] / 100)) * $tc, $precios_menores[$partida['id_material']]):0;
        }
        return count($partidas_cotizacion) > 0 ? $ivg : -1;
    }
    public function calcular_ki($precio, $precio_menor){
        return ($precio - $precio_menor) / $precio_menor;
    }

    function Footer() {
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'BI', 5.5);
        $this->SetY(-4);

        if (Context::getDatabase() == "SAO1814" && Context::getIdObra() == 41) {
            //if(true){
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);
            $this->Cell(6.6, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(6.6, .4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            //$this->Cell(4.8, .5, 'APROBO', 'TRLB', 0, 'C',1);
            $this->Cell(12.8, .4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
            //$this->Cell(3.9, .5, 'AUTORIZO', 'TRLB', 0, 'C',1);
            $this->Ln();
            $this->Cell(6.6, .4, 'Jefe Compras', 'TRLB', 0, 'C', 1);
            $this->Cell(6.6, .4, 'Gerente Administrativo', 'TRLB', 0, 'C', 1);
            $this->Cell(6.4, .4, 'Control de Costos', 'TRLB', 0, 'C', 1);
            $this->Cell(6.4, .4, 'Director de proyecto', 'TRLB', 0, 'C', 1);
            //$this->Cell(5, .5, '', 'TRLB', 0, 'C');
            $this->Ln();

            $this->Cell(6.6, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(6.6, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(6.4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(6.4, 1.2, '', 'TRLB', 0, 'C');
            //$this->Cell(4.8, .9, '', 'TRLB', 0, 'C');
            $this->Ln();
            //$this->SetFont('Arial','',7);
            $this->Cell(6.6, .4, 'LIC. BRENDA ELIZABETH ESQUIVEL ESPINOZA', 'TRLB', 0, 'C', 1);
            $this->Cell(6.6, .4, 'C.P. ROGELIO HERNANDEZ BELTRAN', 'TRLB', 0, 'C', 1);
            $this->Cell(6.4, .4, 'ING. JUAN CARLOS MARTINEZ ANTUNA', 'TRLB', 0, 'C', 1);
            $this->Cell(6.4, .4, 'ING. PEDRO ALFONSO MIRANDA REYES', 'TRLB', 0, 'C', 1);
            $this->Ln();
            $this->Ln();
        } else {


            $this->SetY(-3.5);
            //$this->image("http://saoweb.grupohi.mx/libraries/PHPQRCode/qr.php?cadena=".urlencode($this->cadena_qr), 0.75, $this->GetY(), 2.5, 2.5,'PNG');
            $this->image("http://172.20.74.94/libraries/PHPQRCode/qr.php?cadena=".urlencode($this->cadena_qr), $this->GetX(), $this->GetY(), 2.65, 2.65,'PNG');
            $this->SetY(-3.5);
            $this->setX(3.5);
            //$this->SetFont('Arial', '', 4.5);
            $this->MultiCell(24.5, 0.4, $this->cadena);
            $this->SetY(16.5);
            $this->setX(4.5);


            $this->SetFont('Arial', 'B', 6.5);
            $this->SetTextColor('100,100,100');
            $this->SetY(-1.3);
            $this->Cell(27.5, .4, utf8_decode('Sistema de Administración de Obra'), 0, 0, 'R');
            $this->SetY(-0.9);
            $this->SetFont('Arial', 'BI', 6.5);
            $this->SetTextColor('0,0,0');
            $this->SetX(1);
            $this->Cell(11.5, .4, (utf8_decode('Formato generado desde el módulo de ordenes de compra.')), 0, 0, 'L');
            $this->Cell(16, .4, (utf8_decode('Página ')) . $this->PageNo() . '/{nb}', 0, 0, 'R');
            //$this->y_para_descripcion=  $this->GetY();
        }
    }
    function create()
    {
        $this->SetMargins(0.7, 1, 0.7);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 4.5);
        $this->partidas();

        try {
            $this->Output('I', 'Formato - Cotizacion.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }

}