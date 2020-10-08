<?php


namespace App\PDF\Contratos;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\PresupuestoContratista;
use App\Utils\ValidacionSistema;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PresupuestoContratistaTablaComparativaFormato extends Rotation
{
    protected $obra;
    protected $contratista;
    private $encabezado_pdf = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 279;
    const A4_WIDTH = 216;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(PresupuestoContratista $contratista)
    {
        parent::__construct('L', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());
        $this->contratista = $contratista;
        $this->encabezado_pdf = utf8_decode($this->obra->facturar);
        $this->createQR();
    }

    function Header()
    {
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(17.5, 1, 'TABLA COMPARATIVA DE SUBCONTRATISTAS', 0, 0, 'C', 0);
        $this->SetFont('Arial', 'B', 7);
        $this->SetX(20);
        $this->Cell(4, .5, 'FOLIO CONTRATO:', 'L T', 0, 'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(3, .5,$this->contratista->contratoProyectado->numero_folio_format, 'R T', 0, 'L');
        $this->Ln(.5);
        $this->Cell(19.102);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(4, .5, 'FECHA DE CONSULTA:', 'LB', 0, 'L');
        $this->Cell(3, .5, date("d-m-Y h:m:i"), 'RB', 0, 'L');

        $this->Ln(.2);
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
        $datos_partidas = $this->contratista->datosComparativos();
        $this->SetFillColor(150, 150, 150);
        $this->SetTextColor(255, 255, 255);
        $no_cotizaciones = count($this->contratista->contratoProyectado->presupuestos);
        $font = 5;
        $font2 = 4;
        $heigth = 0.306;
        $cotizacinesXFila = 3;
        $anchos["des"] = 4.7;
        $anchos["u"] = $anchos["c"] = 0.77;
        $anchos["aesp"] = $anchos["u"] + $anchos["c"];
        $anchos["espacio_detalles_globales"] = ($anchos["aesp"] + $anchos["des"]) / 2;

        $anchos["fe"] = $anchos["ar"] = 2;
        $anchos["dg"] = 2.7;
        $anchos["d"] = $anchos["ant"] = $anchos["pu"] = $anchos["porc"] = 1;
        $anchos["tc"] = $anchos["it"] = 1.05;
        $anchos["m"] = 1;
        $anchos["ic"] = 1;
        $anchos["dias"] = 1;
        $anchos["vig"] = $anchos["cre"] = $anchos['importe'] = 1.1;
        $anchos["op"] = $anchos["og"] = $anchos["p"] = 6.2;
        $anchos["desc_g"] = 4.1;

        $no_arreglos = ceil($no_cotizaciones / $cotizacinesXFila);
        $i_e = 0;
        $this->Ln();
        for ($x = 0; $x < $no_arreglos; $x++) {
            $this->SetDrawColor('200', '200', '200');
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
                $this->CellFitScale($anchos["p"], $heigth, $datos_partidas['presupuestos'][$i]['empresa'], 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, 'CONDICIONES GENERALES', 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', '', $font);
                $this->CellFitScale($anchos["fe"], $heigth, utf8_decode("Fecha Cotización:"), 1, 0, 'C', 1);
                $this->SetTextColor(130, 130, 130);
                $this->SetFillColor(255, 255, 255);
                $this->CellFitScale($anchos["dias"], $heigth, $datos_partidas['presupuestos'][$i]['fecha'], 1, 0, 'C', 1);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["vig"], $heigth, "Vigencia:", 1, 0, 'C', 1);
                $this->SetTextColor(130, 130, 130);
                $this->SetFillColor(255, 255, 255);
                $this->CellFitScale($anchos["vig"], $heigth, $datos_partidas['presupuestos'][$i]['vigencia'], 1, 0, 'C', 1);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["dias"], $heigth, utf8_decode("Días"), 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(150, 150, 150);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', '', $font);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ant"], $heigth, "Anticipo", 1, 0, 'C', 1);
                $this->SetTextColor(130, 130, 130);
                $this->SetFillColor(255, 255, 255);
                $this->CellFitScale($anchos["ant"], $heigth, $datos_partidas['presupuestos'][$i]['anticipo'], 1, 0, 'C', 1);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["dias"], $heigth, "%", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["cre"], $heigth, utf8_decode("Crédito"), 1, 0, 'C', 1);
                $this->SetTextColor(130, 130, 130);
                $this->SetFillColor(255, 255, 255);
                $this->CellFitScale($anchos["cre"], $heigth, $datos_partidas['presupuestos'][$i]['dias_credito'], 1, 0, 'C', 1);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["dias"], $heigth, utf8_decode("Días"), 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(0, 0, 0);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, 'Cotizado', 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["pu"], $heigth, "Precio", 1, 0, 'C', 1);
                $this->Cell($anchos["porc"], $heigth, "%", 1, 0, 'C', 1);
                $this->Cell($anchos["pu"], $heigth, "Precio", 1, 0, 'C', 1);
                $this->Cell($anchos['importe'], $heigth, "", 1, 0, 'C', 1);
                $this->Cell($anchos['importe'], $heigth, "", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["dias"], $heigth, "Importe Moneda", 1, 0, 'C', 1);
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
                $this->CellFitScale($anchos["pu"], $heigth, "Unitario AD", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["porc"], $heigth, "Descto.", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["pu"], $heigth, "Unitario", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["importe"], $heigth, "Importe", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["importe"], $heigth, "Moneda", 1, 0, 'C', 1);
                $this->CellFitScale($anchos["dias"], $heigth, "Comparable", 1, 0, 'C', 1);
            }
            $this->Ln();
            $this->y_para_descripcion = $this->GetY();
            $this->y_para_descripcion_arr[] = $this->GetY();
            foreach ($datos_partidas['partidas'] as $key => $partida) {
                asort($this->y_para_descripcion_arr);
                $this->y_para_descripcion = array_pop($this->y_para_descripcion_arr);
                $this->SetY($this->y_para_descripcion);
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font2);
                $this->CellFitScale($anchos["des"], $heigth, utf8_decode($partida['concepto']) . ' ', 1, 0, 'L', 0, '');
                $this->Cell($anchos["c"], $heigth, $partida['unidad'], 1, 0, 'L', 0, '');
                $this->Cell($anchos["u"], $heigth, number_format($partida['cantidad_presupuestada'], '2', '.', ','), 1, 0, 'L', 0, '');
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    if (array_key_exists('presupuestos', $partida) && array_key_exists($i, $partida['presupuestos']) && $partida['presupuestos'][$i]['precio_unitario'] != 0) {
                        $this->SetFillColor(150, 150, 150);
                        $this->SetTextColor(0, 0, 0);
                        $this->SetFont('Arial', '', $font2);
                        $this->Cell($anchos["pu"], $heigth, number_format($partida['presupuestos'][$i]['precio_unitario'], 2, '.', ','), "T B L", 0, "R", 1);
                        $this->CellFitScale($anchos["porc"], $heigth, $partida['presupuestos'][$i]['descuento_partida'] > 0 ? $partida['presupuestos'][$i]['descuento_partida'] : '-', "T B L", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, number_format($partida['presupuestos'][$i]['precio_unitario'], 2, '.', ','), "T B L", 0, "R", 1);
                        $this->CellFitScale($anchos["importe"], $heigth, number_format($partida['presupuestos'][$i]['precio_total'], 2, '.', ','), "T B L", 0, "R", 1);
                        $this->Cell($anchos["importe"], $heigth, $partida['presupuestos'][$i]['tipo_cambio_descripcion'], "B L R T", 0, "R", 1);
                        $this->Cell($anchos["dias"], $heigth, number_format($partida['presupuestos'][$i]['precio_total_moneda'], 2, '.', ','), "B L R T", 0, "R", 1);
                    }
                }

                $this->Ln();
                $this->SetTextColor(0, 0, 0);
                $this->y_para_obs_partidas = $this->getY();
                $xos_ini = $this->getX();

                $this->MultiCell($anchos["des"], $heigth, $partida['observaciones'], 0, 'L', 0, 1);
                $this->y_para_descripcion_arr[] = $this->GetY();
                $this->y_fin_obs_par_sol_arr[] = $this->GetY();
                $xos_ini += $anchos["des"];
                $this->setY($this->y_para_obs_partidas);
                $this->setX($xos_ini);
                $this->CellFitScale($anchos["aesp"], $heigth, 'Observaciones de Partida:', '', 0, 'R', 0);
                $yop_ini = $this->getY();
                $xop_ini = $this->getX();

                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    if (array_key_exists('presupuestos', $partida) && array_key_exists($i, $partida['presupuestos']) && $partida['presupuestos'][$i]['precio_unitario'] != 0 && $partida['presupuestos'][$i]['observaciones']) {
                        $this->SetFillColor(255, 255, 255);
                        $this->SetTextColor(0, 0, 0);
                        $this->SetFont('Arial', '', $font2);
                        $this->setY($yop_ini);
                        $this->setX($xop_ini);
                        $this->MultiCell($anchos["op"], $heigth, utf8_decode($partida['presupuestos'][$i]['observaciones']), "B L R T", "L", 1);
                        $this->y_para_descripcion_arr[] = $this->GetY();
                        $xop_ini += $anchos["op"];
                    }
                }
                $this->Ln();
            }
            asort($this->y_fin_obs_par_sol_arr);
            $this->y_fin_obs_par_sol = array_pop($this->y_fin_obs_par_sol_arr);
            $this->SetY($this->y_fin_obs_par_sol);
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(0, 0, 0);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, 'Cotizado', 1, 0, 'C', 1);
            }
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
                $this->Cell($anchos["desc_g"], $heigth,'', 0, 0, 'R', 1);
                $this->Cell($anchos["importe"], $heigth,array_key_exists($i, $datos_partidas['presupuestos']) ? $datos_partidas['presupuestos'][$i]['tipo_moneda'] : '-', 0, 0, 'C', 1);
                $this->Cell($anchos["dias"], $heigth,array_key_exists($i, $datos_partidas['presupuestos']) && $datos_partidas['presupuestos'][$i]['suma_subtotal_partidas'] > 0 ? number_format($datos_partidas['presupuestos'][$i]['suma_subtotal_partidas'], 2, '.',',') : '-', 0, 0, 'R', 1);
            }
            $this->Ln(0.4);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "% Descuento Global", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["desc_g"], $heigth,array_key_exists($i, $datos_partidas['presupuestos']) ? $datos_partidas['presupuestos'][$i]['descuento_global'] : '-', 1, 0, 'R', 1);
                $this->Cell($anchos["importe"], $heigth,'%', 1, 0, 'C', 1);
                $this->Cell($anchos["dias"], $heigth,array_key_exists($i, $datos_partidas['presupuestos']) && $datos_partidas['presupuestos'][$i]['suma_subtotal_partidas'] > 0 ? number_format($datos_partidas['presupuestos'][$i]['suma_subtotal_partidas'], 2, '.',',') : '-', 1, 0, 'R', 1);
            }
            $this->Ln(0.4);
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, utf8_decode("Subtotal:"), 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["desc_g"], $heigth);
                $this->Cell($anchos["importe"], $heigth,array_key_exists($i, $datos_partidas['presupuestos']) ? $datos_partidas['presupuestos'][$i]['tipo_moneda'] : '-', 1, 0, 'R', 1);
                $this->Cell($anchos["dias"], $heigth,array_key_exists($i, $datos_partidas['presupuestos']) && $datos_partidas['presupuestos'][$i]['suma_subtotal_partidas'] > 0 ? number_format($datos_partidas['presupuestos'][$i]['suma_subtotal_partidas'], 2, '.', ',') : '-', 1, 0, 'R', 1);
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
                $this->Cell($anchos["desc_g"], $heigth);
                $this->Cell($anchos["importe"], $heigth,array_key_exists($i, $datos_partidas['presupuestos']) ? $datos_partidas['presupuestos'][$i]['tipo_moneda'] : '-', 1, 0, 'R', 1);
                $this->Cell($anchos["dias"], $heigth,array_key_exists($i, $datos_partidas['presupuestos']) && $datos_partidas['presupuestos'][$i]['iva_partidas'] > 0 ? number_format($datos_partidas['presupuestos'][$i]['iva_partidas'], 2, '.', ',') : '-', 1, 0, 'R', 1);
            }
            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "TOTAL:", 1, 0, "R", 1);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->Cell($anchos["desc_g"], $heigth);
                $this->Cell($anchos["importe"], $heigth,array_key_exists($i, $datos_partidas['presupuestos']) ? $datos_partidas['presupuestos'][$i]['tipo_moneda'] : '-', 1, 0, 'R', 1);
                $this->Cell($anchos["dias"], $heigth,array_key_exists($i, $datos_partidas['presupuestos']) && $datos_partidas['presupuestos'][$i]['total_partidas'] > 0 ? number_format($datos_partidas['presupuestos'][$i]['total_partidas'], 2, '.', ',') : '-', 1, 0, 'R', 1);
            }
            $this->Ln();
            $this->Ln();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, 'Observaciones Globales', 1, 0, 'C', 1);
            }
            $this->Ln();
            $y_ini = $this->getY();
            $x_ini = $this->getX();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', 'B', $font);
                $this->setY($y_ini);
                $this->setX($x_ini);
                $this->Cell($anchos["des"]+$anchos["u"]+$anchos["c"], $heigth);
                $this->MultiCell($anchos["og"], $heigth, array_key_exists($i, $datos_partidas['presupuestos']) ? utf8_decode($datos_partidas['presupuestos'][$i]['observaciones']) : '', 1, 'J', false);
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

    function Footer()
    {
        /*if (!App::environment('production')) {
            $this->SetFont('Arial','B',90);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(5,15,utf8_decode("MUESTRA"),45);
            $this->RotatedText(10,20,utf8_decode("SIN VALOR"),45);
            $this->SetTextColor('0,0,0');
        }*/
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'BI', 5.5);
        $this->SetY(-5.4);
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
        $this->image("data:image/png;base64," . base64_encode(QrCode::format('png')->generate($this->cadena_qr)), $this->GetX(), $this->GetY(), 3.5, 3.5, 'PNG');
        $this->SetY(-3.6);
        $this->setX(4.5);
        $this->SetFont('Arial', '', 4.5);
        $this->MultiCell(22.5, .3, utf8_decode($this->cadena), 0, 'L');
        $this->Ln(.2);
        $this->SetY(16.5);
        $this->setX(4.5);

        $this->SetFont('Arial', 'B', 6.5);
        $this->SetTextColor('100,100,100');
        $this->SetY(20);
        $this->Cell(26.5, .4, utf8_decode('Sistema de Administración de Obra'), 0, 0, 'R');
        $this->SetY(-1.2);
        $this->SetFont('Arial', 'BI', 6.5);
        $this->SetTextColor('0,0,0');
        $this->SetX(4.5);
        $this->Cell(11.5, .4, utf8_decode('Formato generado desde el sistema de contratos. Fecha de registro: ' . date("d-m-Y", strtotime($this->contratista->fecha))) . ' Fecha de consulta: ' . date("d-m-Y H:i:s"), 0, 0, 'L');
        $this->Cell(11.5, .4, (utf8_decode('Página ')) . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    public function createQR()
    {
        $verifica = new ValidacionSistema();
        $datos_qr2['titulo'] = "Formato TABLA COMPARATIVA DE CONTRATISTAS_".date("d-m-Y")."_CONTRATO:".$this->contratista->contratoProyectado->numero_folio_format."_presupuesto:".$this->contratista->numero_folio_format;
        $datos_qr2["base"] = Context::getDatabase();
        $datos_qr2["obra"] = $this->obra->nombre;
        $datos_qr2["tabla"] = "transacciones";
        $datos_qr2["campo_id"] = "id_transaccion";
        $datos_qr2["id"] = $this->contratista->contratoProyectado->id_transaccion;
        $cadena_json_id = json_encode($datos_qr2);

        $firmada = $verifica->encripta($cadena_json_id);
        $this->cadena_qr = "http://".$_SERVER['SERVER_NAME'].":". $_SERVER['SERVER_PORT']."/api/compras/solicitud-compra/leerQR?data=" . urlencode($firmada);
        $this->cadena = $firmada;

        $this->dato = $verifica->encripta($cadena_json_id);

        $this->qr_name = 'qrcode_'. mt_rand() .'.png';
    }

    function create()
    {
        $this->SetMargins(0.9, 1.2, 0.9);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 5.6);
        $this->partidas();

        try {
            $this->Output('I', 'Formato - Tabla Comparativa Presupuesto Subcontratista '.$this->contratista->numero_folio_format.'-Contrato Proyectado:'.$this->contratista->contratoProyectado->numero_folio_format.'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }
}
