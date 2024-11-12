<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 02:59 PM
 */

namespace App\PDF\Contratos;


use App\Facades\Context;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\Subcontratos\Contrato;
use App\Models\CADECO\Subcontratos\Subcontratos;
use App\Utils\NumberToLetterConverter;
use Ghidev\Fpdf\Rotation;
USE Illuminate\Support\Facades\App;

class OrdenPagoEstimacion extends Rotation
{

    protected $obra;
    protected $estimacion;
    protected $objeto_contrato;

    var $encola = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     * OrdenPagoEstimacion constructor.
     * @param $estimacion
     */
    public function __construct($estimacion)
    {
        parent::__construct('P', 'cm', 'A4');

        $this->obra = Obra::find(Context::getIdObra());
        $this->estimacion = Estimacion::find($estimacion);

        $this->objeto_contrato = Subcontratos::find($this->estimacion['id_antecedente']);

        if(!$this->objeto_contrato){
            $this->objeto_contrato = null;
        }else {
            if ($this->objeto_contrato['observacion'] == null) {
                $subcontrato_transaccion = Subcontrato::find( $this->estimacion['id_antecedente'] );

                // Si existe el campo referencia, úsalo.
                if ($subcontrato_transaccion['referencia'])
                    $this->objeto_contrato = $subcontrato_transaccion['referencia'];

                // ¿No? obten la referencia del contrato proyectado
                else {
                    $contrato_proyectado = ContratoProyectado::find( $subcontrato_transaccion['id_antecedente'] );

                    $this->objeto_contrato = $contrato_proyectado->referencia;
                }
            } else
                $this->objeto_contrato = $this->objeto_contrato['observacion'];
        }
    }

    function Header() {

        $this->logo();
        $this->detalles();
        $this->Ln(1);

        if($this->encola == 'partidas') {
            $this->SetFont('Arial', '', 7);
            $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF']);
            $this->SetWidths([
                ($this->w - 2) * 0.07,
                ($this->w - 2) * 0.30,
                ($this->w - 2) * 0.15,
                ($this->w - 2) * 0.28,
                ($this->w - 2) * 0.05,
                ($this->w - 2) * 0.15,
            ]);

            $this->SetFills(['180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            $this->SetHeights([0.5]);
            $this->SetAligns(['C', 'C', 'C', 'C', 'C', 'C']);
            $this->Row(['Partida',utf8_decode('Descripción'),'Importe', utf8_decode('Aplicación de Costo'), '%','Cuenta']);

            $this->SetFont('Arial', '', 7);
            $this->SetWidths([
                ($this->w - 2) * 0.07,
                ($this->w - 2) * 0.30,
                ($this->w - 2) * 0.15,
                ($this->w - 2) * 0.28,
                ($this->w - 2) * 0.05,
                ($this->w - 2) * 0.15,
            ]);

            $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            $this->SetHeights([0.35]);
            $this->SetAligns(['L', 'L', 'R', 'L', 'L', 'L']);
        }
    }

    function Footer() {
        // if (!App::environment('production')) {
        //     $this->SetFont('Arial','B',80);
        //     $this->SetTextColor(155,155,155);
        //     $this->RotatedText(5,20,utf8_decode("MUESTRA"),45);
        //     $this->RotatedText(6,26,utf8_decode("SIN VALOR"),45);
        //     $this->SetTextColor('0,0,0');
        // }
        $this->firmas();
        $this->SetY($this->GetPageHeight() - 1);
        $this->SetFont('Arial', '', 6);
        $this->Cell(6.5, .4, utf8_decode('Formato generado desde ERP SAO.'), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 6);
        $this->Cell(6.5, .4, '', 0, 0, 'C');
        $this->Cell(6.5, .4, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');

    }

    function logo() {
        $data = $this->obra->getLogoAttribute();
        $data = pack('H*', hex2bin($data));
        $file = public_path('/img/logo_temp.png');
        if (file_put_contents($file, $data) !== false) {
            list($width, $height) = $this->resizeToFit($file);
            $this->Image($file, 1, 2, $width, $height);
            unlink($file);
        }
    }

    function detalles() {
        $this->SetXY(6, 1.5);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(4, 0.4, '', 0, 0);
        $this->CellFitScale(10, 0.4,  utf8_decode($this->obra->nombre_obra_formatos), 0, 1, 'C');
        $this->ln(0.25);

        $this->SetX(6);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(4, 0.4, '', 0, 0);
        $this->CellFitScale(10, 0.4, 'ORDEN DE PAGO No.', 0, 1, 'L');
        $this->Ln(0.25);

        $this->SetX(6);
        $this->SetFont('Arial', '', 10);
        $this->Cell(4, 0.4, 'Subcontrato No.:', 0, 0, 'R');
        $this->CellFitScale(10, 0.4, $this->estimacion->subcontrato->referencia, 'B', 1, 'C');
        $this->Ln(0.1);

        $this->SetX(6);
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, 1, 'Objeto del Contrato:', 0, 0, 'R');
        $this->MultiCell(10, 0.5, $this->objeto_contrato, 1, 'C');
        $this->Ln(0.1);

        $this->SetX(6);
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, 0.4, 'Fecha :', 0, 0, 'R');
        $this->CellFitScale(10, 0.4, date("d/m/Y", strtotime($this->estimacion->fecha)), 'B', 1, 'C');
        $this->Ln(0.1);

        $this->SetX(6);
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, 0.4, 'Monto del Contrato:', 0, 0, 'R');
        $this->CellFitScale(10, 0.4,$this->estimacion->subcontrato->subtotal_format, 'B', 1, 'C');
        $this->Ln(0.1);

        $this->SetX(6);
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, 0.35, 'Contratista :', 0, 0, 'R');
        $this->CellFitScale(10, 0.35, $this->estimacion->subcontrato->empresa->razon_social, 'B', 1, 'C');
        $this->Ln(0.1);

        $this->SetX(6);
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, 0.35, utf8_decode('Estimación :'), 0, 0, 'R');
        $this->multicell(10, 0.35, utf8_decode("#".$this->estimacion->numero_folio . " - " . $this->estimacion->observaciones), 'B', 'J', 0);
        $this->Ln(0.1);

        $this->SetX(6   );
        $this->SetFont('Arial', '', 8);
        $this->Cell(4, 0.35, 'Periodo :', 0, 0, 'R');
        $this->CellFitScale(5, 0.35, 'Del : ' .  date("d/m/Y", strtotime($this->estimacion->cumplimiento)), 'B', 0, 'C');
        $this->CellFitScale(5, 0.35, 'Al : ' .  date("d/m/Y", strtotime($this->estimacion->vencimiento)), 'B', 1, 'C');

        $this->Ln();
    }

    function partidas()
    {
        $this->SetFont('Arial', '', 7);
        $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF']);
        $this->SetWidths([
            ($this->w - 2) * 0.07,
            ($this->w - 2) * 0.30,
            ($this->w - 2) * 0.15,
            ($this->w - 2) * 0.28,
            ($this->w - 2) * 0.05,
            ($this->w - 2) * 0.15,
        ]);

        $this->SetFills(['180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.5]);
        $this->SetAligns(['C', 'C', 'C', 'C', 'C', 'C']);


        $this->Row(['Partida',utf8_decode('Descripción'),'Importe', utf8_decode('Aplicación de Costo'), '%','Cuenta']);
        foreach ($this->estimacion->partidas as $item) {
            if($item->importe != 0) {
                $this->SetFont('Arial', '', 7);
                $this->SetWidths([
                    ($this->w - 2) * 0.07,
                    ($this->w - 2) * 0.30,
                    ($this->w - 2) * 0.15,
                    ($this->w - 2) * 0.28,
                    ($this->w - 2) * 0.05,
                    ($this->w - 2) * 0.15,
                ]);

                $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
                $this->SetHeights([0.35]);
                $this->SetAligns(['L', 'L', 'R', 'L', 'L', 'L']);
                $this->encola = 'partidas';
                $this->Row(['', utf8_decode($item->contrato->descripcion), '$ ' . number_format($item->importe, 2, '.', ','), utf8_decode($item->concepto && strlen($item->concepto->nivel) / 4 > 1 ? $item->concepto->padre() : $item->concepto->nivel_padre()), '', '']);
            }
        }

        $this->SetFont('Arial', '', 7);
        $this->SetWidths([
            ($this->w - 2) * 0.37,
            ($this->w - 2) * 0.15,
            ($this->w - 2) * 0.48
        ]);

        $this->SetFills(['255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.35]);
        $this->SetAligns(['R', 'R', 'R']);

        $this->encola = 'total_partidas';
        $this->Row(['Importe Total :','$ ' . number_format($this->estimacion->suma_importes, 2, '.', ','),'']);

        $this->encola = '';
    }

    function seguimiento()
    {
        $this->encola = 'seguimiento_header';

        $this->SetFont('Arial', '', 7);
        $this->SetStyles(['DF']);
        $this->SetWidths([$this->w - 2]);
        $this->SetFills(['180,180,180']);
        $this->SetTextColors(['0,0,0']);
        $this->SetHeights([0.5]);
        $this->SetAligns(['C']);
        $this->Row(['Seguimiento del Anticipo']);

        $this->SetWidths([
            ($this->w - 2) * 0.25,
            ($this->w - 2) * 0.25,
            ($this->w - 2) * 0.25,
            ($this->w - 2) * 0.25
        ]);
        $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetAligns(['C', 'C', 'C', 'C']);
        $this->SetHeights([0.35]);
        $this->Row([
            'Monto Anticipo',
            utf8_decode('Amortización Pendiente Anterior'),
            utf8_decode('Amortización de esta Estimación'),
            utf8_decode('Amortización Pendiente')
        ]);
        $this->SetAligns(['R', 'R', 'R', 'R']);
        $this->Row([
            '$ ' . number_format($this->estimacion->subcontrato->anticipo_monto, 2, '.', ','),
            '$ ' . number_format($this->estimacion->amortizacion_pendiente_anterior, 2, '.', ','),
            '$ ' . number_format($this->estimacion->monto_anticipo_aplicado, 2, '.', ','),
            '$ ' . number_format($this->estimacion->amortizacion_pendiente, 2, '.', ',')
        ]);
    }

    function totales() {
        $y_inicial = $this->GetY();

        $this->SetX(($this->w) * 0.45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.30, 0.4, utf8_decode('Importe Estimación :'), 0, 0, 'R');
        $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->suma_importes_format, 'B', 1, 'R');
        $this->Ln(0.1);

        $this->SetX(($this->w) * 0.45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.30, 0.4, 'Amortizacion de Anticipo :', 0, 0, 'R');
        $this->CellFitScale(($this->w - 2) * 0.10, 0.4, round($this->estimacion->anticipo, 2) . ' %', 'B', 0, 'L');
        $this->CellFitScale(($this->w - 2) * 0.15, 0.4, $this->estimacion->monto_anticipo_aplicado_format, 'B', 1, 'R');
        $this->Ln(0.1);

        if($this->estimacion->configuracion->ret_fon_gar_antes_iva==1) {
            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, utf8_decode('Retención de Fondo de Garantia Estimación :'), 0, 0, 'R');
            $this->CellFitScale(($this->w - 2) * 0.10, 0.4, round($this->estimacion->retencion, 2) . ' % ', 'B', 0, 'L');
            $this->CellFitScale(($this->w - 2) * 0.15, 0.4,$this->estimacion->retencion_fondo_garantia_orden_pago_format, 'B', 1, 'R');
            $this->Ln(0.1);
        }

        if($this->estimacion->configuracion->retenciones_antes_iva==1) {

            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total Retenciones :', 0, 0, 'R');
            $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->suma_retenciones_format, 'B', 1, 'R');
            $this->Ln(0.1);

            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total Retenciones Liberadas :', 0, 0, 'R');
            $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->suma_liberaciones_format, 'B', 1, 'R');
            $this->Ln(0.1);

        }

        if($this->estimacion->configuracion->desc_pres_mat_antes_iva == 1) {
            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total Deductivas :', 0, 0, 'R');
            $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->suma_deductivas_format, 'B', 1, 'R');
            $this->Ln(0.1);
        }

        if($this->estimacion->configuracion->penalizacion_antes_iva == 1)
        {
            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total Penalizaciones :', 0, 0, 'R');
            $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->suma_penalizaciones_format, 'B', 1, 'R');
            $this->Ln(0.1);

            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total Penalizaciones Liberadas:', 0, 0, 'R');
            $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->suma_penalizaciones_liberadas_format, 'B', 1, 'R');
            $this->Ln(0.1);
        }

        /*
         *  INICIA SUBTOTAL
         * */
        $this->SetX(($this->w) * 0.45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.30, 0.4, 'Subtotal :', 0, 0, 'R');
        $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->subtotal_orden_pago_format, 'B', 1, 'R');
        $this->Ln(0.1);
        /*
         *  FINALIZA SUBTOTAL
         * */

        $this->SetX(($this->w) * 0.45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.30, 0.4, 'IVA ('.$this->estimacion->tasa_iva_format.'%):', 0, 0, 'R');
        $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->iva_orden_pago_format, 'B', 1, 'R');
        $this->Ln(0.1);

        $this->SetX(($this->w) * 0.45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.30, 0.4, utf8_decode('Retención IVA :'), 0, 0, 'R');
        $this->CellFitScale(($this->w - 2) * 0.10, 0.4, $this->estimacion->iva_retenido_porcentaje, 'B', 0, 'L');
        $this->CellFitScale(($this->w - 2) * 0.15, 0.4, $this->estimacion->iva_retenido_format, 'B', 1, 'R');
        $this->Ln(0.1);

        $this->SetX(($this->w) * 0.45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.30, 0.4, utf8_decode('Retención ISR :'), 0, 0, 'R');
        $this->CellFitScale(($this->w - 2) * 0.10, 0.4, $this->estimacion->porcentaje_isr_retenido_format, 'B', 0, 'L');
        $this->CellFitScale(($this->w - 2) * 0.15, 0.4, $this->estimacion->monto_isr_retenido_format, 'B', 1, 'R');
        $this->Ln(0.1);


        $this->SetX(($this->w) * 0.45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total :', 0, 0, 'R');
        $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->total_orden_pago_format, 'B', 1, 'R');
        $this->Ln(0.1);

        if($this->estimacion->configuracion->ret_fon_gar_antes_iva==0) {
            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, utf8_decode('Retención de Fondo de Garantia Estimación :'), 0, 0, 'R');
            if($this->estimacion->configuracion->ret_fon_gar_con_iva==1) {
                $this->CellFitScale(($this->w - 2) * 0.10, 0.4, round($this->estimacion->retencion, 2) . ' % + IVA', 'B', 0, 'L');
            }
            if($this->estimacion->configuracion->ret_fon_gar_con_iva==0) {
                $this->CellFitScale(($this->w - 2) * 0.10, 0.4, round($this->estimacion->retencion, 2) . ' %', 'B', 0, 'L');
            }

            $this->CellFitScale(($this->w - 2) * 0.15, 0.4,$this->estimacion->retencion_fondo_garantia_orden_pago_format, 'B', 1, 'R');
            $this->Ln(0.1);
        }

        $this->SetX(($this->w) * 0.45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.30, 0.4, '', 0, 0, 'R');
        $this->CellFitScale(($this->w - 2) * 0.25, 0.4, '', 'B', 1, 'C');
        $this->Ln(0.1);

        if($this->estimacion->configuracion->desc_pres_mat_antes_iva==0) {
            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total Deductivas :', 0, 0, 'R');
            $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->suma_deductivas_format, 'B', 1, 'R');
            $this->Ln(0.1);
        }
        if($this->estimacion->configuracion->retenciones_antes_iva==0) {

            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total Retenciones :', 0, 0, 'R');
            $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->suma_retenciones_format, 'B', 1, 'R');
            $this->Ln(0.1);


            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total Retenciones Liberadas :', 0, 0, 'R');
            $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->suma_liberaciones_format, 'B', 1, 'R');
            $this->Ln(0.1);
        }

        if($this->estimacion->configuracion->penalizacion_antes_iva == 0)
        {
            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total Penalizaciones :', 0, 0, 'R');
            $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->suma_penalizaciones_format, 'B', 1, 'R');
            $this->Ln(0.1);

            $this->SetX(($this->w) * 0.45);
            $this->SetFont('Arial', '', 8);
            $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total Penalizaciones Liberadas:', 0, 0, 'R');
            $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->suma_penalizaciones_liberadas_format, 'B', 1, 'R');
            $this->Ln(0.1);
        }

        $this->SetX(($this->w) * 0.45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total Anticipo a Liberar :', 0, 0, 'R');
        $this->CellFitScale(($this->w - 2) * 0.25, 0.4, $this->estimacion->anticipo_a_liberar_format, 'B', 1, 'R');
        $this->Ln(0.1);

        $this->SetX(($this->w) * 0.45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.30, 0.4, 'Total de la Orden de Pago :', 0, 0, 'R');
        $this->CellFitScale(($this->w - 2) * 0.25, 0.4, "$ ".number_format($this->estimacion->monto_a_pagar, 2, '.', ','), 'B', 1, 'R');
        $this->Ln(0.1);

        $this->SetX(($this->w) * 0.45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.30, 0.4, 'Importe con letra :', 0, 0, 'R');

        if($this->estimacion->monto_a_pagar != '0.0') {
            $this->MultiCell(($this->w - 2) * 0.25, 0.35, utf8_decode(strtoupper((new NumberToLetterConverter())->num2letras(round($this->estimacion->monto_a_pagar, 2), 0, 1, $this->estimacion->id_moneda))), 1, 1, 'L');
        }else{
            $this->MultiCell(($this->w - 2) * 0.25, 0.35,"CERO", 1, 1, 'L');
        }
        $y_final = $this->GetY();

        $this->SetY($y_inicial + (($y_final - $y_inicial) / 2) - 1);

        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.21, 0.4, 'Acumulado Retenido Anterior :', 0, 0, 'L');
        $this->CellFitScale(($this->w - 2) * 0.125, 0.4, '$ ' .  number_format($this->estimacion->retenido_anterior, 2, '.', ','), 'B', 1, 'R');

        $this->SetFont('Arial', '', 8);
        $this->Cell(($this->w - 2) * 0.21, 0.4, 'Retenido Origen :', 0, 0, 'L');
        $this->CellFitScale(($this->w - 2) * 0.125, 0.4, '$ ' .  number_format($this->estimacion->retenido_origen, 2, '.', ','), 'B', 1, 'R');

        $this->SetY($y_final);

        $this->Ln(0.1);
    }

    function pixelsToCM($val) {
        return ($val * self::MM_IN_INCH / self::DPI) / 10;
    }

    function resizeToFit($imgFilename) {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);
        return [
            round($this->pixelsToCM($scale * $width)),
            round($this->pixelsToCM($scale * $height))
        ];
    }

    function firmas() {
        $this->SetY(- 3.5);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);

        // Firmas específicas para la pista
        if (Context::getDatabase() == "SAO1814_PISTA_AEROPUERTO")
        {
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Realizó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);

            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Recibió'), 'TRLB', 1, 'C', 1);

            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.6, '', 'RL', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.6, '', 'RL', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.6, '', 'RL', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.6, '', 'RL', 1, 'C');

            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.6, '', 'RL', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.6, utf8_decode('C.P. Eleazar Ortega Valerio'), 'RL', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.6, utf8_decode('Ing. Victor Manuel Orozco Muñoz'), 'RL', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.6, utf8_decode('C.P. Miguel Ángel. Figueroa Vidal'), 'RL', 1, 'C');

            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('RESPONSABLE DE AREA'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('ADMINISTRADOR'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('GERENTE DE PROYECTO'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('TESORERO'), 'TRLB', 0, 'C', 1);
        }

        // Firmas para tunel drenaje profundo.
      /*  if (Context::getDatabase() == "SAO1814_TUNEL_DRENAJE_PRO")
        {
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Realizó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Recibió'), 'TRLB', 1, 'C', 1);

            $this->Cell(($this->GetPageWidth() - 4) / 4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 1.2, '', 'TRLB', 1, 'C');

            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, 'RESPONSABLE DE AREA', 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, 'SUPERINTENDENTE DE AREA', 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, 'GERENTE DE AREA', 'TRLB', 0, 'C', 1);
            $this->Cell(0.73);
            $this->Cell(($this->GetPageWidth() - 4) / 4, 0.4, 'ADMINISTRACION', 'TRLB', 0, 'C', 1);
            $this->Ln();
            $this->CellFitScale(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Ing. Guadalupe Moreno Hernandez'), 'TRLB', 0, 'C', 1);
            $this->CellFitScale(0.73);
            $this->CellFitScale(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Ing. Martin Morales Sanchez o Ing. Lazaro Romero Zamora'), 'TRLB', 0, 'C', 1);
            $this->CellFitScale(0.73);
            $this->CellFitScale(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('Ing. Francisco Javier Bay Ortuzar'), 'TRLB', 0, 'C', 1);
            $this->CellFitScale(0.73);
            $this->CellFitScale(($this->GetPageWidth() - 4) / 4, 0.4, utf8_decode('C.P. Andres Moreno Ayala'), 'TRLB', 0, 'C', 1);
        }
      */else if(Context::getDatabase() == "SAO1814_PRESA_LIBERTAD" && Context::getIdObra() == 6 && $this->estimacion->origenAcarreos)
        {
            /*Firmas Acarreos Presa Libertad*/
            $this->Cell(0.1);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 2) / 6, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(0.1);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 2) / 6, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 1.2, '', 'TRL', 0, 'C');
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 1.2, '', 'TRL', 0, 'C');
            $this->SetFont('Arial', 'B', 5);

            $this->Ln();
            $this->Cell(0.1);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode("Ing. Anayeli Marcial Basurto"), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 2) / 6, 0.4, utf8_decode('Ing. Jonathan Vergara Sánchez'), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('C.P. Luis Alberto García Chaires'), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Ing. Jose María Cota'), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Ing. Edgar J. Rodríguez Gómez'), 'RLB', 0, 'C', 0);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Ing. Miguel Angel Bonola Chacon'), 'RLB', 0, 'C', 0);

            $this->Ln();
            $this->Cell(0.1);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Responsable de Subcontratos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 2) / 6, 0.4, utf8_decode('Responsable de Control de Proyectos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Responsable de Administración'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Responsable de Construcción'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Jefe de Trituración'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.2);
            $this->Cell(($this->GetPageWidth() - 3) / 6, 0.4, utf8_decode('Responsable del Proyecto'), 'TRLB', 0, 'C', 1);
        }
        else if(Context::getDatabase() == "SAO1814_CIRCUITO" && (Context::getIdObra() == 7 || Context::getIdObra() == 8)) {
            /*Firmas en CEVASG E INFRASG*/
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Elabora'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('VoBo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Recibe'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Autoriza'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');

            $this->SetFont('Arial', 'B', 6);
            $this->Ln();
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Subcontratos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Coordinador Técnico'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Gerente Administrativo'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Gerente de Proyecto'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.5);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Director de Proyecto'), 'TRLB', 0, 'C', 1);

        } else if(Context::getDatabase() == "SAO1814_CUTZAMALA" && Context::getIdObra() == 6)
        {
            $this->Cell(0.1);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(0.1);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');

            $this->SetFont('Arial', 'B', 5);
            $this->Ln();
            $this->Cell(0.1);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Responsable de Subcontratos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Responsable de Control de Proyectos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Responsable de Administración'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Gerencia Técnica / Ingeniería'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.45);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Responsable del Proyecto'), 'TRLB', 0, 'C', 1);
        }
        else
        {
            $this->Cell(1);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.9);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.9);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.9);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);

            $this->Ln();
            $this->Cell(1);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.9);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.9);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');
            $this->Cell(0.9);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 1.2, '', 'TRLB', 0, 'C');

            $this->SetFont('Arial', 'B', 5);
            $this->Ln();
            $this->Cell(1);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Responsable de Subcontratos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.9);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Responsable de Control de Proyectos'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.9);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Responsable de Administración'), 'TRLB', 0, 'C', 1);
            $this->Cell(0.9);
            $this->Cell(($this->GetPageWidth() - 4) / 5, 0.4, utf8_decode('Responsable del Proyecto'), 'TRLB', 0, 'C', 1);
        }
    }

    function create() {
        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,3.75);
        $this->partidas();
        $this->Ln();
        $this->seguimiento();
        if($this->y > 17.05)
            $this->AddPage();
        $this->Ln(1);
        $this->totales();
        try {
            $this->Output('I', 'Formato - Orden de Pago Estimación.pdf', 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }
}
