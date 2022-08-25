<?php


namespace App\PDF\Contratos;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Subcontratos\AsignacionContratista;
use App\Utils\ValidacionSistema;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        parent::__construct('L', 'cm', array(21.59,32));
        $this->obra = Obra::find(Context::getIdObra());
        $this->asignacion = $asignacion;
        $this->encabezado_pdf = utf8_decode($this->obra->facturar);
        $this->createQR();
    }

    private function createQR()
    {
        $verifica = new ValidacionSistema();
        $datos_qr2['titulo'] = "Formato TABLA DE ASIGNACIÓN DE CONTRATISTAS_".date("d-m-Y")."_asignación: #".$this->asignacion->id_asignacion."_".($this->asignacion->contratoProyectado->numero_folio_format);
        $datos_qr2["base"] = Context::getDatabase();
        $datos_qr2["obra"] = $this->obra->nombre;
        $datos_qr2["tabla"] = "Subcontratos.asignaciones";
        $datos_qr2["campo_id"] = "id_asignacion";
        $datos_qr2["id"] = $this->asignacion->id_asignacion;
        $cadena_json_id = json_encode($datos_qr2);

        $firmada = $verifica->encripta($cadena_json_id);
        $this->cadena_qr = "http://".$_SERVER['SERVER_NAME'].":". $_SERVER['SERVER_PORT']."/api/compras/solicitud-compra/leerQR?data=" . urlencode($firmada);
        $this->cadena = $firmada;

        $this->dato = $verifica->encripta($cadena_json_id);

        $this->qr_name = 'qrcode_'. mt_rand() .'.png';
    }

    function Header()
    {
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(17.5, 1.4, utf8_decode('TABLA DE ASIGNACIÓN DE PROVEEDORES'), 0, 0, 'R', 0);
        $this->SetFont('Arial', 'B', 7);
        $this->SetX(24.2);
        $this->Cell(4, .5, 'FOLIO:', 'L T', 0, 'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(3, .5, $this->asignacion->numero_folio_format, 'R T', 0, 'L');
        $this->Ln(.5);
        $this->SetX(24.2);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(4, .5, 'FECHA:  ', 'L B', 0, 'L');
        $this->Cell(3, .5, $this->asignacion->fecha_registro_format, 'R B', 0, 'L');

        if($this->asignacion->asignacion_parcial){
            $this->Ln(.5);
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(23.3, 0.5, '(PARCIAL)', '', 0, 'C');
            $this->SetFont('Arial', '', 6);
            $this->Ln(-.5);
        }

        $this->SetFont('Arial', '', 6);
        $this->Ln(.5);
        $this->SetX(24.2);
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

        $datos = $this->asignacion->datosComparativos();
        $no_presupuestos = count($datos["presupuestos"]);
        $font = 5;
        $font2 = 4;
        $font_importes = 5;
        $heigth = 0.3;
        $presupuestosXFila = 3;
        $anchos["des"] = 4.5;
        $anchos["u"] = $anchos["ca"] = 0.5;
        $anchos["c"] = $anchos["ca"] = 0.92;
        $anchos["aesp"] = $anchos["des"] + $anchos["u"] + $anchos["c"];
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

        for ($x = 0; $x < $no_arreglos; $x++) {
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            if (($no_presupuestos - $i_e) > $presupuestosXFila) {
                $inc_ie = $presupuestosXFila;
            } else {
                $inc_ie = abs($no_presupuestos - $i_e);
            }

            $this->SetDrawColor('200', '200', '200');
            $x_head = $this->GetX();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetX($x_head);
                $this->SetFillColor(0, 0, 0);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, utf8_decode($datos['presupuestos'][$i]['empresa']), 1, 0, 'C', 1);
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
            $x_head = $this->GetX();
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetX($x_head);
                $this->SetFillColor(100, 100, 100);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', '', $font);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["fe"] + $anchos["fe"], $heigth, utf8_decode("Fecha Cotización:"), 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["fe"], $heigth, $datos['presupuestos'][$i]['fecha'], 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["vig"], $heigth, "Vigencia:", 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["vig"], $heigth, $datos['presupuestos'][$i]['vigencia'], 1, 0, 'C', 0);
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
                $this->CellFitScale($anchos["ant"], $heigth, $datos['presupuestos'][$i]['anticipo'], 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["ant"], $heigth, "%", 1, 0, 'C', 1);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["cre"], $heigth, utf8_decode("Crédito:"), 1, 0, 'C', 1);
                $this->SetTextColor(100, 100, 100);
                $this->CellFitScale($anchos["cre"], $heigth, $datos['presupuestos'][$i]['dias_credito'], 1, 0, 'C', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["cre"], $heigth, utf8_decode("Días"), 1, 0, 'C', 1);
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
            $this->Cell($anchos["des"], $heigth, "Destino", 1, 0, 'C', 1);
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
            $mejor_opcion_partida = $this->asignacion->mejores_opciones_por_concepto;

            foreach ($datos['partidas'] as $key => $partida) {
                asort($this->y_para_descripcion_arr);
                $this->y_para_descripcion = array_pop($this->y_para_descripcion_arr);
                $this->SetY($this->y_para_descripcion);
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $this->SetDrawColor('200', '200', '200');
                $this->SetFont('Arial', 'B', $font2);
                $this->SetY($this->y_para_descripcion);
                $this->MultiCell($anchos["des"], $heigth, utf8_decode($partida['descripcion']), 1, 'L');
                $this->y_para_descripcion_arr[] = $this->GetY();
                $this->y_fin_obs_par_sol_arr[] = $this->GetY();
                $this->SetY($this->y_para_descripcion);
                $this->SetX($anchos["des"] + 0.7);
                $this->MultiCell($anchos["des"], $heigth, utf8_decode($partida['destino']), 1, 'L');
                $this->y_para_descripcion_arr[] = $this->GetY();
                $this->y_fin_obs_par_sol_arr[] = $this->GetY();
                $this->SetY($this->y_para_descripcion);
                $this->SetX(($anchos["des"] * 2) + 0.7);
                $this->SetFont('Arial', 'B', $font2-0.5);
                $this->CellFitScale($anchos["u"], $heigth, $partida['unidad'], 1, 0, 'L', 0, '');
                $this->SetFont('Arial', 'B', $font2);
                $this->CellFitScale($anchos["c"], $heigth, number_format($partida['cantidad'], '2', '.', ','), 1, 0, 'R', 0, '');
                $xca_ini = $this->getX();

                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    if (array_key_exists($key, $mejor_opcion_partida) && !is_null($mejor_opcion_partida[$key]) && $mejor_opcion_partida[$key] == $partida['presupuestos'][$i]['id_transaccion']) {
                        $this->SetFillColor(150, 150, 150);
                        $this->SetTextColor(0, 0, 0);
                    } else {
                        $this->SetFillColor(255, 255, 255);
                        $this->SetTextColor(0, 0, 0);
                    }
                    $this->SetFont('Arial', '', $font_importes);
                    $this->SetDrawColor('200', '200', '200');
                    $this->SetX($xca_ini);
                    $x_prov = $this->GetX();
                    if (array_key_exists('presupuestos', $partida)) {
                        $this->Cell($anchos["pu"], $heigth, array_key_exists($i, $partida['presupuestos']) && $partida['presupuestos'][$i]['precio_unitario_simple'] ? number_format($partida['presupuestos'][$i]['precio_unitario_simple'], 2, '.', ',') : '', "T B R", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, array_key_exists($i, $partida['presupuestos']) && $partida['presupuestos'][$i]['precio_total_compuesto'] ? number_format($partida['presupuestos'][$i]['importe_simple'], 2, '.', ',') : '', "T B L R", 0, "R", 1);
                        $this->CellFitScale($anchos["pu"], $heigth, array_key_exists($i, $partida['presupuestos']) && $partida['presupuestos'][$i]['tipo_cambio_descripcion'] ? $partida['presupuestos'][$i]['tipo_cambio_descripcion'] : '', "T B L R", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, array_key_exists($i, $partida['presupuestos']) && $partida['presupuestos'][$i]['precio_unitario_compuesto'] ? number_format($partida['presupuestos'][$i]['importe_compuesto'], 2, '.', ',') : '', "B L R T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, array_key_exists($i, $partida['presupuestos']) ? $partida['presupuestos'][$i]['cantidad_asignada'] : '-', "B L R T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, array_key_exists($i, $partida['presupuestos']) ? $partida['presupuestos'][$i]['importe_asignado'] : '-', "B L R T", 0, "R", 1);
                        if(array_key_exists($i, $partida['presupuestos']) && $partida['presupuestos'][$i]['cantidad_asignada'] > 0){
                            $this->SetX($x_prov);
                            $this->SetDrawColor('20', '20', '20');
                            $this->Cell($anchos["pu"]*6, $heigth, '', "L R T", 0, "R", 0);
                        }
                    }else {
                        $this->Cell($anchos["pu"], $heigth,'', "T B L R", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, '', "T B L R", 0, "R", 1);
                        $this->CellFitScale($anchos["pu"], $heigth,'', "T B L R", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, '', "B L R T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, '-', "B L R T", 0, "R", 1);
                        $this->Cell($anchos["pu"], $heigth, '-', "B L R T", 0, "R", 1);
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
                asort($this->y_fin_obs_par_sol_arr);
                $this->y_fin_obs_par_sol = array_pop($this->y_fin_obs_par_sol_arr);
                $this->SetY($this->y_fin_obs_par_sol);
                $this->MultiCell($anchos["des"] * 2, $heigth, '', 1, 'L', 0, 1);
                $this->y_para_descripcion_arr[] = $this->GetY();
                $this->y_fin_obs_par_sol_arr[] = $this->GetY();
                $xos_ini += $anchos["des"];
                $this->setY($this->y_para_obs_partidas);
                $this->setX(0.7 + $anchos["des"] * 2);
                $this->SetFont('Arial', 'B', $font2);
                $this->CellFitScale($anchos["u"] + $anchos["c"], $heigth, 'Obs. de Par.:', 'B', 0, 'R', 0);

                $yop_ini = $this->getY();
                $xop_ini = $this->getX();
                for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                    if (array_key_exists($key, $mejor_opcion_partida) &&  !is_null($mejor_opcion_partida[$key]) &&  $mejor_opcion_partida[$key] == $partida['presupuestos'][$i]['id_transaccion']) {
                        $this->SetFillColor(150, 150, 150);
                        $this->SetTextColor(0, 0, 0);
                    }else {
                        $this->SetFillColor(255, 255, 255);
                        $this->SetTextColor(0, 0, 0);
                    }
                    $this->SetFont('Arial', '', $font);
                    $this->setY($yop_ini);
                    $this->setX($xop_ini);
                    $border = "T R B";
                    $this->SetDrawColor('200', '200', '200');
                    if (array_key_exists('presupuestos', $partida)) {
                        if(array_key_exists($i, $partida['presupuestos']) && $partida['presupuestos'][$i]['cantidad_asignada'] > 0){
                            $this->SetDrawColor('20', '20', '20');
                            $border = "R L B";
                        }
                        $this->MultiCell($anchos["op"], $heigth, array_key_exists($i, $partida['presupuestos']) && $partida['presupuestos'][$i]['observaciones'] ? utf8_decode($partida['presupuestos'][$i]['observaciones']) : '-', $border, "L", 1);
                        
                    }else{
                        $this->MultiCell($anchos["op"], $heigth, '-', "T R L B", "L", 1);
                    }
                    $this->y_para_descripcion_arr[] = $this->GetY()+0.015;
                    $xop_ini += $anchos["op"]+0.015;
                }
                $this->Ln();
            }
            asort($this->y_fin_obs_par_sol_arr);
            $this->y_fin_obs_par_sol = array_pop($this->y_fin_obs_par_sol_arr);
            $this->SetY($this->y_fin_obs_par_sol);
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
                $this->Cell($anchos["pu"], $heigth, array_key_exists($i, $datos['presupuestos']) ? number_format($datos['presupuestos'][$i]['suma_subtotal_partidas'], 2, '.', ',') : '', 1, 0, 'R', 1);
                if (array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i])) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->Cell($anchos["pu"] * 2, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? number_format($datos['presupuestos'][$i]['asignacion_subtotal_partidas'], 3, ".", ",") : '-', 1, 0, 'R', 1);
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
                $this->Cell($anchos["pu"] * 2, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? $datos['presupuestos'][$i]['descuento_global'] : '-', 1, 0, 'R', 1);
                $this->Cell($anchos["pu"], $heigth, "%", 1, 0, "C");
                $this->Cell($anchos["pu"], $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? $datos['presupuestos'][$i]['descuento'] : '-', 1, 0, 'R', 1);
                if (array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i])) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->Cell($anchos["pu"] * 2, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? $datos['presupuestos'][$i]['asignacion_descuento'] : '-', 1, 0, 'R', 1);
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
                $this->Cell($anchos["pu"], $heigth, array_key_exists($i, $datos['presupuestos']) ? number_format($datos['presupuestos'][$i]['suma_con_descuento'], 2, '.', ',') : '-', 1, 0, 'R', 1);

                if (array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i])) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->Cell($anchos["pu"] * 2, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas_descuento_global', $datos['presupuestos'][$i]) ? number_format($datos['presupuestos'][$i]['asignacion_subtotal_partidas_descuento_global'], 3, ".", ",") : '-', 1, 0, 'R', 1);
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
                $this->Cell($anchos["pu"], $heigth, array_key_exists($i, $datos['presupuestos']) ? number_format($datos['presupuestos'][$i]['iva_partidas'], 2, '.', ',') : '-', 1, 0, 'R', 1);
                if (array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i])) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->Cell($anchos["pu"] * 2, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? number_format($datos['presupuestos'][$i]['asignacion_iva'], 2, '.', ',') : '-', 1, 0, 'R', 1);
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
                $this->Cell($anchos["pu"], $heigth, array_key_exists($i, $datos['presupuestos']) ? number_format($datos['presupuestos'][$i]['total_partidas'], 2, '.', ',') : '-', 1, 0, 'R', 1);
                if (array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i])) {
                    $this->SetFillColor(0, 0, 0);
                    $this->SetTextColor(255, 255, 255);
                }
                $this->Cell($anchos["pu"] * 2, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? number_format($datos['presupuestos'][$i]['asignacion_total'], 2, '.', ',') : '-', 1, 0, 'R', 1);
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
                $this->MultiCell($anchos["og"], $heigth, utf8_decode($datos['presupuestos'][$i]['observaciones']), 1, 'J', false);
                $this->y_fin_og_arr[] = $this->getY();
                $x_ini += $anchos["og"];
            }

            asort($this->y_fin_og_arr);
            $this->y_fin_og = array_pop($this->y_fin_og_arr);
            $this->SetY($this->y_fin_og);
            $this->Ln();
            $total_asignado_ic = array();
            $this->Cell($anchos["aesp"] + $anchos["des"]);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell($anchos["p"]  * ($presupuestosXFila), .5, utf8_decode("RESUMEN DE ASIGNACIÓN POR MONEDA"), "B", 1, 'C', 0);
            $this->SetFont('Arial', 'B', $font);
            $this->Cell($anchos["espacio_detalles_globales"]);
            $this->Cell($anchos["espacio_detalles_globales"], $heigth, "", 0, 0, 'C', 0);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFillColor(0, 0, 0);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', $font);
                $this->CellFitScale($anchos["p"], $heigth, utf8_decode($datos['presupuestos'][$i]['empresa']), 1, 0, 'C', 1);
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
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                $this->SetFont('Arial', 'B', $font);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 3, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? $datos['presupuestos'][$i]['subtotal_peso'] : '-', 1, 0, 'R', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["pu"], $heigth, "PESOS (MX)", 1, 0, 'C', 1);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 2, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? $datos['presupuestos'][$i]['subtotal_peso'] : '-', 1, 0, 'R', 0);
            }
            $this->Ln();
            $this->Cell($anchos["espacio_detalles_globales"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                if($i==$i_e) {
                    $this->SetFillColor(100, 100, 100);
                    $this->SetTextColor(0, 0, 0);
                    $this->Cell($anchos["espacio_detalles_globales"], $heigth, $datos['presupuestos'][$i]['tc_usd'], 1, 0, 'C', 0);
                }
                $this->SetFont('Arial', 'B', $font);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 3, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? $datos['presupuestos'][$i]['subtotal_dolar'] : '-', 1, 0, 'R', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["pu"], $heigth, 'DOLAR(USD)', 1, 0, 'C', 1);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 2, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? $datos['presupuestos'][$i]['dolares'] : '-', 1, 0, 'R', 0);
            }
            $this->Ln();
            $this->Cell($anchos["espacio_detalles_globales"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                if($i==$i_e) {
                    $this->SetFillColor(100, 100, 100);
                    $this->SetTextColor(0, 0, 0);
                    $this->Cell($anchos["espacio_detalles_globales"], $heigth, $datos['presupuestos'][$i]['tc_eur'], 1, 0, 'C', 0);
                }
                $this->SetFont('Arial', 'B', $font);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 3, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? $datos['presupuestos'][$i]['subtotal_euro'] : '-', 1, 0, 'R', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["pu"], $heigth, 'EUROS', 1, 0, 'C', 1);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 2, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? $datos['presupuestos'][$i]['euros'] : '-', 1, 0, 'R', 0);
            }

            $this->Ln();
            $this->Cell($anchos["espacio_detalles_globales"]);
            for ($i = $i_e; $i < ($i_e + $inc_ie); $i++) {
                if($i==$i_e) {
                    $this->SetFillColor(100, 100, 100);
                    $this->SetTextColor(0, 0, 0);
                    $this->Cell($anchos["espacio_detalles_globales"], $heigth, $datos['presupuestos'][$i]['tc_libra'], 1, 0, 'C', 0);
                }
                $this->SetFont('Arial', 'B', $font);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 3, $heigth,array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ?  $datos['presupuestos'][$i]['subtotal_libra'] : '-', 1, 0, 'R', 0);
                $this->SetTextColor(255, 255, 255);
                $this->CellFitScale($anchos["pu"], $heigth, 'LIBRAS', 1, 0, 'C', 1);
                $this->SetTextColor(0, 0, 0);
                $this->CellFitScale($anchos["pu"] * 2, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? $datos['presupuestos'][$i]['libras'] : '-', 1, 0, 'R', 0);
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
                $this->CellFitScale($anchos["pu"] * 3, $heigth, array_key_exists($i, $datos['presupuestos']) && array_key_exists('asignacion_subtotal_partidas', $datos['presupuestos'][$i]) ? number_format($datos['presupuestos'][$i]['asignacion_total'], 2, '.', ',') : '-', 1, 0, 'R', 0);
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
            $this->SetX(15);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth, utf8_decode("Comprobación Mejor Opción"), 1, 0, 'C', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth, utf8_decode("Total Asignado"), 1, 0, 'C', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth, utf8_decode("Total Mejor Opción"), 1, 0, 'C', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth, utf8_decode("Diferencia"), 1, 0, 'C', 1);

            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', '', $font2);
            $this->SetX(15);
            $this->Cell(($anchos["espacio_detalles_globales"]/2)-1, $heigth, "SUBTOTAL", 1, 0, 'C', 1);
            $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(($anchos["espacio_detalles_globales"]/2)-1.5, $heigth, "PESO MXN", 1, 0, 'C', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth,  number_format($this->asignacion->suma_total_con_descuento, 2, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth,  number_format($this->asignacion->mejor_asignado, 2, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth,  number_format($this->asignacion->diferencia, 2, ".", ","), 1, 0, 'R', 1);

            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', '', $font2);
            $this->SetX(15);
            $this->Cell(($anchos["espacio_detalles_globales"]/2)-1, $heigth, "IVA", 1, 0, 'C', 1);
            $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(($anchos["espacio_detalles_globales"]/2)-1.5, $heigth, "PESO MXN", 1, 0, 'C', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth,  number_format($this->asignacion->suma_subtotal_partidas_iva, 2, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth,  number_format($this->asignacion->mejor_asignado_iva, 2, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth,  number_format($this->asignacion->diferencia_iva, 2, ".", ","), 1, 0, 'R', 1);
            $this->Ln();
            $this->SetFillColor(100, 100, 100);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', '', $font2);
            $this->SetX(15);
            $this->Cell(($anchos["espacio_detalles_globales"]/2)-1, $heigth, "TOTAL", 1, 0, 'C', 1);
            $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(($anchos["espacio_detalles_globales"]/2)-1.5, $heigth, "PESO MXN", 1, 0, 'C', 1);
            $this->Cell(($anchos["espacio_detalles_globales"]-2.5), $heigth, number_format($this->asignacion->suma_subtotal_partidas_total, 2, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth,  number_format($this->asignacion->mejor_asignado_total, 2, ".", ","), 1, 0, 'R', 1);
            $this->Cell($anchos["espacio_detalles_globales"]-2.5, $heigth,  number_format($this->asignacion->diferencia_total, 2, ".", ","), 1, 0, 'R', 1);
            $this->Ln(1);
            $i_e+=$presupuestosXFila;
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
        $base = Context::getDatabase();
        $id_obra = Context::getIdObra();
        $this->SetTextColor(0, 0, 0);
        $this->SetY(-5.1);
        $this->SetFont('Arial', '', 6);
        $this->SetTextColor(0, 0, 0);
        if ($base == "SAO1814" && $id_obra == 41) {
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
            $this->Cell(6.6, 0.7, '', 'TRLB', 0, 'C');
            $this->Cell(6.6, 0.7, '', 'TRLB', 0, 'C');
            $this->Cell(6.4, 0.7, '', 'TRLB', 0, 'C');
            $this->Cell(6.4, 0.7, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->Cell(6.6, .4, 'LIC. BRENDA ELIZABETH ESQUIVEL ESPINOZA', 'TRLB', 0, 'C', 1);
            $this->Cell(6.6, .4, 'C.P JAVIER MENDEZ JOSE', 'TRLB', 0, 'C', 1);
            $this->Cell(6.4, .4, 'ING. JUAN CARLOS MARTINEZ ANTUNA', 'TRLB', 0, 'C', 1);
            $this->Cell(6.4, .4, 'ING. PEDRO ALFONSO MIRANDA REYES', 'TRLB', 0, 'C', 1);
        }
        else if ($base == "SAO1814_TUNEL_DRENAJE_PRO"){

            $this->SetFont('Arial', '', 4);
            $this->SetFillColor(180, 180, 180);
            $this->Cell(3.35, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(6.7, .4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(10.05, .4, utf8_decode('Vo. Bo.'), 'TRLB', 0, 'C', 1);
            $this->Cell(10.05, .4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
            $this->Ln();
            $this->CellFitScale(3.35, .4, 'SUBCONTRATOS TDP', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'GERENCIA ADMINISTRATIVA TDP', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'GERENTE DE ADMINISTRACION DE CONTRATO TDP', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'GERENTE DE CONSTRUCCION TDP', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'GERENTE DE PROCURACION ICA', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'GERENTE DE SUBCONTRATOS LA PENINSULAR', 'TRLB', 0, 'C', 1);
            $this->Cell(3.35, .4, utf8_decode('COMITÉ TDP'), 'TRLB', 0, 'C', 1);
            $this->Cell(3.35, .4, utf8_decode('COMITÉ TDP'), 'TRLB', 0, 'C', 1);
            $this->Cell(3.35, .4, utf8_decode('COMITÉ TDP'), 'TRLB', 0, 'C', 1);
            $this->Ln();
            $this->Cell(3.35, 0.7, '', 'TRLB', 0, 'C');
            $this->Cell(3.35, 0.7, '', 'TRLB', 0, 'C');
            $this->Cell(3.35, 0.7, '', 'TRLB', 0, 'C');
            $this->Cell(3.35, 0.7, '', 'TRLB', 0, 'C');
            $this->Cell(3.35, 0.7, '', 'TRLB', 0, 'C');
            $this->Cell(3.35, 0.7, '', 'TRLB', 0, 'C');
            $this->Cell(3.35, 0.7, '', 'TRLB', 0, 'C');
            $this->Cell(3.35, 0.7, '', 'TRLB', 0, 'C');
            $this->Cell(3.35, 0.7, '', 'TRLB', 0, 'C');

            $this->Ln();
            $this->CellFitScale(3.35, .4, 'ING. GUADALUPE MORENO HERNANDEZ', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'C.P. ANDRES MORENO AYALA', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'LUIS ALFONSO HERNANDEZ REDING', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'ING. FRANCISCO JAVIER BAY ORTUZAR', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'ING. RUBEN LEON T.', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'ING. ALEJANDRO JIMENEZ VAZQUEZ', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'ING. CARLOS R. DE LA MORA RODRIGUEZ', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'ING. JULIO CESAR ROSIQUE ROSIQUE', 'TRLB', 0, 'C', 1);
            $this->CellFitScale(3.35, .4, 'ING. LUIS HORCASITAS MANJARREZ', 'TRLB', 0, 'C', 1);
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
                $this->Cell(6.2, 0.7, '', 1, 0, 'R', 0, '');
                $this->Cell(.4);
            }
        }

        $this->SetY(-3.8);
        $this->image("data:image/png;base64,".base64_encode(QrCode::format('png')->generate($this->cadena_qr)), $this->GetX(), $this->GetY(), 3.5, 3.5,'PNG');
        $this->SetY(-3.6);

        $this->setX(4.5);
        $this->MultiCell(26.5, 0.4, $this->cadena);
        $this->SetFont('Arial', '', 6);
        $this->SetY(16.5);
        $this->setX(4.5);
        $this->SetFont('Arial', 'B', 6);
        $this->SetTextColor('100,100,100');
        $this->SetY(-1.5);
        $this->Cell(30.2, .4, ( utf8_decode('Sistema de Administración de Obra')), 0, 0, 'R');
        $this->SetY(-1);
        $this->SetFont('Arial', 'BI', 6);
        $this->SetTextColor('0,0,0');
        $this->Cell(3.5);
        $this->Cell(11.5, .4, utf8_decode('Formato generado desde el sistema de contratos. Fecha de registro: ') . $this->asignacion->fecha_registro_format.' Fecha de consulta: '.date("d-m-Y H:i:s"), 0, 0, 'L');
        $this->Cell(20.2, .4, (utf8_decode('Página ')) . $this->PageNo() . '/{nb}',0, 0, 'R');
    }

    function create()
    {
        $this->SetMargins(0.7, 1, 0.7);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 5);
        $this->partidas();

        try {
            $this->Output('I', 'Formato - Tabla Comparativa de Asignaciones '.$this->asignacion->numero_folio_format.'-contrato:'.$this->asignacion->contratoProyectado->numero_folio_format.'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }
}
