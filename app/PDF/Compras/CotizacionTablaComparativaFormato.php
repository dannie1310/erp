<?php


namespace App\PDF\Compras;


use App\Facades\Context;
use App\Models\CADECO\Cambio;
use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\Obra;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\TipoCambio;
use App\Utils\ValidacionSistema;
use Ghidev\Fpdf\Rotation;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CotizacionTablaComparativaFormato extends Rotation
{
    protected $obra;
    protected $cotizacion;
    private $encabezado_pdf = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 279;
    const A4_WIDTH = 216;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(CotizacionCompra $cotizacion)
    {
        parent::__construct('L', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());
        $this->cotizacion = $cotizacion;
        $this->encabezado_pdf = utf8_decode($this->obra->facturar);
        $this->createQR();
    }

    function Header()
    {
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
        $this->Cell(3, .5, $this->cotizacion->solicitud->complemento ? $this->cotizacion->solicitud->complemento->folio_compuesto : '', 'R', 0, 'L');

        $this->Ln(.5);
        $this->Cell(19.7);
        $this->Cell(4, .5, 'FOLIO SAO SOLICITUD ', 'LB', 0, 'L');
        $this->Cell(3, .5, $this->cotizacion->solicitud->numero_folio_format, 'RB', 0, 'L');

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
        $datos_partidas = $this->cotizacion->datosComparativos();
        $this->SetFillColor(150, 150, 150);
        $this->SetTextColor(255, 255, 255);
        $no_cotizaciones = count($this->cotizacion->solicitud->cotizaciones);
        $font = 5;
        $font2 = 4;
        $heigth = 0.306;
        $cotizacinesXFila = 4;
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
                if ($datos_partidas['cotizaciones'][$i]['ivg_partida'] == 0) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                } else {
                    $this->SetFillColor(150, 150, 150);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, $datos_partidas['cotizaciones'][$i]['empresa'], 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                if ($datos_partidas['cotizaciones'][$i]['ivg_partida'] == 0) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                } else {
                    $this->SetFillColor(150, 150, 150);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["fe"], $heigth, "Fecha:", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["fe"], $heigth, $datos_partidas['cotizaciones'][$i]['fecha'], 1, 0, 'C', 1);
                $this->CellFitScale($anchos["vig"], $heigth, "Vigencia:", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["vig"], $heigth, $datos_partidas['cotizaciones'][$i]['vigencia'], 1, 0, 'C', 1);
            }

            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                if ($datos_partidas['cotizaciones'][$i]['ivg_partida'] == 0) {
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
                if ($datos_partidas['cotizaciones'][$i]['ivg_partida'] == 0) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                } else {
                    $this->SetFillColor(150, 150, 150);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["ant"], $heigth, $datos_partidas['cotizaciones'][$i]['anticipo'], 1, 0, 'C', 1);
                $this->CellFitScale($anchos["cre"], $heigth, $datos_partidas['cotizaciones'][$i]['dias_credito'], 1, 0, 'C', 1);
                $this->CellFitScale($anchos["ent"], $heigth, $datos_partidas['cotizaciones'][$i]['plazo_entrega'], 1, 0, 'C', 1);
                $this->CellFitScale($anchos["ivg"], $heigth, number_format($datos_partidas['cotizaciones'][$i]['ivg_partida'] * 100, '2', '.', ',') . '%', 1, 0, 'C', 1);
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
            foreach ($datos_partidas['partidas'] as $key => $partida) {
                $ki = -1;
                asort($this->y_para_descripcion_arr);
                $this->y_para_descripcion = array_pop($this->y_para_descripcion_arr);
                $this->SetY($this->y_para_descripcion);
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font2);
                $this->CellFitScale($anchos["des"], $heigth, utf8_decode($partida['material']) . ' ', 1, 0, 'L', 0, '');
                $this->Cell($anchos["c"], $heigth, $partida['unidad'], 1, 0, 'L', 0, '');
                $this->Cell($anchos["u"], $heigth, number_format($partida['cantidad'], '2', '.', ','), 1, 0, 'L', 0, '');
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $ki = 0;
                    if (array_key_exists($i, $partida['cotizaciones']) && $partida['cotizaciones'][$i]['precio_unitario'] > 0) {
                            $this->SetFillColor(255, 255, 255);
                            $this->SetTextColor(0, 0, 0);
                    }else {
                        $this->SetFillColor(200, 200, 200);
                        $this->SetTextColor(200, 200, 200);
                    }
                    $ki = array_key_exists($i, $partida['cotizaciones']) && $partida['cotizaciones'][$i]['calculo_ki'] ? $partida['cotizaciones'][$i]['calculo_ki'] : 0;
                    $this->SetFont('Arial', '', $font2);
                    $this->Cell($anchos["pu"], $heigth, array_key_exists($i, $partida['cotizaciones']) && $partida['cotizaciones'][$i]['precio_con_descuento'] ?  number_format($partida['cotizaciones'][$i]['precio_con_descuento'], 3, '.', ',') : '', "T B L", 0, "R", 1);
                    $this->CellFitScale($anchos["d"], $heigth, $ki == 0 ? '-' : number_format($ki, '4', '.', ','), "T B L", 0, "R", 1);
                    $this->Cell($anchos["it"], $heigth, array_key_exists($i, $partida['cotizaciones']) && $partida['cotizaciones'][$i]['precio_total_compuesto'] ? number_format($partida['cotizaciones'][$i]['precio_total_compuesto'], 2, '.', ',') : '', "T B L", 0, "R", 1);
                    $this->CellFitScale($anchos["m"], $heigth, array_key_exists($i, $partida['cotizaciones']) && $partida['cotizaciones'][$i]['precio_unitario'] > 0 ? $partida['cotizaciones'][$i]['tipo_cambio_descripcion'] : '-', "T B L", 0, "R", 1);
                    $this->Cell($anchos["ic"], $heigth,array_key_exists($i, $partida['cotizaciones']) && $partida['cotizaciones'][$i]['precio_total_moneda'] ? number_format($partida['cotizaciones'][$i]['precio_total_moneda'], 2, '.', ',') : '', "B L R T", 0, "R", 1);
                }

                $this->Ln();
                $this->SetTextColor(0, 0, 0);
                $this->y_para_obs_partidas = $this->getY();
                $xos_ini = $this->getX();

                $this->MultiCell($anchos["des"], $heigth, $partida['observaciones'], 1, 'L', 0, 1);
                $this->y_para_descripcion_arr[] = $this->GetY();
                $this->y_fin_obs_par_sol_arr[] = $this->GetY();
                $xos_ini += $anchos["des"];
                $this->setY($this->y_para_obs_partidas);
                $this->setX($xos_ini);
                $this->CellFitScale($anchos["aesp"], $heigth, 'Observaciones de Partida:', 'B', 0, 'R', 0);
                $yop_ini = $this->getY();
                $xop_ini = $this->getX();

                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetFont('Arial', '', $font2);
                    $this->setY($yop_ini);
                    $this->setX($xop_ini);
                    $this->MultiCell($anchos["op"], $heigth, array_key_exists($i, $partida['cotizaciones']) && (float)$partida['cotizaciones'][$i]['precio_unitario'] > 0 ? utf8_decode($partida['cotizaciones'][$i]['observaciones']) : '', "B L R T", "L", 1);
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
                $this->Cell($anchos["dg"], $heigth,array_key_exists($i, $datos_partidas['cotizaciones']) ? $datos_partidas['cotizaciones'][$i]['descuento_global'] : '-', 1, 0, 'R', 1);
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
                $this->Cell($anchos["dg"], $heigth,array_key_exists($i, $datos_partidas['cotizaciones']) ? number_format($datos_partidas['cotizaciones'][$i]['suma_subtotal_partidas'], 2, '.', ',') : '', 1, 0, 'R', 1);
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
                $this->Cell($anchos["dg"], $heigth,array_key_exists($i, $datos_partidas['cotizaciones']) ? number_format($datos_partidas['cotizaciones'][$i]['iva_partidas'], 2, '.', ',') : '', 1, 0, 'R', 1);
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
                $this->Cell($anchos["dg"], $heigth, array_key_exists($i, $datos_partidas['cotizaciones']) ? number_format($datos_partidas['cotizaciones'][$i]['total_partidas'], 2, '.', ',') : '', 1, 0, 'R', 1);
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
                $this->Cell($anchos["dg"], $heigth,array_key_exists($i, $datos_partidas['cotizaciones']) ?  $datos_partidas['cotizaciones'][$i]['tipo_moneda'] : '', 1, 0, 'R', 1);
            }

            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Subtotal Importe Peso (MXP):", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);

                $this->Cell($anchos["ar"] + $anchos["it"], $heigth);
                $this->Cell($anchos["dg"], $heigth, array_key_exists($i, $datos_partidas['cotizaciones']) ? $datos_partidas['cotizaciones'][$i]['subtotal_peso'] : '', 1, 0, 'R', 1);
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
                $this->Cell($anchos["ar"], $heigth,array_key_exists($i, $datos_partidas['cotizaciones']) ? $datos_partidas['cotizaciones'][$i]['suma_total_dolar'] : '', 1, 0, 'R', 1);
                $this->Cell($anchos["tc"], $heigth,array_key_exists($i, $datos_partidas['cotizaciones']) ? $datos_partidas['cotizaciones'][$i]['tc_usd'] : '', 1, 0, 'R', 1);
                $this->Cell($anchos["dg"], $heigth,array_key_exists($i, $datos_partidas['cotizaciones']) ? $datos_partidas['cotizaciones'][$i]['subtotal_dolar'] : '', 1, 0, 'R', 1);
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
                $this->Cell($anchos["ar"], $heigth, array_key_exists($i, $datos_partidas['cotizaciones']) ? $datos_partidas['cotizaciones'][$i]['suma_total_euro'] : '', 1, 0, 'R', 1);
                $this->Cell($anchos["tc"], $heigth, array_key_exists($i, $datos_partidas['cotizaciones']) ? $datos_partidas['cotizaciones'][$i]['tc_eur'] : '', 1, 0, 'R', 1);
                $this->Cell($anchos["dg"], $heigth, array_key_exists($i, $datos_partidas['cotizaciones']) ? $datos_partidas['cotizaciones'][$i]['subtotal_euro'] : '', 1, 0, 'R', 1);

            }
            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "Subtotal Importe Libra:", 1, 0, "R", 1);

            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["ar"], $heigth, array_key_exists($i, $datos_partidas['cotizaciones']) ? $datos_partidas['cotizaciones'][$i]['suma_total_libra'] : '', 1, 0, 'R', 1);
                $this->Cell($anchos["tc"], $heigth, array_key_exists($i, $datos_partidas['cotizaciones']) ? $datos_partidas['cotizaciones'][$i]['tc_libra'] : '', 1, 0, 'R', 1);
                $this->Cell($anchos["dg"], $heigth, array_key_exists($i, $datos_partidas['cotizaciones']) ? $datos_partidas['cotizaciones'][$i]['subtotal_libra'] : '', 1, 0, 'R', 1);

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
                $this->MultiCell($anchos["og"], $heigth, array_key_exists($i, $datos_partidas['cotizaciones']) ? utf8_decode($datos_partidas['cotizaciones'][$i]['observaciones']) : '-', 1, 'J', false);
                $this->Ln();
                $this->Ln();
                $this->Ln();
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

    function Footer() {
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'BI', 5.5);
        $this->SetY(-4);

        if (Context::getDatabase() == "SAO1814" && Context::getIdObra() == 41) {
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);
            $this->Cell(6.6, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(6.6, .4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(12.8, .4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
            $this->Ln();
            $this->Cell(6.6, .4, 'Jefe Compras', 'TRLB', 0, 'C', 1);
            $this->Cell(6.6, .4, 'Gerente Administrativo', 'TRLB', 0, 'C', 1);
            $this->Cell(6.4, .4, 'Control de Costos', 'TRLB', 0, 'C', 1);
            $this->Cell(6.4, .4, 'Director de proyecto', 'TRLB', 0, 'C', 1);
            $this->Ln();

            $this->Cell(6.6, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(6.6, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(6.4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(6.4, 1.2, '', 'TRLB', 0, 'C');
            $this->Ln();
            $this->Cell(6.6, .4, 'LIC. BRENDA ELIZABETH ESQUIVEL ESPINOZA', 'TRLB', 0, 'C', 1);
            $this->Cell(6.6, .4, 'C.P. ROGELIO HERNANDEZ BELTRAN', 'TRLB', 0, 'C', 1);
            $this->Cell(6.4, .4, 'ING. JUAN CARLOS MARTINEZ ANTUNA', 'TRLB', 0, 'C', 1);
            $this->Cell(6.4, .4, 'ING. PEDRO ALFONSO MIRANDA REYES', 'TRLB', 0, 'C', 1);
            $this->Ln();
            $this->Ln();
        } else {
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
            $this->Cell(11.5, .4, utf8_decode('Formato generado desde el sistema de compras. Fecha de registro: ' . date("d-m-Y", strtotime($this->cotizacion->fecha))).' Fecha de consulta: '.date("d-m-Y H:i:s"), 0, 0, 'L');
            $this->Cell(15, .4, (utf8_decode('Página ')) . $this->PageNo() . '/{nb}', 0, 0, 'R');
        }
    }

    public function createQR()
    {
        $verifica = new ValidacionSistema();
        $datos_qr2['titulo'] = "Formato Cotización Tabla Comparativa Compra_".date("d-m-Y")."_solicitud:".($this->cotizacion->solicitud->complemento ? $this->cotizacion->solicitud->complemento->folio_compuesto : '')."_".$this->cotizacion->solicitud->numero_folio_format."_cotizacion:".$this->cotizacion->numero_folio_format;
        $datos_qr2["base"] = Context::getDatabase();
        $datos_qr2["obra"] = $this->obra->nombre;
        $datos_qr2["tabla"] = "transacciones";
        $datos_qr2["campo_id"] = "id_transaccion";
        $datos_qr2["id"] = $this->cotizacion->solicitud->id_transaccion;
        $cadena_json_id = json_encode($datos_qr2);

        $firmada = $verifica->encripta($cadena_json_id);
        $this->cadena_qr = "http://".$_SERVER['SERVER_NAME'].":". $_SERVER['SERVER_PORT']."/api/compras/solicitud-compra/leerQR?data=" . urlencode($firmada);
        $this->cadena = $firmada;

        $this->dato = $verifica->encripta($cadena_json_id);

        $this->qr_name = 'qrcode_'. mt_rand() .'.png';
    }

    function create()
    {
        $this->SetMargins(0.7, 1, 0.7);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 5);
        $this->partidas();

        try {
            $this->Output('I', 'Formato - Tabla Comparativa Cotizacion '.$this->cotizacion->numero_folio_format.'-solicitud:'.$this->cotizacion->solicitud->numero_folio_format.'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }
}
