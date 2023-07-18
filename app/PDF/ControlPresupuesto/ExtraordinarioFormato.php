<?php


namespace App\PDF\ControlPresupuesto;


use App\Models\CADECO\ControlPresupuesto\Extraordinario;
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

class ExtraordinarioFormato extends Rotation
{
    var $encola = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 100;

    protected $partidas;
    protected $resumen;
    protected $solicitud;
    protected $obra;

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

        $this->solicitud = Extraordinario::find($id);
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
            $this->SetFont('Arial', '', 8);
            $this->SetStyles(array('DF', 'DF', 'DF', 'FD', 'DF', 'DF'));
            $this->SetFills(array('180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180'));
            $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
            $this->SetHeights(array(0.5));
            $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
            $this->SetWidths(array(0.02 * $this->WidthTotal, 0.6 * $this->WidthTotal, 0.08 * $this->WidthTotal, 0.1 * $this->WidthTotal, 0.1 * $this->WidthTotal, 0.1 * $this->WidthTotal));
            $this->Row(array('#', utf8_decode("Descripción"), utf8_decode("Unidad"),  utf8_decode("Volumen"), "Costo", utf8_decode("Importe")));

            $this->SetFont('Arial', '', 6);
            $this->setFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
            $this->setTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
            $this->setHeights([0.5]);
            $this->setAligns(['C','L','C','C','R','R']);
            $this->SetWidths(array(0.02 * $this->WidthTotal, 0.6 * $this->WidthTotal, 0.08 * $this->WidthTotal, 0.1 * $this->WidthTotal, 0.1 * $this->WidthTotal, 0.1 * $this->WidthTotal));
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

        $this->SetFont('Arial', '', $this->txtSubtitleTam - 1);

        //Detalles de la Asignación (Titulo)
        $this->SetFont('Arial', 'B', $this->txtSeccionTam);
        $this->SetXY($this->GetPageWidth() / 2, 1);
        $this->Cell(0.5 * $this->WidthTotal, 0.7, utf8_decode('SOLICITUD DE CAMBIO AL PRESUPUESTO'), 'TRL', 0, 'C');

        $this->Cell(0.5);
        $this->Cell(0.5 * $this->WidthTotal, .7, '', 0, 1, 'L');

    }

    public function detallesAsignacion($x){
        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.35, utf8_decode('Obra:'), '', 0, 'LB');
        $this->SetFont('Arial', '', $this->txtContenidoTam);
        $this->CellFitScale(0.375 * $this->WidthTotal, 0.35,  $this->obra->descripcion != null ?utf8_decode($this->obra->descripcion) :  $this->obra->nombre, '', 1, 'L');

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
        $this->CellFitScale(0.375 * $this->WidthTotal, 0.35, utf8_decode($this->solicitud->usuario->getNombreCompletoAttribute()), '', 1, 'L');

        $this->SetFont('Arial', 'B', $this->txtContenidoTam);
        $this->SetX($x);
        $this->Cell(0.125 * $this->WidthTotal, 0.35, utf8_decode('Estatus:'), '', 0, 'L');
        $this->SetFont('Arial', '', $this->txtContenidoTam);
        $this->CellFitScale(0.375 * $this->WidthTotal, 0.35, utf8_decode(strtoupper($this->solicitud->estatus->descripcion)), '', 1, 'L');
    }

    public function partidas(){
        $this->encola = "partidas";
        $this->SetFont('Arial', 'B', $this->txtSeccionTam);
        $this->SetXY($this->GetX(), $this->GetY());
        $this->Cell($this->WidthTotal, 0.7, utf8_decode('CONCEPTO RAÍZ'), 'TRLB', 1, 'C');

        $this->SetWidths(array($this->WidthTotal));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('1,1,1'));
        $this->SetHeights(array(0.5));
        $this->SetFont('Arial', '', 8);
        $this->Row(Array($this->solicitud->conceptoRaiz->path));
        $this->ln(0.5);



        $this->SetFont('Arial', 'B', $this->txtSeccionTam);
        $this->Cell($this->WidthTotal, 0.7, utf8_decode('CONCEPTO EXTRAORDINARIO'), 'TRLB', 1, 'C');


        $this->SetFont('Arial', '', 8);
        $this->SetStyles(array('DF', 'DF', 'DF', 'FD', 'DF', 'DF'));
        $this->SetFills(array('180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
        $this->SetHeights(array(0.5,0.5,0.5,0.5,0.5,0.5));
        $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
        $this->SetWidths(array(0.02 * $this->WidthTotal, 0.6 * $this->WidthTotal, 0.08 * $this->WidthTotal, 0.1 * $this->WidthTotal, 0.1 * $this->WidthTotal, 0.1 * $this->WidthTotal));
        $this->Row(array('#', utf8_decode("Descripción"), utf8_decode("Unidad"),  utf8_decode("Volumen"), "Costo", utf8_decode("Importe")));

        $this->setFont('Arial', '', 8);
        $this->SetWidths(array(0.02 * $this->WidthTotal, 0.6 * $this->WidthTotal, 0.08 * $this->WidthTotal, 0.1 * $this->WidthTotal, 0.1 * $this->WidthTotal, 0.1 * $this->WidthTotal));
        $this->setFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
        $this->setTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
        $this->setHeights([0.5]);
        $this->setAligns(['C','L','L','R','R','R']);

        $longitud_nivel_agrupadores = 0;

        foreach ($this->solicitud->partidas as $key=>$partida) {
            if($partida->monto_presupuestado>0){
                $this->setAligns(['C','L','L','R','R','R']);

                if($partida->unidad != '' && !$partida->id_material){
                    $this->setFills(['200,200,200','200,200,200','200,200,200','200,200,200','200,200,200','200,200,200']);
                    $longitud_nivel_agrupadores = strlen($partida->nivel)+4;
                }else if($longitud_nivel_agrupadores == strlen($partida->nivel)){
                    $this->setFont('Arial', 'B', 8);
                    $this->setFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
                }else{
                    $this->setFont('Arial', '', 8);
                    $this->setFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
                }

                try{
                    $this->row([
                        $key + 1,
                        utf8_decode($partida->descripcion_format),
                        utf8_decode($partida->unidad == ""?"-":$partida->unidad),
                        $partida->cantidad_format,
                        $partida->precio_unitario_format,
                        $partida->monto_presupuestado_format
                    ]);
                }catch (\Exception $e){
                    dd($e,[
                        $key + 1,
                        utf8_decode($partida->descripcion_format),
                        utf8_decode($partida->unidad == ""?"-":$partida->unidad),
                        $partida->cantidad_format,
                        $partida->precio_unitario_format,
                        $partida->monto_presupuestado_format,
                    ]);
                }
            }
        }
    }

    public function resumen(){
        if($this->getY()>16){
            $this->addPage();
        }
        $this->SetX(1);
        $this->ln(0.5);
        $this->SetFont('Arial', '', 8);
        $this->SetStyles(array('DF', 'DF','DF','DF'));
        $this->SetFills(array('180,180,180', '180,180,180','180,180,180','180,180,180'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetAligns(array('C', 'C','C','C'));
        $this->SetWidths(array(0.2 * $this->WidthTotal, 0.2 * $this->WidthTotal, 0.2 * $this->WidthTotal, 0.2 * $this->WidthTotal));
        $this->Row(array('Monto del Presupuesto', 'Monto del Extraordinario', 'Monto del Presupuesto Actualizado', '% del Cambio'));

        $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetAligns(array('R', 'R', 'R', 'R'));
        $this->SetWidths(array(0.2 * $this->WidthTotal, 0.2 * $this->WidthTotal,0.2 * $this->WidthTotal,0.2 * $this->WidthTotal));

        $this->SetX(1);
        $this->Row([$this->solicitud->importe_original_format,
            $this->solicitud->importe_afectacion_format,$this->solicitud->importe_actualizado_format,$this->solicitud->porcentaje_cambio_format]);
    }

    public function motivo(){
        $this->ln(0.5);

        $this->encola = "motivo";
        $this->SetWidths(array($this->WidthTotal));
        $this->SetFills(array('180,180,180'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetFont('Arial', '', 8);
        $this->SetAligns(array('C'));
        $this->Row(array("Motivo"));
        $this->SetAligns(array('J'));
        $this->SetStyles(array('DF'));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetFont('Arial', '', 8);
        $this->SetWidths(array($this->WidthTotal));
        $this->Row(array(utf8_decode($this->solicitud->motivo)));
     }

     public function firmas(){

        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);

        $qrX = $this->GetPageWidth() ;


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
        $this->Cell(6.5, .4, utf8_decode('Fecha de Consulta: ' . date('d-m-Y H:i:s') . ' Formato generado desde el Sistema de Control de Presupuesto del SAO ERP.'), 0, 0, 'L');
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
        $this->resumen();
        $this->motivo();

        try {
            $this->Output('I', 'Formato - Solicitud de Concepto Extraordinario.pdf', 1);
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
