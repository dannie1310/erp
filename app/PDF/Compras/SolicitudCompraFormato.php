<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 04/11/2019
 * Time: 07:17 p. m.
 */


namespace App\PDF\Compras;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\SolicitudCompra;
use App\Utils\ValidacionSistema;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SolicitudCompraFormato extends Rotation
{
    protected $solicitud;
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

    public function __construct(SolicitudCompra $solicitudCompra)
    {

        parent::__construct('P', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());
        $this->solicitud = $solicitudCompra;

        $this->SetAutoPageBreak(true, 5);
        $this->WidthTotal = $this->GetPageWidth() - 2;
        $this->txtTitleTam = 18;
        $this->txtSubtitleTam = 13;
        $this->txtSeccionTam = 9;
        $this->txtContenidoTam = 11;
        $this->txtFooterTam = 6;
        $this->encabezado_pdf = $this->solicitud->encabezado_pdf;
        $this->createQR();

        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 6);
        $this->partidas();
    }

    function Header()
    {
        $this->setXY(1, 1.5);
        $this->SetFont('Arial', 'B', 18);
        $this->MultiCell(12.5, "0.7", utf8_decode($this->encabezado_pdf), 0, "C"); //(1* $this->WidthTotal, 0.1, utf8_decode($this->encabezado_pdf), '', 'CB');

        $this->setY(2);
        //Obtener Posiciones despues de los títulos
        $y_inicial = $this->getY() - 1;
        $x_inicial = 14;
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
        $this->Ln(0.5);

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
        $this->Row(array(utf8_decode($this->obra->descripcion)));
        $this->Ln(.2);
        $this->SetFont('Arial', '', 10);
        $this->Cell(10);
        $this->Cell(9.5, .7, utf8_decode('EMPRESA'), 0, 0, 'L');
        $this->Ln(.6);
        $y_inicial = $this->getY();
        $this->Cell(10);
        $this->SetFont('Arial', 'B', 10);
        $this->CellFitScale(9.5, .5, utf8_decode($this->obra->descripcion), '', 'J');
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

    function detallesSolicitudPagoAnticipado($x)
    {

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FOLIO'), 'LT', 0, 'L');
        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->Cell(0.207 * $this->WidthTotal, 0.5, '' . $this->solicitud->complemento ? utf8_decode($this->solicitud->complemento->folio_compuesto) : '', 'RT', 1, 'R');

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FECHA'), 'L', 0, 'L');
        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->Cell(0.207 * $this->WidthTotal, 0.5, '' . $this->solicitud->fecha_format, 'R', 1, 'R');

        if (!is_null($this->solicitud->complemento)) {
            $this->SetFont('Arial', 'B', $this->txtContenidoTam);
            $this->SetX($x);
            $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FECHA REQ. O.'), 'L', 0, 'L');
            $this->SetFont('Arial', 'B', $this->txtContenidoTam);
            $this->Cell(0.207 * $this->WidthTotal, 0.5, '' . $this->solicitud->complemento ? date("d/m/Y", strtotime($this->solicitud->complemento->fecha_requisicion_origen)) : '', 'R', 1, 'R');
        }


        if (!is_null($this->solicitud->complemento)) {
            $this->SetFont('Arial', 'B', $this->txtContenidoTam);
            $this->SetX($x);
            $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FOLIO REQ. O.'), 'L', 0, 'L');
            $this->SetFont('Arial', 'B', $this->txtContenidoTam);
            $this->Cell(0.207 * $this->WidthTotal, 0.5, '' . $this->solicitud->complemento->requisicion_origen, 'R', 1, 'R');
        }


        $this->SetFont('Arial', 'B', 9);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.5, utf8_decode('FOLIO SAO'), 'LB', 0, 'L');
        $this->SetFont('Arial', 'B', '#' . 10);
        $this->Cell(0.207 * $this->WidthTotal, 0.5, $this->solicitud->numero_folio_format, 'RB', 1, 'R');

    }

    function partidas()
    {

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

            $autorizada = '-';
            if($this->solicitud->estado>0)
            {
                $autorizada  = $item->cantidad ;
            }

            $this->Row([
                $i + 1,
                $item->cantidad_original1 > 0 ? $item->cantidad_original1 : $item->cantidad,
                $autorizada,
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
                $this->Row([utf8_decode($item->entrega->concepto->path)]);
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

    function firmas()
    {
        if ($this->obra->configuracionCompras) {
            if ($this->obra->configuracionCompras->con_autorizacion == 1) {
                if ($this->solicitud->estado == 0) {
                    $this->SetFont('Arial', '', 80);
                    $this->SetTextColor(204, 204, 204);
                    $this->RotatedText(2, 20, "PENDIENTE DE", 45);
                    $this->RotatedText(7, 20, "AUTORIZAR", 45);
                    $this->SetTextColor('0,0,0');
                }
                if ($this->solicitud->estado == -1 || $this->solicitud->estado == -2) {
                    $this->SetFont('Arial', '', 80);
                    $this->SetTextColor(204, 204, 204);
                    $this->RotatedText(2, 20, "SOLICITUD", 45);
                    $this->RotatedText(7, 20, "RECHAZADA", 45);
                    $this->SetTextColor('0,0,0');
                }
            }
        }
        $this->SetY(-5.9);
        $this->SetFont('Arial', '', 6);
        if (Context::getDatabase() == "SAO1814" && Context::getIdObra() == 41)
        {
            //if(true){

            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);
            $this->Cell(4.8, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            //$this->Cell(4.8, .4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(4.8, .4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(10, .4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
            $this->Ln();
            //$this->Cell(4, .4, 'Jefe Compras', 'TRLB', 0, 'C', 1);
            $this->Cell(4.8, .4, utf8_decode('Jefe Almacén'), 'TRLB', 0, 'C', 1);
            $this->Cell(4.8, .4, 'Gerente Administrativo', 'TRLB', 0, 'C', 1);
            $this->Cell(5, .4, utf8_decode('Control de Costos'), 'TRLB', 0, 'C', 1);
            $this->Cell(5, .4, 'Director de proyecto', 'TRLB', 0, 'C', 1);
            $this->Ln();

            //$this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Ln();
            //$this->SetFillColor(180, 180, 180);
            //$this->Cell(4, .4, 'LIC. BRENDA ELIZABETH ESQUIVEL ESPINOZA', 'TRLB', 0, 'C', 1);
            $this->Cell(4.8, .4, 'LIC. FERNANDO HERNANDEZ ALMAZAN', 'TRLB', 0, 'C', 1);
            $this->Cell(4.8, .4, 'C.P. ROGELIO HERNANDEZ BELTRAN', 'TRLB', 0, 'C', 1);
            $this->Cell(5, .4, 'ING. JUAN CARLOS MARTINEZ ANTUNA', 'TRLB', 0, 'C', 1);
            $this->Cell(5, .4, 'ING. PEDRO ALFONSO MIRANDA REYES', 'TRLB', 0, 'C', 1);
        }
        else if (Context::getDatabase() == "SAO1814_TUNEL_MANZANILLO" && Context::getIdObra() == 3 && $this->solicitud->id_area_compradora != 4)
        {

            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(255, 255, 255);
            //$this->Cell(4, .4, 'Jefe Compras', 'TRLB', 0, 'C', 1);
            $this->Cell(4.8, .4, utf8_decode('Solicitó'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.8, .4, utf8_decode('Capturó'), 'TRLB', 0, 'C', 0);
            $this->Cell(5, .4, utf8_decode('Aprobó'), 'TRLB', 0, 'C', 0);
            $this->Cell(5, .4, 'Control de Proyectos', 'TRLB', 0, 'C', 0);
            $this->Ln();

            //$this->Cell(4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Ln();
            //$this->SetFillColor(180, 180, 180);
            //$this->Cell(4, .4, 'LIC. BRENDA ELIZABETH ESQUIVEL ESPINOZA', 'TRLB', 0, 'C', 1);
            $this->Cell(4.8, .4, '', 'TRLB', 0, 'C', 0);
            $this->Cell(4.8, .4, utf8_decode($this->solicitud->usuario_registro), 'TRLB', 0, 'C', 0);
            $this->Cell(5, .4, utf8_decode('C.P. MARCO A. MALDONADO HERNANDEZ'), 'TRLB', 0, 'C', 0);
            $this->Cell(5, .4, '', 'TRLB', 0, 'C', 0);
        }
        else if (Context::getDatabase() == "SAO1814_TUNEL_MANZANILLO" && Context::getIdObra() == 3 && $this->solicitud->id_area_compradora == 4)
        {
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(255, 255, 255);

            $this->Cell(4.8, .4, utf8_decode('Solicitó'), 'TRLB', 0, 'C', 0);
            $this->Cell(4.8, .4, utf8_decode('Capturó'), 'TRLB', 0, 'C', 0);
            $this->Cell(5, .4, utf8_decode('Aprobó'), 'TRLB', 0, 'C', 0);
            $this->Cell(5, .4, utf8_decode('Aprobó'), 'TRLB', 0, 'C', 0);
            $this->Ln();

            $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
            $this->Ln();

            $this->Cell(4.8, .4, '', 'TRLB', 0, 'C', 0);
            $this->Cell(4.8, .4, utf8_decode(''), 'TRLB', 0, 'C', 0);
            $this->Cell(5, .4, 'ADMINISTRADOR DEL PROYECTO', 'TRLB', 0, 'C', 0);
            $this->Cell(5, .4, utf8_decode('ING. JOSE MARTÍN ORTIZ VAZQUEZ'), 'TRLB', 0, 'C', 0);
        }
        else
            {
            $this->CellFitScale(6, .5, utf8_decode('Solicitó'), 1, 0, 'C');
            $this->Cell(.7);
            $this->CellFitScale(6, .5, utf8_decode('Capturó'), 1, 0, 'C');
            $this->Cell(.8);
            $this->CellFitScale(6, .5, utf8_decode('Aprobó'), 1, 0, 'C');
            $this->Ln(.5);
            $this->CellFitScale(6, 1, ' ', 1, 0, 'C');
            $this->Cell(.7);
            $this->CellFitScale(6, 1, ' ', 1, 0, 'C');
            $this->Cell(.8);
            $this->CellFitScale(6, 1, ' ', 1, 0, 'R');
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
        $this->firmas();

        $this->SetY(-3.8);
        $this->image("data:image/png;base64," . base64_encode(QrCode::format('png')->generate($this->cadena_qr)), $this->GetX(), $this->GetY(), 3.5, 3.5, 'PNG');
        $this->SetY(-3.6);
        $this->SetX(-17);
        $this->SetFont('Arial', '', 5);
        $this->MultiCell(16, .3, utf8_decode($this->cadena), 0, 'L');
        $this->Ln(.2);

        $this->SetY($this->GetPageHeight() - 1);
        $this->SetFont('Arial', '', 6);

        $this->SetFont('Arial', 'B', 6);
        $this->SetTextColor('100,100,100');
        $this->SetY(-1.3);
        $this->Cell(19.5, .4, utf8_decode('Sistema de Administración de Obra'), 0, 0, 'R');

        $this->SetFont('Arial', 'BI', 6);
        $this->SetY(-0.8);
        $this->setX(4.5);
        $this->SetTextColor('0,0,0');
        $this->Cell(7, .4, utf8_decode('Formato generado desde el sistema de compras del SAO ERP. Fecha y hora de registro: ' . $this->solicitud->fecha_hora_registro_format), 0, 0, 'L');

        $this->Ln(.5);
        $this->SetY(-0.9);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(19.5, .5, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    public function createQR()
    {
        $verifica = new ValidacionSistema();
        $datos_qr2['titulo'] = "Formato Solicitud de Compra_" . date("d-m-Y") . "_" . ($this->solicitud->complemento ? $this->solicitud->complemento->folio_compuesto : '') . "_" . $this->solicitud->numero_folio_format;
        $datos_qr2["base"] = Context::getDatabase();
        $datos_qr2["obra"] = $this->obra->nombre;
        $datos_qr2["tabla"] = "transacciones";
        $datos_qr2["campo_id"] = "id_transaccion";
        $datos_qr2["id"] = $this->solicitud->id_transaccion;
        $cadena_json_id = json_encode($datos_qr2);

        $firmada = $verifica->encripta($cadena_json_id);
        $this->cadena_qr = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/api/compras/solicitud-compra/leerQR?data=" . urlencode($firmada);
        $this->cadena = $firmada;

        $this->dato = $verifica->encripta($cadena_json_id);

        $this->qr_name = 'qrcode_' . mt_rand() . '.png';
    }

    function create()
    {
        try {
            $this->Output('I', "Formato - Solicitud Compra_" . $this->solicitud->numero_folio . ".pdf", 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }
}
