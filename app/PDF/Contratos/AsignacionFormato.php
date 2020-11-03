<?php


namespace App\PDF\Contratos;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Subcontratos\AsignacionContratista;
use Ghidev\Fpdf\Rotation;

class AsignacionFormato extends Rotation
{
    protected $obra;
    protected $asignacion;
    private $encabezado_pdf = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 279;
    const A4_WIDTH = 216;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(AsignacionContratista $asignacion)
    {
        parent::__construct('L', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());
        $this->asignacion = $asignacion;
        $this->encabezado_pdf = utf8_decode($this->obra->facturar);
        //$this->createQR();
    }

    function Header()
    {
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(23, 1.5, utf8_decode('TABLA DE ASIGNACIÓN DE PROVEEDORES'), 0, 0, 'C', 0);
        $this->SetFont('Arial', 'B', 7);
        $this->SetX(20.4);
        $this->Cell(4, .5, 'FOLIO:', 'L T', 0, 'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(3, .5, $this->asignacion->numero_folio_format, 'R T', 0, 'L');
        $this->Ln(.5);
        $this->Cell(19.7);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(4, .5, 'FECHA:  ', 'L B', 0, 'L');
        $this->Cell(3, .5, $this->asignacion->fecha_registro_format, 'R B', 0, 'L');
        $this->SetFont('Arial', '', 6);
        $this->Ln(.5);
        $this->Cell(19.7);
        $this->Cell(4, .5, 'CONTRATO:', 'LB', 0, 'L');
        $this->Cell(3, .5, $this->asignacion->contratoProyectado->numero_folio_format, 'RB', 0, 'L');

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
        $no_presupuestos = count($this->asignacion->contratoProyectado->presupuestos);
        dd($this->asignacion->datosComparativos());
        $presupuestos = $this->asignacion->contratoProyectado->presupuestos;

        $datos_partidas_globales = [];

        $font = 5;
        $font2 = 4;
        $font_importes = 5;
        $heigth = 0.3;
        $presupuestosXFila = 3;
        $bandera_asignacion = 0;
        $anchos["des"] = 4.75;
        $anchos["u"] = $anchos["c"] = $anchos["ca"] = 0.71;
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
        $no_arreglos = ceil($no_presupuestos / $presupuestosXFila);
        $i_e = 0;
        $total_mejor_opcion = 0;
        $partidas_comprobacion_mejor_opcion = array();

        for ($x = 0; $x < $no_arreglos; $x++) {
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            if (($no_presupuestos - $i_e) > $presupuestosXFila) {
                $inc_ie = $presupuestosXFila;
            } else {
                $inc_ie = abs($no_presupuestos - $i_e);
            }

            $this->SetDrawColor('200', '200', '200');
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(0, 0, 0);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, utf8_decode($this->asignacion->contratoProyectado->presupuestos[$i]->empresa->razon_social), 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, "CONDICIONES GENERALES", 1, 0, 'C', 0);
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
                $this->CellFitScale($anchos["fe"], $heigth, $cotizaciones[$i]->fecha_format, 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["vig"], $heigth, "Vigencia:", 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["vig"], $heigth, $cotizaciones[$i]->complemento ? $cotizaciones[$i]->complemento->vigencia : '-', 1, 0, 'C', 0);
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
                $this->CellFitScale($anchos["ant"], $heigth, $cotizaciones[$i]->complemento ? $cotizaciones[$i]->complemento->anticipo : '-', 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ant"], $heigth, "%", 1, 0, 'C', 1);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["cre"], $heigth, utf8_decode("Crédito:"), 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["cre"], $heigth, $cotizaciones[$i]->complemento ? $cotizaciones[$i]->complemento->dias_credito : '-', 1, 0, 'C', 0);
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
                $this->CellFitScale($anchos["ent"], $heigth,  $cotizaciones[$i]->complemento ? $cotizaciones[$i]->complemento->plazo_entrega : '-', 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ent"], $heigth, utf8_decode("Días"), 1, 0, 'C', 1);
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
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetTextColor(255, 255, 255);
                $this->SetFillColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"] * 4, $heigth, "Cotizado", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"] * 2, $heigth, "Asignado", 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);

            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["pu"], $heigth, "Precio", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Importe Moneda", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "", 1, 0, 'C', 1);
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
                $this->CellFitScale($anchos["pu"], $heigth, "Unitario", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Importe", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Moneda", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Comparable", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Cantidad", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Importe", 1, 0, 'C', 1);
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
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font2);
                $this->CellFitScale($anchos["des"], $heigth, $partida_solicitud->material->descripcion, 1, 0, 'L', 0, '');
                $this->Cell($anchos["c"], $heigth, $partida_solicitud->unidad, 1, 0, 'L', 0, '');
                $this->Cell($anchos["u"], $heigth,number_format($partida_solicitud->cantidad, '2', '.', ','), 1, 0, 'L', 0, '');

                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $partida_cotizacion = CotizacionCompraPartida::where('id_transaccion', '=', $cotizaciones[$i]->id_transaccion)
                        ->where('id_material', '=', $partida_solicitud->id_material)
                        ->where('precio_unitario', '!=', 0)
                        ->first();
                    if($partida_cotizacion) {
                        if (array_key_exists($partida_solicitud->id_material, $mejor_opcion_partida) && $mejor_opcion_partida[$partida_solicitud->id_material] == $cotizaciones[$i]->id_transaccion) {
                            $this->SetFillColor(150, 150, 150);
                            $this->SetTextColor(0, 0, 0);
                        } else {
                            $this->SetFillColor(255, 255, 255);
                            $this->SetTextColor(0, 0, 0);
                        }
                        $this->SetFont('Arial', '', $font_importes);
                        $this->Cell($anchos["pu"], $heigth, $partida_cotizacion ? number_format($partida_cotizacion->precio_compuesto, 4, '.', ',') : '', "T B L R", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, $partida_cotizacion ? number_format($partida_cotizacion->cantidad * $partida_cotizacion->precio_compuesto, 2, '.', ',') : '', "T B L R", 0, "R", 1);
                        $this->CellFitScale($anchos["pu"], $heigth, $partida_cotizacion ? $partida_cotizacion->moneda ? $partida_cotizacion->moneda->nombre : '' : '', "T B L R", 0, "R", 1);
                        $asignacion_partida = AsignacionProveedorPartida::where('id_transaccion_cotizacion', '=', $cotizaciones[$i]->id_transaccion)
                            ->where('id_material', '=', $partida_cotizacion->id_material)
                            ->where('id_asignacion_proveedores', '=', $this->asignacion->id)->first();

                        $this->Cell($anchos["pu"], $heigth, number_format($partida_cotizacion->total_precio_moneda, 2, '.', ','), "B L R T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, $asignacion_partida ? number_format($asignacion_partida->cantidad_asignada,2, '.', ',')  : '-', "B L R T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, $asignacion_partida ? number_format($asignacion_partida->total_precio_moneda, 2, '.', ',') : '-', "B L R T", 0, "R", 1);
                        if($asignacion_partida) {
                            $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['porMoneda'][$partida_cotizacion->id_moneda][$key] = ($asignacion_partida->total_precio_moneda);
                            if(!array_key_exists('subtotal',$datos_partidas_globales[$cotizaciones[$i]->id_transaccion])) {
                                $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal'] = $this->asignacion->subtotalPorCotizacion($cotizaciones[$i]->id_transaccion);
                            }
                        }
                    }else {
                        $this->SetFillColor(200, 200, 200);
                        $this->SetTextColor(200, 200, 200);
                        $this->SetFont('Arial', '', $font_importes);
                        $this->Cell($anchos["pu"], $heigth, '', "L T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, '', "T", 0, "R", 1);
                        $this->CellFitScale($anchos["pu"], $heigth, '', "T", 0, "R", 1);

                        $this->Cell($anchos["pu"], $heigth, '', "T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, '', "T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, '', "R T", 0, "R", 1);
                    }
                }
                $this->Ln();
                $this->SetTextColor(0, 0, 0);
                $this->y_para_obs_partidas = $this->getY();
                $yos_ini = $this->getY();
                $xos_ini = $this->getX();
                $this->SetFont('Arial', 'B', $font2);
                $this->MultiCell($anchos["des"], $heigth,   '', 1, 'L', 0, 1);
                $this->y_para_descripcion_arr[] = $this->GetY();
                $this->y_fin_obs_par_sol_arr[] = $this->GetY();
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

                    if ($partida_cotizacion) {
                        if (array_key_exists($partida_solicitud->id_material, $mejor_opcion_partida) && $mejor_opcion_partida[$partida_solicitud->id_material] == $cotizaciones[$i]->id_transaccion) {
                            $this->SetFillColor(150, 150, 150);
                            $this->SetTextColor(0, 0, 0);
                        } else {
                            $this->SetFillColor(255, 255, 255);
                            $this->SetTextColor(0, 0, 0);
                        }
                        $this->SetFont('Arial', '', $font2);
                        $this->setY($yop_ini);
                        $this->setX($xop_ini);
                        $this->MultiCell($anchos["op"], $heigth, utf8_decode($partida_cotizacion->partida ? $partida_cotizacion->partida->observaciones : '-'), "T R L B", "L", 1);
                        $this->y_para_descripcion_arr[] = $this->GetY();
                        $xop_ini += $anchos["op"];
                    } else {
                        $this->SetFillColor(200, 200, 200);
                        $this->SetTextColor(200, 200, 200);
                        $this->SetFont('Arial', '', $font2);
                        $this->setY($yop_ini);
                        $this->setX($xop_ini);
                        $this->MultiCell($anchos["op"], $heigth, '', "L B R", "L", 1);
                        $this->y_para_descripcion_arr[] = $this->GetY();
                        $xop_ini += $anchos["op"];
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
                $this->Cell($anchos["pu"] * 2, $heigth);
                $this->Cell($anchos["pu"], $heigth, 'PESO (MX)', 1, 0, 'R', 1);
                $this->Cell($anchos["pu"], $heigth, number_format($cotizaciones[$i]->IVA_con_descuento, 2, ".", ","), 1, 0, 'R', 1);
                if(array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales)) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->Cell($anchos["pu"] * 2, $heigth,array_key_exists($cotizaciones[$i]->id_transaccion, $datos_partidas_globales) ? number_format(($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal_con_descuento'] * 0.16), 2, ".", ",") : '-', 1, 0, 'R', 1);
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
                    $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['total']  = $datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal_con_descuento'] + ($datos_partidas_globales[$cotizaciones[$i]->id_transaccion]['subtotal_con_descuento']  * 0.16);
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
            $this->Cell(($anchos["espacio_detalles_globales"]/2), $heigth, "PESO MXP", 1, 0, 'C', 1);
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
            $this->Cell(($anchos["espacio_detalles_globales"]/2), $heigth, "PESO MXP", 1, 0, 'C', 1);
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
            $this->Cell(($anchos["espacio_detalles_globales"]/2), $heigth, "PESO MXP", 1, 0, 'C', 1);
            $this->Cell(($anchos["espacio_detalles_globales"]-0.8), $heigth, number_format($this->asignacion->suma_subtotal_partidas_total, 3, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth,  number_format($this->asignacion->mejor_asignado_total, 3, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-0.8, $heigth,  number_format($this->asignacion->diferencia_total, 3, ".", ","), 1, 0, 'R', 1);
            $this->Ln(2);
            $i_e+=$cotizacinesXFila;
        }
    }

    function create()
    {
        $this->SetMargins(0.7, 1, 0.7);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 4);
        $this->partidas();

        try {
            $this->Output('I', 'Formato - Tabla Comparativa de Asignaciones '.$this->asignacion->numero_folio_format.'-contrato:'.$this->asignacion->contratoProyectado->numero_folio_format.'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }
}
