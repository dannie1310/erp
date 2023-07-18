<?php

namespace App\PDF\PortalProveedores;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\SolicitudCompra;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Utils\ValidacionSistema;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class InvitacionCotizarFormato extends Rotation
{
    protected $solicitud;
    protected $contrato;
    protected $invitacion;
    protected $obra;
    private $encabezado_pdf = '';
    var $encola = '';
    private $cadena_qr = '';
    private $cadena = '';
    private $dato = '';
    private $qr_name = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     * SolicitudCompraFormato constructor.
     * @param $solicitudCompra
     */

    public function __construct(Invitacion $invitacion)
    {

        parent::__construct('P', 'cm', 'Letter');

        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $invitacion->base_datos);
        $this->obra = Obra::find($invitacion->id_obra);
        $this->solicitud = $invitacion->solicitud;
        $this->contrato = $invitacion->contratoProyectado;
        $this->invitacion = $invitacion;

        $this->SetAutoPageBreak(true, 2);
        $this->WidthTotal = $this->GetPageWidth() - 2;
        $this->txtTitleTam = 18;
        $this->txtSubtitleTam = 13;
        $this->txtSeccionTam = 9;
        $this->txtContenidoTam = 11;
        $this->txtFooterTam = 6;
        $this->encabezado_pdf = ($this->invitacion->tipo == 2) ? "INVITACIÓN A CONTRAOFERTAR" : "INVITACIÓN A COTIZAR";

        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 6);
        $this->partidas();
    }

    function Header()
    {
        $this->setXY(1, 1);
        $this->SetFont('Arial', 'B', 18);
        $this->MultiCell(12.5, "0.7", utf8_decode($this->encabezado_pdf), 0, "C");

        $this->setY(2);
        //Obtener Posiciones despues de los títulos
        $y_inicial = $this->getY() - 1;
        $x_inicial = 14;
        $this->setY($y_inicial);
        $this->setX($x_inicial);


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

        //Encabezado
        $this->setY($y_inicial);
        $this->setX($x_inicial);
        $this->encabezado($x_inicial);


        //Obtener Y después de la tabla

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
        $this->Row(array(utf8_decode($this->obra->nombre_obra_formatos)));
        $this->Ln(.2);
        $this->SetFont('Arial', '', 10);
        $this->Cell(10);
        $this->Cell(9.5, .7, utf8_decode('EMPRESA'), 0, 0, 'L');
        $this->Ln(.6);
        $y_inicial = $this->getY();
        $this->Cell(10);
        $this->SetFont('Arial', 'B', 10);
        $this->CellFitScale(9.5, .5, utf8_decode($this->obra->nombre_obra_formatos), '', 'J');
        $this->Ln(.5);
        $this->Cell(10);
        $this->SetFont('Arial', '', 10);
        $this->Multicell(9.5, .5, utf8_decode($this->obra->direccion) . '
    RFC: ' . $this->obra->rfc, '', 'J');
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
        $this->CellFitScale(9.5, .5, utf8_decode($this->obra->facturar), '', 'J');
        $this->Ln(.5);
        $this->Cell(10);
        $this->SetFont('Arial', '', 10);
        $this->Multicell(9.5, .5, utf8_decode($this->obra->direccion . '
RFC: ' . $this->obra->rfc), '', 'J');


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

        if($this->solicitud)
        {
            if ($this->encola == 'partida') {
                $this->Ln(.7);
                $this->SetFont('Arial', '', 6);
                $this->SetFillColor(180, 180, 180);
                $this->SetWidths([0.5, 1.5, 1.5, 2.5, 2.5, 9, 2]);
                $this->SetStyles(['DF', 'DF', 'DF', 'FD', 'FD', 'DF']);
                $this->SetRounds(['1', '', '', '', '', '', '2']);
                $this->SetRadius([0.2, 0, 0, 0, 0, 0, 0.2]);
                $this->SetFills(['180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
                $this->SetHeights([0.4]);
                $this->SetAligns(['C', 'C', 'C', 'C', 'C', 'C', 'C']);
                $this->Row(["#", "Cant. Solicitada", "Cant. Autorizada", "Unidad", "No. Parte", utf8_decode("Descripción"), "Fecha de Entrega Requerida"]);

                $this->SetWidths([0.5, 1.5, 1.5, 2.5, 2.5, 9, 2]);
                $this->SetRounds(['', '', '', '', '', '', '']);
                $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
                $this->SetAligns(['C', 'R', 'R', 'C', 'L', 'L', 'C']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            }
            if ($this->encola == 'observacion_partida') {
                $this->SetRounds(['4', '', '', '', '', '', '3']);
                $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0]);
                $this->SetWidths([19.5]);
                $this->SetAligns(['L']);
            }
        }

        if($this->contrato)
        {
            $this->partidasTitle();
        }
    }

    public function partidasTitle()
    {
        $this->Ln();
        $this->SetFills(180, 180, 180);
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);

        $this->setXY(1, 8);

        $this->Cell((0.030 * $this->WidthTotal), 0.4, '#', 'LRBT', 0, 'C', 180);
        $this->Cell((0.1 * $this->WidthTotal), 0.4, 'Clave', 'LRBT', 0, 'C', 180);
        $this->Cell((0.38 * $this->WidthTotal), 0.4, utf8_decode('Descripción'), 'LRBT', 0, 'C', 180);
        $this->Cell((0.099 * $this->WidthTotal), 0.4, 'Unidad', 'LRBT', 0, 'C', 180);
        $this->Cell((0.099 * $this->WidthTotal), 0.4, 'Cantidad', 'LRBT', 0, 'C', 180);
        $this->Cell((0.29 * $this->WidthTotal), 0.4, 'Destino', 'LRBT', 0, 'C', 180);
        $w_t = $this->WidthTotal;
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([$w_t * 0.030, $w_t * 0.1, $w_t * 0.38, $w_t * 0.099, $w_t * 0.099, $w_t * 0.29]);
        $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '2']);
        $this->SetRadius([0, 0, 0, 0, 0, 0]);
        $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C', 'L', 'L', 'R', 'R', 'L']);
    }

    function encabezado($x)
    {

        $this->SetFont('Arial', 'B', 9);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FOLIO DE INVITACIÓN:'), 'LT', 0, 'L');
        $this->Cell(0.207 * $this->WidthTotal, 0.5, '' . $this->invitacion->numero_folio_format, 'RT', 1, 'R');

        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FECHA DE INVITACIÓN:'), 'L', 0, 'L');
        $this->Cell(0.207 * $this->WidthTotal, 0.5, '' . $this->invitacion->fecha_hora_format, 'R', 1, 'R');

        $this->SetX($x);
        if($this->solicitud)
        {
            $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FOLIO DE SOLICITUD:'), 'LB', 0, 'L');
            $this->Cell(0.207 * $this->WidthTotal, 0.5, '' . $this->solicitud->numero_folio_format, 'RB', 1, 'R');
        }
        if($this->contrato)
        {
            $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FOLIO DE CONTRATO:'), 'LB', 0, 'L');
            $this->Cell(0.207 * $this->WidthTotal, 0.5, '' . $this->contrato->numero_folio_format, 'RB', 1, 'R');
        }
    }

    function partidas()
    {

        if($this->solicitud){
            /*Concepto*/
            if (!is_null($this->solicitud->complemento)) {
                $this->SetWidths(array(19.5));
                $this->SetRounds(array('12'));
                $this->SetRadius(array(0.2));
                $this->SetFills(array('180,180,180'));
                $this->SetTextColors(array('0,0,0'));
                $this->SetStyles(array('DF'));
                $this->SetHeights(array(0.5));
                $this->SetFont('Arial', '', 6);
                $this->SetAligns(array('C'));
                $this->Row(array("Concepto"));
                $this->SetRounds(array('34'));
                $this->SetRadius(array(0.2));
                $this->SetAligns(array('C'));
                $this->SetStyles(array('DF'));
                $this->SetFills(array('255,255,255'));
                $this->SetTextColors(array('0,0,0'));
                $this->SetHeights(array(0.5));
                $this->SetFont('Arial', '', 6);
                $this->Row(array(utf8_decode(str_replace(array("\r", "\n"), '', "" . $this->solicitud->complemento->concepto))));
            }

            /*Partidas*/
            $this->Ln(.7);
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);
            $this->SetWidths([0.5, 1.5, 1.5, 2.5, 2.5, 9, 2]);
            $this->SetStyles(['DF', 'DF', 'DF', 'FD', 'FD', 'DF']);
            $this->SetRounds(['1', '', '', '', '', '', '2']);
            $this->SetRadius([0.2, 0, 0, 0, 0, 0, 0.2]);
            $this->SetFills(['180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            $this->SetHeights([0.4]);
            $this->SetAligns(['C', 'C', 'C', 'C', 'C', 'C', 'C']);
            $this->Row(["#", "Cant. Solicitada", "Cant. Autorizada", "Unidad", "No. Parte", utf8_decode("Descripción"), "Fecha de Entrega Requerida"]);

            /*Imprimimos las partidas*/
            foreach ($this->solicitud->partidas as $i => $item) {
                $this->encola = 'partida';
                $this->SetWidths([0.5, 1.5, 1.5, 2.5, 2.5, 9, 2]);
                $this->SetRounds(['', '', '', '', '', '', '']);
                $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
                $this->SetAligns(['C', 'R', 'R', 'C', 'L', 'L', 'C']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);

                $this->Row([
                    $i + 1,
                    $item->cantidad_original1 > 0 ? $item->cantidad_original1 : $item->cantidad,
                    $item->cantidad_original1 > 0 ? $item->cantidad : '-',
                    $item->unidad,
                    utf8_decode($item->material->numero_parte),
                    utf8_decode($item->material->descripcion),
                    $item->entrega->fecha_format,
                ]);

                $this->encola = 'observacion_partida';
                /*Destino de Concepto o Almacén */
                $this->SetRounds(['4', '', '', '', '', '', '3']);
                $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0]);
                $this->SetWidths([19.5]);
                $this->SetAligns(['L']);
                if ($item->entrega->concepto) {
                    $this->Row([utf8_decode($item->entrega->concepto->path_sgv)]);
                }
                if ($item->entrega->almacen) {
                    $this->Row([utf8_decode($item->entrega->almacen->descripcion)]);
                }

                /*Observaciones de partida*/
                if ($item->complemento && $item->complemento->observaciones != "") {
                    $this->encola = 'observacion_partida';
                    $this->SetRounds(['4', '', '', '', '', '', '3']);
                    $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0]);
                    $this->SetWidths([19.5]);
                    $this->SetAligns(['L']);

                    if ($item->complemento) {
                        $this->Row([utf8_decode($item->complemento->observaciones)]);
                    }
                }
            }

            /*Observaciones de la Solicitud*/
            if (!is_null($this->solicitud->observaciones)) {
                $this->Ln(.7);
                $this->SetWidths(array(19.5));
                $this->SetRounds(array('12'));
                $this->SetRadius(array(0.2));
                $this->SetFills(array('180,180,180'));
                $this->SetTextColors(array('0,0,0'));
                $this->SetStyles(array('DF'));
                $this->SetHeights(array(0.5));
                $this->SetFont('Arial', '', 6);
                $this->SetAligns(array('C'));
                $this->Row(array("Observaciones"));
                $this->SetRounds(array('34'));
                $this->SetRadius(array(0.2));
                $this->SetAligns(array('C'));
                $this->SetStyles(array('DF'));
                $this->SetFills(array('255,255,255'));
                $this->SetTextColors(array('0,0,0'));
                $this->SetHeights(array(0.5));
                $this->SetFont('Arial', '', 6);
                $this->SetAligns(array('J'));
                $this->Row(array(utf8_decode(str_replace(array("\r", "\n"), '', "" . $this->solicitud->observaciones))));
            }

        }

        if($this->contrato){
            $w_t = $this->WidthTotal;
            $this->Ln();
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);
            $this->SetWidths([$w_t * 0.030, $w_t * 0.1, $w_t * 0.38, $w_t * 0.099, $w_t * 0.099, $w_t * 0.29]);
            $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF']);
            $this->SetRounds(['1', '', '', '', '', '2']);
            $this->SetRadius([0, 0, 0, 0, 0, 0]);
            $this->SetFills(['255,255,55', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            $this->SetHeights([0.4]);
            $this->SetAligns(['C', 'L', 'L', 'R', 'R', 'L']);

            foreach ($this->contrato->conceptos as $key => $c) {

                $path_corta = '';
                if( $c->destino){
                    if($c->destino->concepto)
                    {
                        $path_corta = utf8_decode($c->destino->concepto->getPathCortaSgv($this->invitacion->id_obra));
                    }
                }

                $this->Row([$key + 1,
                    mb_strtoupper($c->clave),
                    utf8_decode(mb_strtoupper($c->descripcion_guion_nivel_format)),
                    $c->unidad != null ? $c->unidad : '',
                    $c->cantidad_original != '0' ? number_format($c->cantidad_original, 2, ".", ",") : '-',
                    $path_corta]);
            }
        }


    }

    function Footer()
    {
        if (!App::environment('production')) {
            $this->SetFont('Arial', 'B', 80);
            $this->SetTextColor(155, 155, 155);
            $this->RotatedText(5, 15, utf8_decode("MUESTRA"), 45);
            $this->RotatedText(6, 21, utf8_decode("SIN VALOR"), 45);
            $this->SetTextColor('0,0,0');
        }

        $this->SetY($this->GetPageHeight() - 1);
        $this->SetFont('Arial', '', 6);

        $this->SetFont('Arial', 'B', 6);
        $this->SetTextColor('100,100,100');
        $this->SetY(-1.3);
        $this->Cell(19.5, .4, utf8_decode('Sistema de Administración de Obra'), 0, 0, 'R');

        $this->SetFont('Arial', 'BI', 6);
        $this->SetY(-1.3);
        $this->setX(1);
        $this->SetTextColor('0,0,0');
        $this->Cell(7, .4, utf8_decode('Formato generado desde el SAO ERP. Fecha y hora de registro: ' . $this->invitacion->fecha_hora_format), 0, 0, 'L');

        $this->Ln(.5);
        $this->SetY(-0.9);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(19.5, .5, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create()
    {
        try {
            $this->Output('I', "Formato - Invitacion a Cotizar" . $this->invitacion->numero_folio . ".pdf", 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }
}
