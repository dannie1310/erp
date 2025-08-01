<?php

namespace App\PDF\ControlRecursos;

use App\Models\CONTROL_RECURSOS\Comprobacion;
use App\Models\CONTROL_RECURSOS\CuentaEmpresa;
use App\Models\CONTROL_RECURSOS\GrupoTipoGastoComp;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;

class ComprobacionGastosFormato extends Rotation
{
    private $encabezado_pdf = '';
    private $comprobacion = null;
    private $deducibles = null;
    private $no_deducibles = null;
    private $duracion = 0;
    protected $vale;
    var $encola = "";
    const vy = 0;

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(Comprobacion $comprobacion)
    {
        parent::__construct('P', 'cm', 'Letter');
        $this->comprobacion = $comprobacion;
        $this->WidthTotal = $this->GetPageWidth() - 0.82;
        $this->encola = '';
        if(($this->comprobacion->SerieVD=="") ||($this->comprobacion->FolioVD==0))
        {
            $this->vale = " - SIN VALE RELACIONADO - ";
        }
        else
        {
            $this->vale = $this->comprobacion->SerieVD." - ".$this->comprobacion->FolioVD;
        }
        $this->duracion = $this->comprobacion->duracion_viaje;
    }

    function Header()
    {
        $this->Image(public_path('/img/logo_hc.png'), 1, .5, 2.5, 1.5);

        //REDEFINICION DEL TAMAÑO
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(255,255,255);
        $this->Cell(12.5,.5,' ',0,0,'C');
        $this->SetFillColor(0,0,0);
        $this->Cell(4,.5,'SERIE',1,0,'C',1);
        $this->Cell(4,.5,'FOLIO',1,1,'C',1);
        $this->SetFillColor(204,204,204);
        $this->SetTextColor(0,0,0);

        $this->Cell(3, .5, ' ', 0, 0, 'C');

        $this->SetFont('Arial', 'BU', 12);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(9.5,.5,utf8_decode('COMPROBACIÓN DE GASTOS'),0,0,'C');

        //REDEFINICION DEL TAMAÑO
        $this->SetFont('Arial','B',12);
        $this->SetTextColor(0,0,0);
        $this->Cell(4,.5,$this->comprobacion->Serie,1,0,'C',1);
        $this->Cell(4,.5,$this->comprobacion->Folio,1,1,'C',1);
        $this->Cell(19.5,1,'',0,1,'C');

        $this->tablaFechas();

        if($this->encola == "documentos")
        {
            $this->encabezadosDoctos();
        }

        $currentPage = $this->PageNo();
        if($currentPage>1){
            $this->Ln();
        }
    }

    public function tablaFechas()
    {
        $this->encola = "documentos";
        $this->SetFont('Arial','',9);
        $this->SetFillColor(0,0,0);
        $this->SetTextColor(255,255,255);
        $this->Cell(8.5,.4,' ',0,0,'C');
        $this->Cell(3.5,.4,'FECHA',1,0,'C',1);
        $this->Cell(8.5,.4,'VALE DEFINITIVO RELACIONADO',1,1,'C',1);
        $this->SetFillColor(204,204,204);
        $this->SetTextColor(0,0,0);

        $this->SetFont('Arial','B',9);

        $this->Cell(8.5,.4,' ',0,0,'C');
        $this->Cell(3.5,.4,$this->comprobacion->fecha_format,1,0,'C',1);
        $this->Cell(8.5,.4,$this->vale,1,1,'C',1);

        $this->SetFont('Arial','B',12);

        $this->Cell(20.5,.3,' ',0,1,'C');

        $this->SetFillColor(0,0,0);
        $this->SetTextColor(255,255,255);
        $this->Cell(20.5,.5,$this->comprobacion->empresa_descripcion,1,1,'C',1);
        $this->SetFillColor(204,204,204);
        $this->SetTextColor(0,0,0);

        $this->SetFont('Arial','B',11);

        $this->Cell(20.5,.5,' ',0,1,'C');

        $this->SetFillColor(0,0,0);
        $this->SetTextColor(255,255,255);
        $this->Cell(9,.5,utf8_decode('TOTAL DE LA COMPROBACIÓN:'),1,0,'L',1);
        $this->SetFillColor(204,204,204);
        $this->SetTextColor(0,0,0);
        $this->Cell(11.5,.5,number_format($this->comprobacion->total_comprobante,2,'.',',')." ".$this->comprobacion->moneda_descripcion,1,1,'C',1);
        $this->SetFont('Arial','',8);
        $this->Cell(20.5,.3,' ',0,1,'C');
        $this->Cell(4,.4,'SOLICITA: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(16.5,.4, $this->comprobacion->empleado_descripcion ,0,1,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(4,.4,'DEPARTAMENTO: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(16.5,.4,$this->comprobacion->departamento_intranet,0,1,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(4,.4,'DEL: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(4.875,.4,$this->comprobacion->FechaDelFormatos,0,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(2,.4,'AL: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(4.875,.4,$this->comprobacion->FechaAlFormatos,0,1,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(4,.4,'PROYECTO/OFICINA: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(16.4,.5,$this->comprobacion->proyecto_descripcion,0,1,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(4,.4,'MOTIVO: ',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->MultiCell(16.5,.4, utf8_decode($this->comprobacion->Motivo) ,0,'J');
    }

    public function encabezadosDoctos()
    {

        /**
         * DOCUMENTOS DEDUCIBLES
         **/
        $encabezadofiscal=0;
        $this->SetFont('Arial','B',10);
        $this->Cell(20.5,.5,' ',0,1,'C');
        if(count($this->comprobacion->documentosDeducibles)>0)
        {
            //$PD=number_format((($encabezadofiscal*100)/$TotalDoctos),0,',','.');
            $PD=($this->comprobacion->total_deducibles*100)/$this->comprobacion->total_comprobante;
            $PD=number_format($PD,0,',','.');

            $this->SetFillColor(0,0,0);
            $this->SetTextColor(255,255,255);
            $this->Cell(7,.5,'GASTOS DEDUCIBLES ( '.count($this->comprobacion->documentosDeducibles).' )',0,0,'L',1);
            $this->Cell(13.5,.5,$this->comprobacion->total_deducibles_format.'   ('.$PD.'%)',0,1,'R',1);
            $this->SetFillColor(204,204,204);
            $this->SetTextColor(0,0,0);

            //REDEFINIC�N DE TAMA�O DE LA LETRA
            $this->SetFont('Arial','B',7);

            $this->Cell(1.5,.4,'FECHA',1,0,'C',1);
            $this->Cell(1.7,.4,'DOCTO.',1,0,'C',1);
            $this->Cell(2.5,.4,'FOLIO',1,0,'C',1);
            $this->Cell(3,.4,'CONCEPTO',1,0,'C',1);
            $this->Cell(1.5,.4,'IMPORTE',1,0,'C',1);
            $this->Cell(1.5,.4,'I.V.A.',1,0,'C',1);
            $this->Cell(1.3,.4,'2% HOSP.',1,0,'C',1);

            $this->Cell(1.5,.4,'TOTAL',1,0,'C',1);
            $this->Cell(6,.4,'OBSERVACIONES',1,1,'C',1);

            $importe = 0;
            $iva = 0;
            $hos = 0;
            $this->SetFont('Arial','',7);
            /*DETALLES DE LOS DOCTOS*/
            foreach ($this->comprobacion->documentosDeducibles as $documento)
            {
                $this->Cell(1.5,.4,$documento->fecha_formato,1,0,'L');
                $this->Cell(1.7,.4,$documento->tipo_docto_descripcion,1,0,'L');
                $this->Cell(2.5,.4,$documento->FolioDocto,1,0,'L');
                $this->Cell(3,.4,$documento->tipo_gasto_descripcion,1,0,'L');
                $this->Cell(1.5,.4,number_format($documento->Importe,2,'.',','),1,0,'R');
                $this->Cell(1.5,.4,number_format($documento->IVA,2,'.',','),1,0,'R');
                $this->Cell(1.3,.4,number_format($documento->ImpHosp,2,'.',','),1,0,'R');
                $this->Cell(1.5,.4,number_format($documento->Total,2,'.',','),1,0,'R');
                $this->Cell(6,.4,$documento->Observaciones,1,1,'L');
                $importe = $importe + $documento->Importe;
                $iva = $iva + $documento->IVA;
                $hos = $hos + $documento->ImpHosp;
            }
            //SUBTOTAL DE LOS DEDUCIBLES

            $importe=number_format($importe,2,'.',',');
            $iva=number_format($iva,2,'.',',');
            $hos=number_format($hos,2,'.',',');

            $this->SetFont('Arial','',7);
            $this->Cell(8.7,.3,'SUBTOTAL: ',0,0,'R');
            $this->SetFont('Arial','B',6);
            $this->Cell(1.5,.3,$importe,1,0,'R');
            $this->Cell(1.5,.3,$iva,1,0,'R');
            $this->Cell(1.3,.3,$hos,1,0,'R');
            $this->Cell(1.5,.3,$this->comprobacion->total_deducibles_format,1,0,'R');
        }

        /**
         * DOCUMENTOS NO DEDUCIBLES
         **/
        $this->SetFont('Arial','B',10);
        $this->Cell(20.5,.3,' ',0,1,'C');

        if(count($this->comprobacion->documentosNoDeducibles)>0)
        {
            //Espacio
            $this->Cell(20.5,.3,' ',0,1,'C');

            //Encabezado de Vale Definitivo
            $this->SetFillColor(0,0,0);
            $this->SetTextColor(255,255,255);
            $this->Cell(20.5,.1,' ',0,1,'C',1);
            $this->SetFillColor(204,204,204);
            $this->SetTextColor(0,0,0);


            $this->SetFillColor(0,0,0);
            $this->SetTextColor(255,255,255);
            $this->Cell(.1,.5,' ',0,0,'C',1);
            $this->SetFillColor(204,204,204);
            $this->SetTextColor(0,0,0);
            $this->SetFont('Arial','B',12);
            $this->Cell(16.3,.5,'V A L E    D E F I N I T I V O',0,0,'C',1);
            $this->Cell(4,.5,$this->comprobacion->folio_vale.' ',0,0,'R',1);
            $this->SetFont('Arial','B',10);
            $this->SetFillColor(0,0,0);
            $this->SetTextColor(255,255,255);
            $this->Cell(.1,.5,' ',0,1,'C',1);
            $this->SetFillColor(204,204,204);
            $this->SetTextColor(0,0,0);
            $this->SetFillColor(0,0,0);
            $this->SetTextColor(255,255,255);
            $this->Cell(20.5,.1,' ',0,1,'C',1);
            $this->SetFillColor(204,204,204);
            $this->SetTextColor(0,0,0);

            $PND=($this->comprobacion->total_no_deducibles*100) / $this->comprobacion->total_comprobante;
            $PND=number_format($PND,0,',','.');
            $this->SetFillColor(0,0,0);
            $this->SetTextColor(255,255,255);
            $this->Cell(7.5,.5,'GASTOS SIN COMPROBANTE ( '.count($this->comprobacion->documentosNoDeducibles).' )',0,0,'L',1);
            $this->Cell(13,.5,$this->comprobacion->total_no_deducibles_format.'   ('.$PND.'%)',0,1,'R',1);
            $this->SetFillColor(204,204,204);
            $this->SetTextColor(0,0,0);


            //REDEFINIC�N DE TAMA�O DE LA LETRA
            $this->SetFont('Arial','B',7);

            $this->Cell(2,.4,'FECHA',1,0,'C',1);
            $this->Cell(2,.4,'DOCUMENTO',1,0,'C',1);
            $this->Cell(2.5,.4,'FOLIO',1,0,'C',1);
            $this->Cell(4,.4,'CONCEPTO',1,0,'C',1);
            $this->Cell(2.5,.4,'IMPORTE',1,0,'C',1);
            $this->Cell(7.5,.4,'OBSERVACIONES',1,1,'C',1);

            $importe = 0;
            $iva = 0;
            $hos = 0;
            /*DETALLES DE LOS DOCTOS*/
            $this->SetFont('Arial','',7);
            foreach ($this->comprobacion->documentosNoDeducibles as $documento)
            {
                $this->Cell(2,.4,$documento->fecha_formato,1,0,'L');
                $this->Cell(2,.4,substr($documento->tipo_docto_descripcion,0,8).'. ',1,0,'L');
                $this->Cell(2.5,.4,$documento->FolioDocto,1,0,'L');
                $this->Cell(4,.4,$documento->tipo_gasto_descripcion,1,0,'L');
                $this->Cell(2.5,.4,number_format($documento->Importe,2,'.',','),1,0,'R');
                if(strlen($documento->Observaciones) > 45){
                    $this->Cell(7.5,.4,substr(utf8_decode($documento->Observaciones), 0 ,45) . "...",1,1,'L');
                }
                else{
                    $this->Cell(7.5,.4,utf8_decode($documento->Observaciones),1,1,'L');
                }

                $importe = $importe + $documento->Importe;
            }

            $SubtotalNoDeduciblesTotal=number_format($this->comprobacion->total_no_deducibles,2,'.',',');
            $this->SetFont('Arial','B',6);
            $this->SetFillColor(0,0,0);
            $this->SetTextColor(255,255,255);
            $this->Cell(20.5,.1,' ',0,1,'C',1);
            $this->SetFillColor(204,204,204);
            $this->SetTextColor(0,0,0);
            $this->SetFillColor(0,0,0);
            $this->SetTextColor(255,255,255);
            $this->Cell(.1,.5,' ',0,0,'C',1);
            $this->SetFillColor(204,204,204);
            $this->SetTextColor(0,0,0);
            $this->SetFont('Arial','B',12);
            $this->Cell(10,.5,'TOTAL DEL VALE DEFINITIVO: ',0,0,'C',1);
            $this->Cell(2.4,.5,$SubtotalNoDeduciblesTotal,0,0,'R',1);
            $this->Cell(7.9,.5,' ',0,0,'C',1);
            $this->SetFont('Arial','B',10);
            $this->SetFillColor(0,0,0);
            $this->SetTextColor(255,255,255);
            $this->Cell(.1,.5,' ',0,1,'C',1);
            $this->SetFillColor(204,204,204);
            $this->SetTextColor(0,0,0);
            $this->SetFillColor(0,0,0);
            $this->SetTextColor(255,255,255);
            $this->Cell(20.5,.1,' ',0,1,'C',1);
            $this->SetFillColor(204,204,204);
            $this->SetTextColor(0,0,0);
        }

        $this->Cell(20.5,.5,' ',0,1,'C');
        $this->SetFont('Arial','B',10);

        $ETotalPromedioF = $this->comprobacion->total_comprobante / $this->duracion;
        $this->SetFillColor(0,0,0);
        $this->SetTextColor(255,255,255);
        $this->Cell(10,.5,utf8_decode('RESUMEN DE GASTOS DE ' . $this->duracion . ' DÍA(S)'),0,0,'L',1);
        $this->Cell(10.5,.5,'PROMEDIO DIARIO:' . number_format($ETotalPromedioF,2,'.',','),0,1,'R',1);
        $this->SetFillColor(204,204,204);
        $this->SetTextColor(0,0,0);

        //REDEFINIC�N DE TAMA�O DE LA LETRA
        $this->SetFont('Arial','B',8);
        $this->Cell(4.5,.4,'',0,0,'L');
        $this->Cell(7.5,.4,'DEDUCIBLES',1,0,'C',1);
        $this->Cell(3,.4,'NO DEDUCIBLES',1,1,'C',1);


        $this->Cell(4.5,.4,'CONCEPTO',1,0,'C',1);
        $this->Cell(2.5,.4,'IMPORTE',1,0,'C',1);
        $this->Cell(2.5,.4,'I.V.A.',1,0,'C',1);
        $this->Cell(2.5,.4,'SUBTOTAL',1,0,'C',1);
        $this->Cell(3,.4,'SUBTOTAL',1,0,'C',1);
        $this->Cell(3,.4,'TOTAL',1,0,'C',1);
        $this->Cell(2.5,.4,'PROM. DIARIO',1,1,'C',1);

        $this->SetFont('Arial','',8);
        $grupos = $this->comprobacion->agruparDocumentos();
        $im_deducible = 0;
        $iva_deducible = 0;
        $imp_deducible = 0;
        $total_deducible = 0;
        $im_no_deducible = 0;
        $porcentaje_deducible = 0;
        foreach ($grupos as$grupo)
        {
            $g =  GrupoTipoGastoComp::find($grupo->Grupo);
            $deducibles = $this->comprobacion->sumaPorGrupo($grupo->Grupo,1);
            $no_deducibles = $this->comprobacion->sumaPorGrupo($grupo->Grupo,2);
            $total = ($deducibles->total != null ? $deducibles->total : 0) + ($no_deducibles->total != null ? $no_deducibles->total : 0);

            $this->Cell(4.5,.4,$g->Descripcion,1,0,'L');
            $this->Cell(2.5,.4,$deducibles->importe != null ? number_format($deducibles->importe,2,'.',',') : 0.00,1,0,'R');
            $this->Cell(2.5,.4,$deducibles->iva != null   ? number_format($deducibles->iva,2,'.',',') : 0.00,1,0,'R');
            $this->Cell(2.5,.4,$deducibles->total != null  ? number_format($deducibles->total,2,'.',',') : 0.00,1,0,'R');
            $this->Cell(3,.4,$no_deducibles->importe != null  ? number_format($no_deducibles->importe,2,'.',',') : 0.00,1,0,'R');
            $this->Cell(3,.4,number_format($total,2,'.',','),1,0,'R');
            $this->Cell(2.5,.4,number_format(($total / $this->duracion) ,2,'.',','),1,1,'R');
            $im_deducible = $im_deducible + ($deducibles->importe != null ? $deducibles->importe : 0.00);
            $iva_deducible = $iva_deducible + ($deducibles->iva != null ? $deducibles->iva : 0.00);
            $imp_deducible = $imp_deducible + ($deducibles->imp != null ? ($deducibles->importe + $deducibles->iva) : 0.00);
            $im_no_deducible = $im_no_deducible + ($no_deducibles->importe != null ? $no_deducibles->importe : 0.00);
            $total_deducible = $total_deducible + $total;
            $porcentaje_deducible = $porcentaje_deducible + ($total / $this->duracion);
        }

        $this->SetFont('Arial','B',8);

        $this->Cell(4.5,.4,' ',0,0,'R');
        $this->Cell(2.5,.4,number_format($im_deducible,2,'.',','),1,0,'R');
        $this->Cell(2.5,.4,number_format($iva_deducible,2,'.',','),1,0,'R');
        $this->Cell(2.5,.4,number_format($imp_deducible,2,'.',','),1,0,'R');
        $this->Cell(3,.4,number_format($im_no_deducible,2,'.',','),1,0,'R');
        $this->Cell(3,.4,number_format($total_deducible,2,'.',','),1,0,'R');
        $this->Cell(2.5,.4,number_format($porcentaje_deducible,2,'.',','),1,1,'R');

        if(($this->comprobacion->Estatus == 2) || ($this->comprobacion->Estatus == 3) || ($this->comprobacion->Estatus == 4)) {


            $this->Cell(20.5, .5, ' ', 0, 1, 'C');
            $this->SetFont('Arial', 'B', 10);
            $this->SetTextColor(255, 255, 255);
            $this->SetFillColor(0, 0, 0);

            $this->Cell(20.5, .5, 'RESUMEN DE SALDOS DE LAS SOLICITUDES COMPROBADAS ', 0, 1, 'L', 1);

            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetFillColor(204, 204, 204);
            $leyenda = '';
            if ($this->comprobacion->solicitudComprobada->ImportePorComprobar == 0) {
                $favor = "SALDO";
                $saldo = '';
            } else {
                if ($this->comprobacion->solicitudComprobada->ImportePorComprobar > 0) {
                    $favor = "DE LA EMPRESA*";
                    $saldo = 'SALDO A FAVOR';
                    $leyenda = ("*FAVOR DE ANEXAR EN ÉSTA COMPROBACIÓN COPIA FOTOSTÓTICA DEL DEPÓSITO REALIZADO Y ENTREGAR EL ORIGINAL A TESORERÍA");

                } else {
                    if ($this->comprobacion->solicitudComprobada->ImportePorComprobar < 0) {
                        $favor = "DEL EMPLEADO";
                        $saldo = 'SALDO A FAVOR';
                    }
                }
            }

            $this->Cell(2.5, .4, '', 'T L R', 0, 'C', 1);
            $this->Cell(4, .4, 'CHEQUE/TRANSF.', 1, 0, 'C', 1);
            $this->Cell(2.5, .4, '', 'T L R', 0, 'C', 1);
            $this->Cell(2.5, .4, 'COMPROBADO', 'T L R', 0, 'C', 1);
            $this->Cell(2.5, .4, 'COMPROBADO', 'T L R', 0, 'C', 1);
            $this->Cell(2.5, .4, 'COMPROBADO', 'T L R', 0, 'C', 1);
            $this->SetFillColor(0, 0, 0);
            $this->SetTextColor(255, 255, 255);
            $this->SetDrawColor(255, 255, 255);
            $this->Cell(4, .4, $saldo, 1, 1, 'C', 1);

            $this->SetTextColor(0, 0, 0);
            $this->SetFillColor(204, 204, 204);
            $this->SetDrawColor(0, 0, 0);
            $this->Cell(2.5, .4, 'SOLICITUD', 'B L R', 0, 'C', 1);
            $this->Cell(2, .4, 'NUMERO', 1, 0, 'C', 1);
            $this->Cell(2, .4, 'FECHA', 1, 0, 'C', 1);
            $this->Cell(2.5, .4, 'IMPORTE', 'B L R', 0, 'C', 1);
            $this->Cell(2.5, .4, 'ANTERIOR', 'B L R', 0, 'C', 1);
            $this->Cell(2.5, .4, 'ACTUAL', 'B L R', 0, 'C', 1);
            $this->Cell(2.5, .4, 'TOTAL', 'B L R', 0, 'C', 1);
            $this->SetFillColor(0, 0, 0);
            $this->SetTextColor(255, 255, 255);
            $this->SetDrawColor(255, 255, 255);
            $this->Cell(4, .4, $favor, 'B L R', 1, 'C', 1);
            $this->SetTextColor(0, 0, 0);
            $this->SetFillColor(204, 204, 204);
            $this->SetDrawColor(0, 0, 0);

            $this->SetFillColor(204, 204, 204);
            $this->SetTextColor(0, 0, 0);
            $this->SetDrawColor(0, 0, 0);

            $this->SetFont('Arial', '', 8);
            $this->Cell(2.5, .4, $this->comprobacion->solicitud->folio_formato, 1, 0, 'C');
            $this->Cell(2, .4, $this->comprobacion->solicitud->partidaPago ? substr($this->comprobacion->solicitud->partidaPago->pago->formaPago->Nombre,0,2) .'-'. $this->comprobacion->solicitud->partidaPago->pago->Numero : ' - ', 1, 0, 'C'); //$Resumen["TCHT"].$Resumen["CHT"]
            $this->Cell(2, .4, $this->comprobacion->solicitud->partidaPago ? $this->comprobacion->solicitud->partidaPago->pago->FechaPago : '', 1, 0, 'C');
            $this->Cell(2.5, .4, $this->comprobacion->solicitud->importe_format, 1, 0, 'R');
            $this->Cell(2.5, .4, number_format($this->comprobacion->solicitudComprobada->ImporteComprobadoAnterior, 2, ".", ","), 1, 0, 'R');
            $this->Cell(2.5, .4, number_format($this->comprobacion->solicitudComprobada->ImporteComprobadoActual, 2, ".", ","), 1, 0, 'R');
            $this->Cell(2.5, .4, number_format($this->comprobacion->solicitudComprobada->ImporteComprobadoAnterior + $this->comprobacion->solicitudComprobada->ImporteComprobadoActual, 2, ".", ","), 1, 0, 'R');
            $this->SetFont('Arial', 'B', 8);
            $this->SetFillColor(0, 0, 0);
            $this->SetTextColor(255, 255, 255);
            $this->SetDrawColor(255, 255, 255);
            $this->Cell(4, .4, number_format(abs($this->comprobacion->solicitudComprobada->ImportePorComprobar), 2, ".", ","), 1, 1, 'R', 1);

            $this->SetDrawColor(0, 0, 0);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(19.5, 0.5, "", 0, 1, 'L');
            if ($leyenda != "") {
                $this->SetFillColor(0, 0, 0);
                $this->SetTextColor(255, 255, 255);
                $this->Cell(19.5, 0.5, "I  M  P  O  R  T  A  N  T  E", 'L R B T', 1, 'C', 1);
                $this->SetTextColor(0, 0, 0);
                $this->Cell(19.5, 1, $leyenda, 'L R', 1, 'L');
                $this->cuentas();
            }
        }
    }

    public function create()
    {
        $this->SetMargins(0.4, 0.5, 0.4);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,3);
        try {
            $this->Output('I', "Formato - Comprobación: ".$this->comprobacion->folio.".pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

    function Footer()
    {
        if (!App::environment('production')) {
            $this->SetFont('Arial','B',90);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(3,15,utf8_decode("MUESTRA"),45);
            $this->RotatedText(6,20,utf8_decode("SIN VALOR"),45);
            $this->SetTextColor('0,0,0');
        }

        $this->SetY(25.25);

        $this->SetFont('Arial','B',7);
        $this->SetTextColor(255,255,255);
        $this->SetFillColor(0,0,0);
        $this->Cell(19.5,.3,' ',0,1,'R');

        if($this->comprobacion->JefeArea != '' || $this->comprobacion->JefeArea != 0)
            $jefe = $this->comprobacion->JefeArea;
        else
            $jefe = " - - - - - - - - ";

        if($this->comprobacion->DirectorArea != '' || $this->comprobacion->DirectorArea != 0)
            $dir_area= $this->comprobacion->DirectorArea;
        else
            $dir_area=" - - - - - - - - ";

        if($this->comprobacion->AutTramiteFinanzas != '' || $this->comprobacion->AutTramiteFinanzas != 0)
            $autfin=$this->comprobacion->AutTramiteFinanzas;
        else
            $autfin=" - - - - - - - - ";

        if($this->comprobacion->Version==1)
        {
            $this->Cell(5.12,.3,'SOLICITA',1,0,'C',1);
            $this->Cell(4.875,.3,utf8_decode('JEFE ÁREA'),1,0,'C',1);
            $this->Cell(4.875,.3,utf8_decode('DIRECTOR ÁREA'),1,0,'C',1);
            $this->Cell(4.875,.3,'AUT. TRAMITE FINANZAS',1,1,'C',1);

            $this->Cell(5.12,1,' ',1,0,'C');
            $this->Cell(5.12,1,' ',1,0,'C');
            $this->Cell(5.12,1,' ',1,0,'C');
            $this->Cell(5.12,1,' ',1,1,'C');

            $this->SetFont('Arial','B',5);
            $this->SetTextColor(0,0,0);

            $this->Cell(5.12,.2,$this->comprobacion->empleado_descripcion,1,0,'C');
            $this->Cell(5.12,.2,$jefe,1,0,'C');
            $this->Cell(5.12,.2,$dir_area,1,0,'C');
            $this->Cell(5.12,.2,$autfin,1,1,'C');
        }
        else {
            $cantidad_firmas = count($this->comprobacion->firmas);
              if($cantidad_firmas > 1)
              {
                  if($cantidad_firmas>=4)
                  {
                      $blancos=false;
                      $ancho=17.5/$cantidad_firmas;
                  }
                  else
                  {
                      $blancos=true;
                      $ancho=4.5;
                      $ancho_blancos=(17.5-($ancho * $cantidad_firmas))/2;
                  }
                  if($blancos)
                  {
                      $this->Cell($ancho_blancos,1,' ',0,0,'C');
                  }
                  $y=$this->getY();
                  $x=$this->getX();

                  $this->SetFont('Arial','',6);
                  $this->SetTextColor(255,255,255);
                  $this->SetFillColor(0,0,0);

                  $this->CellFitScale($ancho,.3,"SOLICITA",1,2,'C',1);
                  $this->Cell($ancho,1,'',1,2,'C');
                  $this->SetFont('Arial','B',5);
                  $this->SetFillColor(255,255,255);
                  $this->SetTextColor(0,0,0);

                  $this->CellFitScale($ancho,.2,$this->comprobacion->empleado_descripcion,1,2,'C',1);
                  $x+=$ancho;

                foreach ($this->comprobacion->firmasOrdenadas as $firma)
                  {
                      $this->setY($y);
                      $this->setX($x);
                      $this->SetFont('Arial','',6);
                      $this->SetTextColor(255,255,255);
                      $this->SetFillColor(0,0,0);
                      $this->CellFitScale($ancho,.3,utf8_decode($firma->encabezado->descripcion),1,2,'C',1);
                      $this->Cell($ancho,1,'',1,2,'C');
                      $this->SetFont('Arial','',5);
                      $this->SetFillColor(255,255,255);
                      $this->SetTextColor(0,0,0);
                      $this->CellFitScale($ancho,.2,utf8_decode($firma->firmante->descripcion),1,2,'C',1);
                      $x+=$ancho;
                  }
              }
              else {
                  $Nombre_Direccion = '';
                  if($this->comprobacion->Alias_Depto != 'CNC')
                  {
                      if($autfin=="ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ")
                      {
                          $Nombre_Direccion=utf8_decode("DIRECCIÓN DE ADMON. Y FIN.");
                      }
                      else
                          if($autfin=="C.P. GUSTAVO ADOLFO ESPINOSA CARBAJAL")
                          {
                              $Nombre_Direccion=utf8_decode("DIRECCIÓN GENERAL");
                          }
                      $this->Cell(5.12,.3,'SOLICITA',1,0,'C',1);
                      $this->Cell(5.12,.3,'AUTORIZA',1,0,'C',1);
                      $this->Cell(5.12,.3,'VoBo',1,0,'C',1);

                      $this->Cell(5.12,.3,$Nombre_Direccion,1,1,'C',1);

                      $this->Cell(5.12,1,' ',1,0,'C');
                      $this->Cell(5.12,1,' ',1,0,'C');
                      $this->Cell(5.12,1,' ',1,0,'C');
                      $this->Cell(5.12,1,' ',1,1,'C');

                      $this->SetFont('Arial','B',5);
                      $this->SetTextColor(0,0,0);

                      $this->Cell(5.12,.2,$this->comprobacion->empleado_descripcion,1,0,'C');
                      $this->Cell(5.12,.2,$jefe,1,0,'C');
                      $this->Cell(5.12,.2,$dir_area,1,0,'C');
                      $this->Cell(5.12,.2,$autfin,1,1,'C');
                  }
                  else
                  {
                      if($autfin=="ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ")
                      {
                          $Nombre_Direccion=utf8_decode("DIRECCIÓN DE ADMON. Y FIN.");
                      }
                      else
                          if($autfin=="C.P. GUSTAVO ADOLFO ESPINOSA CARBAJAL")
                          {
                              $Nombre_Direccion=utf8_decode("DIRECCIÓN GENERAL");
                          }
                      $this->SetFont('Arial','B',5);


                      $this->Cell(4.1,.3,'SOLICITA',1,0,'C',1);
                      $this->Cell(4.1,.3,'AUTORIZA',1,0,'C',1);
                      $this->Cell(4.1,.3,'VoBo',1,0,'C',1);
                      $this->Cell(4.1,.3,'DIR DE PROM Y DES DE NEGOCIOS',1,0,'C',1);
                      $this->Cell(4.1,.3,$Nombre_Direccion,1,1,'C',1);

                      $this->Cell(4.1,1,' ',1,0,'C');
                      $this->Cell(4.1,1,' ',1,0,'C');
                      $this->Cell(4.1,1,' ',1,0,'C');
                      $this->Cell(4.1,1,' ',1,0,'C');
                      $this->Cell(4.1,1,' ',1,1,'C');

                      $this->SetFont('Arial','B',5);
                      $this->SetTextColor(0,0,0);

                      $this->Cell(4.1,.2,$this->comprobacion->empleado_descripcion,1,0,'C');
                      $this->Cell(4.1,.2,$jefe,1,0,'C');
                      $this->Cell(4.1,.2,$dir_area,1,0,'C');
                      $this->Cell(4.1,.2,utf8_decode('LIC. ARTURO OROZCO GÓMEZ'),1,0,'C');
                      $this->Cell(4.1,.2,$autfin,1,1,'C');

                  }
              }
          }
        $this->SetXY(1,-0.8);
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(10, .3, utf8_decode('Formato generado desde el sistema de control de recursos. Fecha de registro: ' . date("d-m-Y", strtotime($this->comprobacion->Fecha))).' Fecha de consulta: '.date("d-m-Y H:i:s"), 0, 0, 'L');
        $this->Cell(10.5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function cuentas()
    {
        $cuenta = CuentaEmpresa::where('IdEmpresa', $this->comprobacion->EmpresaFacturar)->where('DepositosDevolucion', 1)->first();

        $leyendacb_1=("El importe a favor de la empresa deber� depositarse en ");
        $leyendacb_3="a nombre de ".$this->comprobacion->empresa->RazonSocial." ";
        $leyendacb_2='';
        if($cuenta->cuenta->banco->Banco != null && $cuenta->cuenta->Cuenta != null)
        {

            $leyendacb_2=$leyendacb_2."".$cuenta->cuenta->banco->Banco." a la cuenta bancaria ".$cuenta->cuenta->Cuenta." o en ";

        }
        $leyendacb_2=substr($leyendacb_2,0,strlen($leyendacb_2)-5);
        $leyendacb=$leyendacb_1.$leyendacb_2.$leyendacb_3;
        $this->SetFont('Arial','B',8);
        $this->MultiCell(19.5,.5,$leyendacb,'L R B','L');
    }
}
