<?php


namespace App\PDF\ControlPresupuesto;


use Carbon\Carbon;
use BaconQrCode\Writer;
use App\Facades\Context;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\Obra;
use Dingo\Api\Facade\Route;
use App\Models\CADECO\Concepto;
use BaconQrCode\Renderer\Image\Png;
use Illuminate\Support\Facades\App;
use App\Models\CADECO\Configuracion\NodoTipo;
use App\Models\CADECO\ControlPresupuesto\VariacionVolumen;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioPartidaHistorico;

class VariacionVolumenFormato extends Rotation
{
    var $encola = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 100;

    /**
     *EntradaAlmacenFormato constructor.
     * @param $entrada
     */

    public function __construct($id)
    {
        parent::__construct('L', 'cm', 'A4');
        $this->SetAutoPageBreak(true,5);
        $this->WidthTotal = $this->GetPageWidth() - 2;
        $this->txtTitleTam = 18;
        $this->txtSubtitleTam = 13;
        $this->txtSeccionTam = 9;
        $this->txtContenidoTam = 7;
        $this->txtFooterTam = 6;
        $this->obra = Obra::find(Context::getIdObra());

        $this->solicitud = VariacionVolumen::find($id);
    }

    public function Header()
    {
        $this->titulos();
        //Obtener Posiciones despues de los títulos
        $y_inicial = $this->getY();
        $x_inicial = $this->GetPageWidth() / 2;
        $this->setY($y_inicial);
        $this->setX($x_inicial);
        $this->logo();

        //Tabla Detalles de la Asignación
        $this->detallesAsignacion($x_inicial);

        //Posiciones despues de la primera tabla
        $y_final = $this->getY();

        $this->setXY($x_inicial, $y_inicial);

        $alto = abs($y_final - $y_inicial);

        //Redondear Bordes Detalles Asignacion
        $this->SetWidths(array(0.5 * $this->WidthTotal));

        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array($alto));
        $this->SetStyles(array('DF'));
        $this->SetAligns("L");
        $this->SetFont('Arial', '', $this->txtContenidoTam);
        $this->setXY($x_inicial, $y_inicial);
        $this->Row(array(""));

        $this->setXY($x_inicial, $y_inicial);


        //Tabla Detalles de la Asignacion
        $this->setY($y_inicial);
        $this->setX($x_inicial);
        $this->detallesAsignacion($x_inicial);

        //Obtener Y después de la tabla
        $this->setY($y_final);
        $this->Ln(1);

        if($this->encola == 'partidas') {
            $this->SetWidths(array(0.03 * $this->WidthTotal,  0.47 * $this->WidthTotal, 0.07
                * $this->WidthTotal, 0.07 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal));
            $this->SetStyles(array('DF', 'DF', 'FD', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF'));
            $this->SetFills(array('180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180'));
            $this->SetTextColors(array('0,0,0',  '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
            $this->SetHeights(array(0.3));
            $this->SetAligns(array('R',  'R', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
            $this->Row(array('#',  utf8_decode("Descripción"), utf8_decode("Unidad"), utf8_decode("Precio Unitario"), utf8_decode("Volúmen Original"), utf8_decode("Volúmen del Cambio"), utf8_decode("Volúmen Actualizado"), utf8_decode("Importe Original"), utf8_decode("Importe del Cambio"), utf8_decode("Importe Actualizado") ));

            $this->SetFont('Arial', '', 6);
            $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
            $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
            $this->SetHeights(array(0.35));
            $this->SetAligns(array('L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
        }

        else if($this->encola == 'motivo')
        {
            $this->SetWidths(array($this->WidthTotal));
            $this->SetFills(array('180,180,180'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetHeights(array(0.3));
            $this->SetFont('Arial', '', 6);
            $this->SetAligns(array('C'));

        }
        else if ($this->encola == 'resumen_h') {
            $this->SetX(($this->WidthTotal * .6) + 1);
            $this->SetFont('Arial', '', 6);
            $this->SetStyles(array('DF', 'DF'));
            $this->SetFills(array('180,180,180', '180,180,180'));
            $this->SetTextColors(array('0,0,0', '0,0,0'));
            $this->SetHeights(array(0.38));
            $this->SetAligns(array('C', 'C'));
            $this->SetWidths(array(0.2 * $this->WidthTotal, 0.2 * $this->WidthTotal));
        } else if($this->encola == 'resumen') {
            $this->SetFills(array('255,255,255', '255,255,255'));
            $this->SetTextColors(array('0,0,0', '0,0,0'));
            $this->SetHeights(array(0.38));
            $this->SetAligns(array('L', 'R'));
            $this->SetWidths(array(0.2* $this->WidthTotal, 0.2* $this->WidthTotal));
            $this->SetX(($this->WidthTotal * .6) + 1);
        }


    }



    public function titulos(){

        $this->SetFont('Arial', '', $this->txtSubtitleTam -1);

        //Detalles de la Asignación (Titulo)
        $this->SetFont('Arial', 'B', $this->txtSeccionTam);
        $this->SetXY($this->GetPageWidth() /2, 1);
        $this->Cell(0.5 * $this->WidthTotal, 0.7, utf8_decode('SOLICITUD DE CAMBIO AL PRESUPUESTO'), 'TRL', 0, 'C');

        $this->Cell(0.5);
        $this->Cell(0.5 * $this->WidthTotal, .7, '', 0, 1, 'L');

    }

    public function detallesAsignacion($x){
        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.35, utf8_decode('Obra:'), '', 0, 'LB');
        $this->SetFont('Arial', '', $this->txtContenidoTam);
        $this->CellFitScale(0.375 * $this->WidthTotal, 0.35,  utf8_decode($this->obra->nombre_obra_formatos), '', 1, 'L');

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.35, utf8_decode('Tipo de Solicitud:'), '', 0, 'LB');
        $this->SetFont('Arial', '', $this->txtContenidoTam);
        $this->CellFitScale(0.375 * $this->WidthTotal, 0.35, utf8_decode($this->solicitud->tipoOrden->descripcion), '', 1, 'L');

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.35, utf8_decode('Número de Folio:'), '', 0, 'LB');
        $this->SetFont('Arial', '', '#'.$this->txtContenidoTam);
        $this->CellFitScale(0.375 * $this->WidthTotal, 0.35, utf8_decode('#' . $this->solicitud->numero_folio), '', 1, 'L');

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.35, utf8_decode('Área Solicitante:'), '', 0, 'LB');
        $this->SetFont('Arial', '', '#'.$this->txtContenidoTam);
        $this->CellFitScale(0.375 * $this->WidthTotal, 0.35, utf8_decode($this->solicitud->area_solicitante), '', 1, 'L');


        $this->SetFont('Arial', 'B', '#'.$this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.35, utf8_decode('Fecha de Solicitud:'), '', 0, 'L');
        $this->SetFont('Arial', '', $this->txtContenidoTam);
        $this->CellFitScale(0.375 * $this->WidthTotal, 0.35, utf8_decode(Carbon::parse($this->solicitud->fecha_solicitud)->format('d-m-Y')), '', 1, 'L');

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.35, utf8_decode('Persona que Solicita:'), '', 0, 'L');
        $this->SetFont('Arial', '', $this->txtContenidoTam);
        $this->CellFitScale(0.375 * $this->WidthTotal, 0.35, utf8_decode($this->solicitud->usuario->nombre_completo), '', 1, 'L');

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.35, utf8_decode('Estatus:'), '', 0, 'L');
        $this->SetFont('Arial', '', $this->txtContenidoTam);
        $this->CellFitScale(0.375 * $this->WidthTotal, 0.35, utf8_decode(strtoupper($this->solicitud->estatus->descripcion)), '', 1, 'L');
    }

    public function partidas(){
        $estaSolicitudSuma = 0;
        $nivel_base = '';
        //Tabla
        $this->SetWidths(array(0));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('1,1,1'));
        $this->SetHeights(array(0));
        $this->SetFont('Arial', 'B', $this->txtSeccionTam);
        $this->SetTextColors(array('255,255,255'));

        $this->SetFont('Arial', '', 6);
        $this->SetStyles(array('DF', 'DF', 'FD', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF'));
        $this->SetFills(array('180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
        $this->SetHeights(array(0.28));
        $this->SetAligns(array('L', 'L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
        $this->SetWidths(array(0.03 * $this->WidthTotal, 0.47 * $this->WidthTotal, 0.07
            * $this->WidthTotal, 0.07 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal));
        $this->Row(array('#', utf8_decode("Descripción"), utf8_decode("Unidad"), utf8_decode("Precio Unitario"), utf8_decode("Volúmen Original"), utf8_decode("Volúmen del Cambio"), utf8_decode("Volúmen Actualizado"), utf8_decode("Importe Original"), utf8_decode("Importe del Cambio"), utf8_decode("Importe Actualizado") ));

        $this->SetFont('Arial', '', 6);
        $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
        $this->SetHeights(array(0.35));
        $this->SetAligns(array('L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
        $this->SetWidths(array(0.03 * $this->WidthTotal, 0.47 * $this->WidthTotal, 0.07
            * $this->WidthTotal, 0.07 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal, 0.06 * $this->WidthTotal));
        foreach($this->solicitud->variacionVolumenPartidas as $i => $partida){
            $nivel_base = substr($partida->nivel, 0,4);
            $conceptoBase = Concepto::where('id_concepto', '=', $partida->id_concepto)->first();
            $historico = SolicitudCambioPartidaHistorico::where('id_solicitud_cambio_partida', '=', $partida->id)
                        ->where('nivel', '=', $partida->nivel)
                        ->first();


                $variacion_volumen = $partida->cantidad_presupuestada_nueva - $partida->cantidad_presupuestada_original;


                    // Si ya existe el histórico, muestra esa info
                    if($historico) {
                        $cantidadPresupuestada =  $historico->cantidad_presupuestada_original;
                        $cantidadNueva =  $historico->cantidad_presupuestada_actualizada;
                        $monto_presupuestado = $historico->monto_presupuestado_original;
                        $monto_nuevo = $historico->monto_presupuestado_actualizado;
                        $variacion_volumen =  $historico->cantidad_presupuestada_actualizada - $historico->cantidad_presupuestada_original;
                        $variacion_importe =  ($historico->monto_presupuestado_actualizado - $historico->monto_presupuestado_original);
                    } else {
                        $divisor = $conceptoBase->cantidad_presupuestada == 0?1:$conceptoBase->cantidad_presupuestada;
                        $factor = ($conceptoBase->cantidad_presupuestada + $variacion_volumen) / $divisor;
                        $cantidadPresupuestada = $conceptoBase->cantidad_presupuestada;
                        $cantidadNueva = ($conceptoBase->cantidad_presupuestada * $factor);
                        $monto_presupuestado = $conceptoBase->monto_presupuestado;
                        $monto_nuevo = ($conceptoBase->monto_presupuestado * $factor);
                        $variacion_volumen = ($conceptoBase->cantidad_presupuestada * $factor) - $conceptoBase->cantidad_presupuestada;
                        $variacion_importe = ($conceptoBase->monto_presupuestado * $factor) - $conceptoBase->monto_presupuestado;
                    }

                    //Calcula total de esta solicitud
                    $estaSolicitudSuma += $variacion_importe;
                    $this->Row( [
                        $i +1,
                        utf8_decode($partida->concepto->descripcion),  // Descripción
                        utf8_decode($partida->unidad), // Unidad
                        '$ ' . number_format($partida->precio_unitario_original, 2, '.', ','), // Precio unitario
                        number_format($cantidadPresupuestada, 2, '.', ','), // Vólumen Original
                        number_format($variacion_volumen, 2, '.', ','), // Vólumen del cambio
                        number_format($cantidadNueva, 2, '.', ','),  // Vólumen actualizado
                        '$ '. number_format($monto_presupuestado, 2, '.', ','), // Importe original
                        '$ '. number_format($variacion_importe, 2, '.', ','), // Importe del cambio
                        '$ '. number_format($monto_nuevo, 2, '.', ','), // Importe actualizado
                    ]);

        }
        $this->encola = '';

        $concepto_obra = Concepto::where('nivel', '=', $nivel_base)->first();
        $nodo_tipo = NodoTipo::where('id_tipo_nodo', '=', 1)->where('id_concepto_proyecto', '=', $concepto_obra->id_concepto)->first();

        if(!$nodo_tipo){
            dd('La obra no tiene la configuracion de Costo Directo, favor de contactar a Soporte a Aplicaciones.');
        }
        $data_resumen = [
            'MONTO DEL COSTO DIRECTO' =>  number_format($historico ? $nodo_tipo->concepto->monto_presupuestado -  $estaSolicitudSuma : $nodo_tipo->concepto->monto_presupuestado, 2, '.', ','),
            'MONTO DE ESTA SOLICITUD' =>  number_format($estaSolicitudSuma, 2, '.', ','),
            'MONTO DEL COSTO DIRECTO ACTUALIZADO' => number_format(($historico ? $nodo_tipo->concepto->monto_presupuestado : $nodo_tipo->concepto->monto_presupuestado + $estaSolicitudSuma), 2, '.', ',')
        ];

        $this->resumen($data_resumen);

    }

    public function resumen($data){
        $this->Ln();
        $this->SetX(($this->WidthTotal * .6) + 1);
        $this->SetFont('Arial', '', 6);
        $this->SetStyles(array('DF', 'DF'));
        $this->SetFills(array('180,180,180', '180,180,180'));
        $this->SetTextColors(array('0,0,0', '0,0,0'));
        $this->SetHeights(array(0.38));
        $this->SetAligns(array('C', 'C'));
        $this->SetWidths(array(0.2 * $this->WidthTotal, 0.2 * $this->WidthTotal));
        $this->encola = 'resumen_h';
        $this->Row(array('Detalle', 'Cantidad'));
        $this->encola = 'resumen';
        $this->SetFills(array('255,255,255', '255,255,255'));
        $this->SetTextColors(array('0,0,0', '0,0,0'));
        //$this->SetHeights(array(0.38));
        $this->SetAligns(array('L', 'R'));
        $this->SetWidths(array(0.2 * $this->WidthTotal, 0.2 * $this->WidthTotal));

        foreach ($data as $nombre => $valor) {
            $this->SetX(($this->WidthTotal * .6) + 1);
            $this->Row([$nombre, '$'.$valor]);
        }
    }

    public function motivo(){

        $this->encola = "motivo";
        $this->SetWidths(array($this->WidthTotal));
        $this->SetFills(array('180,180,180'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(0.3));
        $this->SetFont('Arial', '', 6);
        $this->SetAligns(array('C'));
        $this->Row(array("Motivo"));
        $this->SetAligns(array('J'));
        $this->SetStyles(array('DF'));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(0.35));
        $this->SetFont('Arial', '', 6);
        $this->SetWidths(array($this->WidthTotal));
        $this->Row(array(utf8_decode($this->solicitud->motivo)));
     }

     public function firmas(){

        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);

        // $qr_name = 'qrcode_'. mt_rand() .'.png';
        // $renderer = new Png();
        // $renderer->setHeight(132);
        // $renderer->setWidth(132);
        // $renderer->setMargin(0);
        // $writer = new Writer($renderer);
        // $url = $_SERVER['SERVER_NAME'].':'. $_SERVER['SERVER_PORT'].'/api/control-presupuesto/variacion-volumen/'.$this->solicitud->id. '/formato-variacion-volumen?db='.Context::getDatabase().'&idobra='.Context::getIdObra().'&access_token='.config('app.env_variables.SERVICIO_CFDI_TOKEN');
        // $writer->writeFile($url , $qr_name);

        // $this->SetY($this->GetPageHeight() - 5);

        $qrX = $this->GetPageWidth() ;

        // $this->Image($qr_name, 1);
        // unlink($qr_name);

        $this->SetY($this->GetPageHeight() - 4);
        $firmasWidth = 6.5;
        $firmaX1 = ($qrX / 3) - ($firmasWidth / 2);
        $firmaX2 = ($qrX / 1.50) - ($firmasWidth / 2);

        $this->SetX($firmaX1);
        $this->Cell($firmasWidth, 0.4, utf8_decode('COORDINADOR DE CONTROL DE PROYECTOS'), 'TRLB', 0, 'C', 1);

        $this->SetX($firmaX2);
        $this->Cell($firmasWidth, 0.4, utf8_decode('PERSONA QUE SOLICITA'), 'TRLB', 1, 'C', 1);


        $this->SetX($firmaX1);
        $this->Cell($firmasWidth, 1.2, '', 'TRLB', 0, 'C');

        $this->SetX($firmaX2);
        $this->Cell($firmasWidth, 1.2, '', 'TRLB', 1, 'C');

        $this->SetX($firmaX1);
        $this->Cell($firmasWidth, 0.4, utf8_decode(''), 'TRLB', 0, 'C', 1);

        $this->SetX($firmaX2);
        $this->Cell($firmasWidth, 0.4, utf8_decode($this->solicitud->usuario->getNombreCompletoAttribute()), 'TRLB', 0, 'C', 1);
    }

     public function Footer() {
        $this->firmas();
        $this->SetFont('Arial', 'B', $this->txtFooterTam);
        $this->SetY($this->GetPageHeight() - 1);
        $this->SetFont('Arial', '', $this->txtFooterTam);
        $this->Cell(6.5, .4, utf8_decode(''), 0, 0, 'L');
        $this->SetFont('Arial', 'B', $this->txtFooterTam);
        $this->Cell(6.5, .4, '', 0, 0, 'C');
        $this->Cell(15, .4, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
        $this->SetY($this->GetPageHeight() - 1.3);
        $this->SetFont('Arial', 'B', $this->txtFooterTam);
        $this->Cell(6.5, .4, utf8_decode('Fecha de Consulta: ' . date('Y-m-d g:i a') . ' Formato generado desde SAO-ERP.'), 0, 0, 'L');
    }

    public function logo()
    {
        $data = $this->obra->getLogoAttribute();
        $data = pack('H*', hex2bin($data));
        $file = public_path('/img/logo_temp.png');
        if (file_put_contents($file, $data) !== false) {
            list($width, $height) = $this->resizeToFit($file);
            $this->Image($file, 1, 1, $width-2, $height-1);
            unlink($file);
        }
    }

    public function create()
    {
        $this->SetMargins(1, .5, 2);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 4);
        $this->partidas();
        $this->Ln(0.75);
        $this->motivo();

        try {
            $this->Output('I', 'Formato - Variación de Volumen.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }

    public function RotatedText($x,$y,$txt,$angle)
    {
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }

    public function pixelsToCM($val) {
        return ($val * self::MM_IN_INCH / self::DPI) / 10;
    }

    public function resizeToFit($imgFilename) {
        // dd($imgFilename);
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);

        return [
            round($this->pixelsToCM($scale * $width)),
            round($this->pixelsToCM($scale * $height))
        ];
    }
}
