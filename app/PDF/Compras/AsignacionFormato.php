<?php


namespace App\PDF\Compras;


use App\Facades\Context;
use App\Models\CADECO\Compras\AsignacionProveedor;
use App\Models\CADECO\Obra;
use App\Utils\ValidacionSistema;
use Ghidev\Fpdf\Rotation;
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

        $this->SetAutoPageBreak(true, 5);
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

        $this->Ln(.5);
        $this->Cell(19);
        $this->Cell(4, .5, utf8_decode('REQUISICIÓN SAO: '), 'LB', 0, 'L');
        $this->Cell(3, .5, $this->asignacion->solicitud->complemento ? $this->asignacion->solicitud->complemento->requisicion_folio_format : '', 'RB', 0, 'L');

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
        $partidas = [];
        //$no_cotizaciones = count($this->asignacion->solicitud->cotizaciones);
        $no_cotizaciones = 1;
        $total_asignado = 0;
        $subtotal_asignado = 0;
        $iva_total_asignado = 0;
        $asignaciones = $this->asignacion->solicitud->asignacionesProveedores;
        foreach ($this->asignacion->solicitud->items as $key => $item) {
            if (array_key_exists($item->id_material, $partidas)) {
                $partidas[$item->id_material]->cantidad = $partidas[$item->id_material]['cantidad'] + $item->cantidad;
            } else {
                $partidas[$item->id_material] = $item;
            }
        }
        $font = 5;
        $font2 = 4;
        $font_importes = 5;
        $heigth = 0.3;
        $cotizacinesXFila = 3;
        $anchos["des"] = 4.5;
        $anchos["u"] = $anchos["c"] = $anchos["ca"] = 0.71;
        $anchos["aesp"] = $anchos["u"] + $anchos["c"];
        $anchos["espacio_detalles_globales"] = ($anchos["aesp"] + $anchos["des"]) / 2;

        $anchos["pu"] = $anchos["fe"] = 1.1;
        $anchos["d"] = $anchos["ant"] = 0.4;
        $anchos["tc"] = $anchos["it"] = $anchos["ita"] = 0.9;
        $anchos["m"] = 0.4;
        $anchos["ic"] = 0.9;

        $anchos["dg"] = $anchos["pu"] + $anchos["d"];
        //$anchos["op"] = $anchos["og"] = $anchos["p"] = $anchos["pu"] + $anchos["d"] + $anchos["it"] + $anchos["m"] + $anchos["ic"] + $anchos["ca"] + $anchos["ita"];
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

            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(0, 0, 0);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, utf8_decode('PANDA'), 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, "PANDA", 1, 0, 'C', 0);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            $asignados = array();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', '', $font);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["fe"] + $anchos["fe"], $heigth, utf8_decode("Fecha Cotización:"), 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["fe"], $heigth, '2/Oct/2012', 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["vig"], $heigth, "Vigencia:", 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["vig"], $heigth, '34/ago/2020', 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["vig"], $heigth, utf8_decode("Días"), 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', '', $font);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ant"], $heigth, "Anticipo:", 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["ant"], $heigth, '-', 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ant"], $heigth, "%", 1, 0, 'C', 1);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["cre"], $heigth, utf8_decode("Crédito:"), 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["cre"], $heigth, '-', 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["cre"], $heigth, utf8_decode("Días"), 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', '', $font);
                $this->CellFitScale($anchos["ent"] * 2, $heigth, "", 1, 0, 'C', 1);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ent"] * 2, $heigth, "Plazo de Entrega:", 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["ent"], $heigth,  '-', 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ent"], $heigth, utf8_decode("Días"), 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, "", 0, 0, 'C', 0);

            endfor;
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetTextColor(255, 255, 255);
                $this->SetFillColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"] * 4, $heigth, "Cotizado", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"] * 2, $heigth, "Asignado", 1, 0, 'C', 1);

            endfor;
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);

            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"], $heigth, "Precio", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Importe Moneda", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
            endfor;
            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["des"], $heigth, utf8_decode("Descripción"), 1, 0, 'C', 1);
            $this->Cell($anchos["u"], $heigth, "Unidad", 1, 0, 'C', 1);
            $this->Cell($anchos["c"], $heigth, "Cantidad", 1, 0, 'C', 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"], $heigth, "Unitario", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Importe", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Moneda", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Comparable", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Cantidad", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Importe", 1, 0, 'C', 1);
            endfor;
            $this->Ln();
            $this->y_para_descripcion = $this->GetY();
            $this->y_para_descripcion_arr[] = $this->GetY();
            $partidas_solicitud = ['uno' => 1,'dos' => 2, 'tres' => 3];
            $subtotal_adesc_f = array();
            $resumen_moneda = array();
            foreach ($partidas_solicitud as $partida_solicitud){
                asort($this->y_para_descripcion_arr);
                $this->y_para_descripcion = array_pop($this->y_para_descripcion_arr);
                $this->SetY($this->y_para_descripcion);
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font2);
                $this->CellFitScale($anchos["des"], $heigth, 'Descripcion del texto', 1, 0, 'L', 0, '');
                $this->Cell($anchos["c"], $heigth, 'MXS', 1, 0, 'L', 0, '');
                $this->Cell($anchos["u"], $heigth, number_format(100,2, '.', ''), 1, 0, 'L', 0, '');

                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
//                    $partida_cotizacion = Cotizacion::where('id_transaccion', '=', $cotizaciones[$i]->id_transaccion)->where('id_material', '=', $partida_solicitud->id_material)->first();
//                    if (count($partida_cotizacion) > 0 && $partida_cotizacion->precio_unitario > 0) {
//                        if ($this->calcular_ki($partida_cotizacion->precio_unitario, $precios[$partida_solicitud->id_material]) == 0) {
//                            $this->SetFillColor(150, 150, 150);
//                            $this->SetTextColor(0, 0, 0);
////
////
//                        } else {
                            $this->SetFillColor(255, 255, 255);
                            $this->SetTextColor(0, 0, 0);
//                        }
//                        $partidas_comprobacion_mejor_opcion[$partida_cotizacion->id_transaccion][$partida_solicitud->id_item]["cantidad"] = $partida_cotizacion->cantidad;
//                        $partidas_comprobacion_mejor_opcion[$partida_cotizacion->id_transaccion][$partida_solicitud->id_item]["precio_unitario_conv"] =
//                            $partida_cotizacion->precio_unitario - ($partida_cotizacion->precio_unitario * $partida_cotizacion->descuento / 100);
//                        $asignacion_partida = AsignacionProveedorPartida::where('id_item_solicitud', '=', $partida_solicitud->id_item)->where('id_transaccion_cotizacion', '=', $partida_cotizacion->id_transaccion)->where('id_asignacion_proveedores', '=', $this->asignacion->id)->first();
//                        $cant_asignada = $asignacion_partida != null ? $asignacion_partida->cantidad_asignada : 0;
//
//                        array_key_exists($partida_cotizacion->id_transaccion, $partidas_comprobacion_mejor_opcion) ?
//                            array_key_exists($partida_solicitud->id_item, $partidas_comprobacion_mejor_opcion[$partida_cotizacion->id_transaccion]) ?
//                                array_key_exists('cantidad_asignada',  $partidas_comprobacion_mejor_opcion[$partida_cotizacion->id_transaccion][$partida_solicitud->id_item]) ? $partidas_comprobacion_mejor_opcion[$partida_cotizacion->id_transaccion][$partida_solicitud->id_item]["cantidad_asignada"] += $cant_asignada
//                                    :$partidas_comprobacion_mejor_opcion[$partida_cotizacion->id_transaccion][$partida_solicitud->id_item]["cantidad_asignada"] = $cant_asignada
//                                :$partidas_comprobacion_mejor_opcion[$partida_cotizacion->id_transaccion][$partida_solicitud->id_item]["cantidad_asignada"] = $cant_asignada
//                            :$partidas_comprobacion_mejor_opcion[$partida_cotizacion->id_transaccion][$partida_solicitud->id_item]["cantidad_asignada"] = $cant_asignada;


//                        $precio_desc = $partida_cotizacion->precio_unitario - ($partida_cotizacion->precio_unitario * $partida_cotizacion->cotizacion_partidas_complemento->descuento_partida / 100);
//                        $t_c = 1;
//                        $partida_cotizacion->id_moneda == 2 ? $t_c = $partida_cotizacion->cotizacion_compra->complemento->tc_usd : '';
//                        $partida_cotizacion->id_moneda == 3 ? $t_c = $partida_cotizacion->cotizacion_compra->complemento->tc_eur : '';
                        $this->SetFont('Arial', '', $font_importes);
                        $this->Cell($anchos["pu"], $heigth, number_format(2334, 2, '.', ','), "T B L", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, number_format(54322, 2, '.', ','), "T B L", 0, "R", 1);
                        $this->CellFitScale($anchos["pu"], $heigth, 'nombre', "T B L", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, number_format(5555, 2, '.', ','), "B L R T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, '-', "B L R T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, '-Mucho', "B L R T", 0, "R", 1);
//
                    //__JAJAJA
//                        array_key_exists($partida_cotizacion->id_transaccion, $subtotal_adesc_f) ? $subtotal_adesc_f[$partida_cotizacion->id_transaccion] += ($precio_desc * $partida_solicitud->cantidad * $t_c)
//                            : $subtotal_adesc_f[$partida_cotizacion->id_transaccion] = ($precio_desc * $partida_solicitud->cantidad * $t_c);
//                        array_key_exists($partida_cotizacion->id_transaccion, $asignados) ? $asignados[$partida_cotizacion->id_transaccion] += ($cant_asignada * $precio_desc * $t_c)
//                            : $asignados[$partida_cotizacion->id_transaccion] = ($cant_asignada * $precio_desc * $t_c);
//                        array_key_exists($partida_cotizacion->cotizacion_compra->id_empresa, $resumen_moneda) ?
//                            array_key_exists($partida_cotizacion->id_transaccion, $resumen_moneda[$partida_cotizacion->cotizacion_compra->id_empresa]) ?
//                                array_key_exists($partida_cotizacion->id_moneda, $resumen_moneda[$partida_cotizacion->cotizacion_compra->id_empresa][$partida_cotizacion->id_transaccion]) ? $resumen_moneda[$partida_cotizacion->cotizacion_compra->id_empresa][$partida_cotizacion->id_transaccion][$partida_cotizacion->id_moneda] += ($cant_asignada * $precio_desc * $t_c)
//                                    : $resumen_moneda[$partida_cotizacion->cotizacion_compra->id_empresa][$partida_cotizacion->id_transaccion][$partida_cotizacion->id_moneda] = ($cant_asignada * $precio_desc * $t_c)
//                                : $resumen_moneda[$partida_cotizacion->cotizacion_compra->id_empresa][$partida_cotizacion->id_transaccion][$partida_cotizacion->id_moneda] = ($cant_asignada * $precio_desc * $t_c)
//                            : $resumen_moneda[$partida_cotizacion->cotizacion_compra->id_empresa][$partida_cotizacion->id_transaccion][$partida_cotizacion->id_moneda] = ($cant_asignada * $precio_desc * $t_c);
//                    } else {
//                        $subtotal_adesc_f[$cotizaciones[$i]->id_transaccion] = 0;
//                        $asignados[$cotizaciones[$i]->id_transaccion] = 0;
//                        $resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion][$cotizaciones[$i]->id_moneda] = 0;
//                        $this->SetFillColor(200, 200, 200);
//                        $this->SetTextColor(200, 200, 200);
//                        $this->Cell($anchos["pu"], $heigth, '', "T B L", 0, "R", 1);
//                        $this->Cell($anchos["pu"], $heigth, '', "T B L", 0, "R", 1);
//                        $this->CellFitScale($anchos["pu"], $heigth, '', "T B L", 0, "R", 1);
//                        $this->Cell($anchos["pu"], $heigth, '', "B L R T", 0, "R", 1);
//                        $this->Cell($anchos["pu"], $heigth, '', "B L R T", 0, "R", 1);
//                        $this->Cell($anchos["pu"], $heigth, '', "B L R T", 0, "R", 1);
//                    }


                endfor;
                    //-----Primera
                $this->Ln();
                $this->SetTextColor(0, 0, 0);
                $yos_ini = $this->getY();
                $xos_ini = $this->getX();
                $this->y_para_obs_partidas = $this->getY();
                $this->SetFont('Arial', 'B', $font2);
                $this->MultiCell($anchos["des"], $heigth,  '', 1, 'L', 0, 1); ///$partidas_solicitud[$p]["observaciones"]
                $this->y_para_descripcion_arr[] = $this->GetY();
                $this->y_fin_obs_par_sol_arr[] = $this->GetY();
                $xos_ini += $anchos["des"];
                $this->setY($this->y_para_obs_partidas);
                $this->setX($xos_ini);
                $this->SetFont('Arial', 'B', $font2);
                $this->CellFitScale($anchos["aesp"], $heigth, 'Observaciones de Partida:', 'B', 0, 'R', 0);
                $yop_ini = $this->getY();
                $xop_ini = $this->getX();
//                Segunda
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
//                    $partida_cotizacion = Cotizacion::where('id_transaccion', '=', $cotizaciones[$i]->id_transaccion)->where('id_material', '=', $partida_solicitud->id_material)->first();
//                    if (count($partida_cotizacion) > 0 && $partida_cotizacion->precio_unitario > 0) {
//                        if ($this->calcular_ki($partida_cotizacion->precio_unitario, $precios[$partida_solicitud->id_material]) == 0) {
//                            $this->SetFillColor(150, 150, 150);
//                            $this->SetTextColor(0, 0, 0);
//                        } else {
//                            $this->SetFillColor(255, 255, 255);
//                            $this->SetTextColor(0, 0, 0);
//                        }
//                    } else {
//
                        $this->SetFillColor(200, 200, 200);
                        $this->SetTextColor(200, 200, 200);
//                    }
                    $this->SetFont('Arial', '', $font);
                    $this->setY($yop_ini);
                    $this->setX($xop_ini);
                    $this->MultiCell($anchos["op"], $heigth, 'Otra cotizacion', "B L R T", "L", 1);
                    $this->y_para_descripcion_arr[] = $this->GetY();
                    $xop_ini += $anchos["op"];
                endfor;
                $this->Ln();
            }
            asort($this->y_fin_obs_par_sol_arr);
            $this->y_fin_obs_par_sol = array_pop($this->y_fin_obs_par_sol_arr);
            $this->SetY($this->y_fin_obs_par_sol);
//
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetTextColor(255, 255, 255);
                $this->SetFillColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"] * 4, $heigth, "Cotizado", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"] * 2, $heigth, "Asignado", 1, 0, 'C', 1);

            endfor;
//
            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Subtotal Antes Descuentos", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["pu"] * 2, $heigth,"",1,0,"",1);
                $this->Cell($anchos["pu"], $heigth, 'PESO (MX)', 1, 0, 'R', 1);
                $this->Cell($anchos["pu"], $heigth, number_format(7655, 2, ".", ","), 1, 0, 'R', 1);
//                if ($asignados[$cotizaciones[$i]->id_transaccion] > 0) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
//                } else {
//                    $this->SetFillColor(255, 255, 255);
//                    $this->SetTextColor(0, 0, 0);
//                }
                $this->Cell($anchos["pu"]*2, $heigth, number_format(84655, 2, ".", ","), 1, 0, 'R', 1);
            endfor;
//
            $this->Ln();
            $this->Ln(0.1);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "% Descuento Global", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
//                $importe_descuento_f[$cotizaciones[$i]->id_transaccion] = $subtotal_adesc_f[$cotizaciones[$i]->id_transaccion] * $cotizaciones[$i]->complemento->descuento/100;
                $this->Cell($anchos["pu"] * 2, $heigth,  '-', 1, 0, 'R', 1);
                $this->Cell($anchos["pu"], $heigth, "%", 1, 0, "C");
                $this->Cell($anchos["pu"], $heigth, '-', 1, 0, 'R', 1);
//                if ($asignados[$cotizaciones[$i]->id_transaccion] > 0) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
//                } else {
//                    $this->SetFillColor(255, 255, 255);
//                    $this->SetTextColor(0, 0, 0);
//                }
                $this->Cell($anchos["pu"] * 2, $heigth,'-', 1, 0, 'R', 1);
            endfor;
//
            $this->Ln();
            $this->Ln(0.1);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Subtotal:", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetFillColor(255, 255, 255);
                $this->SetDrawColor(199, 199, 199);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["pu"] * 2, $heigth);
                $this->Cell($anchos["pu"], $heigth, 'PESO (MX)', 1, 0, 'R', 1);
                $this->Cell($anchos["pu"], $heigth, number_format(23455, 2, ".", ","), 1, 0, 'R', 1);

//                if ($asignados[$cotizaciones[$i]->id_transaccion] > 0) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
//                    $subtotal_asignado += $asignados[$cotizaciones[$i]->id_transaccion]-($asignados[$cotizaciones[$i]->id_transaccion]*$cotizaciones[$i]->complemento->descuento/100);
//                } else {
//                    $this->SetFillColor(255, 255, 255);
//                    $this->SetTextColor(0, 0, 0);
//                }
                $this->Cell($anchos["pu"]*2, $heigth, number_format(433234, 2, ".", ","), 1, 0, 'R', 1);
            endfor;
            $this->Ln();
//            $this->Ln(0.1);
            $this->SetFillColor(100, 100, 100);
            $this->SetDrawColor(0, 0, 0);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "IVA:", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetFillColor(255, 255, 255);
                $this->SetDrawColor(199, 199, 199);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["pu"] * 2, $heigth);
//                if($cotizaciones[$i]->complemento->importe >0){
//                    $tasa_iva = $cotizaciones[$i]->impuesto * 100 / $cotizaciones[$i]->complemento->importe / 100;
//                }else{
//                    $tasa_iva = 0;
//                }
                $this->Cell($anchos["pu"], $heigth, 'PESO (MX)', 1, 0, 'R', 1);
//                $iva_c[$cotizaciones[$i]->id_transaccion] = ($subtotal_adesc_f[$cotizaciones[$i]->id_transaccion] - $importe_descuento_f[$cotizaciones[$i]->id_transaccion]) * $tasa_iva;
                $this->Cell($anchos["pu"], $heigth, number_format(13562.4, 2, ".", ","), 1, 0, 'R', 1);

//                $resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion]["tasa_iva"] = $tasa_iva;
//                if ($asignados[$cotizaciones[$i]->id_transaccion] > 0) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
//                    $iva_total_asignado += ($asignados[$cotizaciones[$i]->id_transaccion]-($asignados[$cotizaciones[$i]->id_transaccion]*$cotizaciones[$i]->complemento->descuento / 100)) * $tasa_iva;
//                } else {
//                    $this->SetFillColor(255, 255, 255);
//                    $this->SetTextColor(0, 0, 0);
//                }
                $this->Cell($anchos["pu"] * 2, $heigth, number_format(13562.4, 2, ".", ","), 1, 0, 'R', 1);
            endfor;
            $this->Ln();
//            $this->Ln(0.1);
            $this->SetFillColor(100, 100, 100);
            $this->SetDrawColor(0, 0, 0);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Total:", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetFillColor(255, 255, 255);
                $this->SetDrawColor(199, 199, 199);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["pu"] * 2, $heigth);
                $this->Cell($anchos["pu"], $heigth, 'PESO (MX)', 1, 0, 'R', 1);
//                $total_f[$cotizaciones[$i]->id_transaccion] = ($subtotal_adesc_f[$cotizaciones[$i]->id_transaccion]-$importe_descuento_f[$cotizaciones[$i]->id_transaccion]) * (1+$tasa_iva);
                $this->Cell($anchos["pu"], $heigth, number_format(98321.6,2,".",","), 1, 0, 'R', 1);
//                if ($asignados[$cotizaciones[$i]->id_transaccion] > 0) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
//                    $total_asignado += ($asignados[$cotizaciones[$i]->id_transaccion]-($asignados[$cotizaciones[$i]->id_transaccion]*$cotizaciones[$i]->complemento->descuento/100)) * (1 + $tasa_iva);
//                } else {
//                    $this->SetFillColor(255, 255, 255);
//                    $this->SetTextColor(0, 0, 0);
//                }
//                if($cotizaciones[$i]->complemento->importe >0){
//                    $tasa_iva = $cotizaciones[$i]->impuesto*100/$cotizaciones[$i]->complemento->importe/100;
//                }else{
//                    $tasa_iva = 0;
//                }
                $this->Cell($anchos["pu"] * 2, $heigth, number_format(98321.3,2,".",","), 1, 0, 'R', 1);
            endfor;
            $this->Ln();
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetTextColor(255, 255, 255);
                $this->SetFillColor(100, 100, 100);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"] * 6, $heigth, "Observaciones Globales", 1, 0, 'C', 1);
            endfor;

            $this->Ln();

            $this->Cell($anchos["aesp"] + $anchos["des"]);
            $y_ini = $this->getY();
            $x_ini = $this->getX();

            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->setY($y_ini);
                $this->setX($x_ini);
                $this->MultiCell($anchos["og"], $heigth, 'Se  asigna  la  compra  al  proveedor  SOLUCIONES  EN  COMPUTO  SARYBA,derivado  de  que  su  propuesta  en  conjunto  es  mas  baja.        Tiempo  deEntrega Optimo de 2 a 4 dias.', 1, 'J', false);
                $this->y_fin_og_arr[] = $this->getY();
                $x_ini+=$anchos["og"];
            endfor;
            asort($this->y_fin_og_arr);
            $this->y_fin_og = array_pop($this->y_fin_og_arr);
            $this->SetY($this->y_fin_og);
            $this->Ln();
            //$this->Ln();


            $total_asignado_ic = array();

            $this->Ln();
            $this->Cell($anchos["espacio_detalles_globales"]*2);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell($anchos["p"]*3,.5, utf8_decode("RESUMEN DE ASIGNACIÓN POR MONEDA"), "B", 1, 'C', 0);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "", 0, 0, 'C', 0);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetFillColor(0, 0, 0);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, utf8_decode('COMUNICACIONES ETC SA DE CV'), 1, 0, 'C', 1);
            endfor;
            $this->Ln();
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "TIPO DE CAMBIO", 1, 0, 'C', 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"]*3, $heigth, "TOTAL EN PESOS(MX)", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Pagaderos en:", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"]*2, $heigth, "", 1, 0, 'C', 1);
            endfor;
//
            $this->Ln();
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(0, 0, 0);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "1", 1, 0, 'C', 0);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
//                $res_moneda = array_key_exists(1, $resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion]) ? $resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion][1]:0;
                $this->SetFont('Arial', 'B', $font);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"]*3, $heigth,number_format(98321.6,2,".",","), 1, 0, 'R', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["pu"], $heigth, "PESOS (MX)", 1, 0, 'C', 1);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"]*2, $heigth, number_format(98321.6,2,".",","), 1, 0, 'R', 0);
//                array_key_exists($cotizaciones[$i]->id_empresa, $total_asignado_ic) ? array_key_exists($cotizaciones[$i]->id_transaccion, $total_asignado_ic[$cotizaciones[$i]->id_empresa]) ?
//                    $total_asignado_ic[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion] += $res_moneda * (1+$resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion]["tasa_iva"]) * 1:
//                    $total_asignado_ic[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion] = $res_moneda * (1+$resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion]["tasa_iva"]) * 1:
//                    $total_asignado_ic[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion] = $res_moneda * (1+$resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion]["tasa_iva"]) * 1;
            endfor;
//
            $mon_extranjeras = [1,2,3];   #BORRAR
            $billetes = 0;
            foreach ($mon_extranjeras as $key => $moneda_e) {
//                $tc_ = TipoCambio::cambio(1, $key);
                $billetes ++;
                $this->Ln();
                $this->Cell($anchos["espacio_detalles_globales"]);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(0, 0, 0);
                $this->Cell($anchos["espacio_detalles_globales"], $heigth, 'PESO'.$billetes, 1, 0, 'C', 0);
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
//                    $res_moneda = array_key_exists($key, $resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion]) ? $resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion][$key]:0;
                    $this->SetFont('Arial', 'B', $font);
                    $this->SetTextColor(0, 0, 0);
                    $this->CellFitScale($anchos["pu"] * 3, $heigth, '-', 1, 0, 'R', 0);
                    $this->SetTextColor(255, 255, 255);
                    $this->CellFitScale($anchos["pu"], $heigth, 'DOLAR(USD)', 1, 0, 'C', 1);
                    $this->SetTextColor(0, 0, 0);
                    $this->CellFitScale($anchos["pu"] * 2, $heigth, '-', 1, 0, 'R', 0);
//                    array_key_exists($cotizaciones[$i]->id_empresa, $total_asignado_ic) ? array_key_exists($cotizaciones[$i]->id_transaccion, $total_asignado_ic[$cotizaciones[$i]->id_empresa]) ?
//                        $total_asignado_ic[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion] += $res_moneda * (1+$resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion]["tasa_iva"]) * $tc_ :
//                        $total_asignado_ic[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion] = $res_moneda * (1+$resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion]["tasa_iva"]) * $tc_ :
//                        $total_asignado_ic[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion] = $res_moneda * (1+$resumen_moneda[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion]["tasa_iva"]) * $tc_ ;
                endfor;
            }
//
            $this->Ln();
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetDrawColor(0,0,0);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "TOTAL IVA INCLUIDO", 1, 0, 'R', 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++):
//                $t_asignado = $total_asignado_ic[$cotizaciones[$i]->id_empresa][$cotizaciones[$i]->id_transaccion];
                $this->SetDrawColor(0,0,0);
                $this->SetFont('Arial', 'B', $font);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"]*3, $heigth, number_format(98342.1,2,".",","), 1, 0, 'R', 0);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"], $heigth, "PESOS (MX)", 1, 0, 'C', 0);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"]*2, $heigth, "", 0, 0, 'C', 0);
                $this->SetDrawColor(200,200,200);
            endfor;
            $this->Ln();
            $this->Ln(.2);

//            $i_e+=$cotizacinesXFila;
        }
    }

    public function Footer()
    {
        $this->SetTextColor(0, 0, 0);
        $this->SetY(-5.8);
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
        $this->Cell(11.5, .4, utf8_decode('Formato generado desde el sistema de compras. Fecha de registro: ' . date("d-m-Y", strtotime($this->asignacion->fecha_format))).' Fecha de consulta: '.date("d-m-Y H:i:s"), 0, 0, 'L');
        $this->Cell(15, .4, (utf8_decode('Página ')) . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create()
    {
        $this->SetMargins(1, .5, 2);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 2);
        $this->partidas();

        try {
            $this->Output('I', 'Asignación de Proveedores.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }
}
