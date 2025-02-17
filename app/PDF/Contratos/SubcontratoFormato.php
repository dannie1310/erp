<?php


namespace App\PDF\Contratos;


// use Ghidev\Fpdf\Rotation;
use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Subcontrato;
use App\Models\IGH\TipoCambio;
use App\Utils\PDF\FPDI\FPDI;
use DateTime;

class SubcontratoFormato extends FPDI
{
    private $subcontrato;
    private $encabezado_pdf = '';
    private $encola = '';
    private $paginaClausulado = 0;

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(Subcontrato $subcontrato)
    {

        parent::__construct('P', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());
        $this->subcontrato = $subcontrato;

        $this->SetAutoPageBreak(true, 5);
        $this->WidthTotal = $this->GetPageWidth() - 2;
        $this->txtTitleTam = 18;
        $this->txtSubtitleTam = 13;
        $this->txtSeccionTam = 9;
        $this->txtContenidoTam = 11;
        $this->txtFooterTam = 6;
        $this->encabezado_pdf = utf8_decode('SUBCONTRATO');


    }

    function Header(){
        $ln = 0;
        $nuevo = 0;
        if($this->encola == 'clausulado' && $this->subcontrato->clasificacionSubcontrato){
            if(Context::getDatabase()  == "SAO1814_TERMINAL_NAICM"){
                $this->setSourceFile(public_path('pdf/ClausuladosPDF/Clausulado_ctvm.pdf'));
            }
            else if($this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 3 || $this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 7){
                if(Context::getDatabase()  == "SAO1814_QUERETARO_SAN_LUIS" && Context::getIdObra() == 5){
                    $this->setSourceFile(public_path('pdf/ClausuladosPDF/ClausuladoOS_CCSL.pdf'));
                    $ln = 11.23;
                }else{
                    if(strtotime($this->subcontrato->fecha) >= strtotime('2023-01-01'))
                    {dd("a");
                        $this->setSourceFile(public_path('pdf/ClausuladosPDF/ClausuladoOS2025.pdf'));
                        $ln = 20.55;
                        $nuevo = 1;
                        $this->paginaClausulado = 1;
                    }
                    else if(strtotime($this->subcontrato->fecha) >= strtotime('2024-02-29'))
                    {
                        $this->setSourceFile(public_path('pdf/ClausuladosPDF/ClausuladoOS2024.pdf'));
                        $ln = 20.55;
                        $nuevo = 1;
                    }else{
                        $this->setSourceFile(public_path('pdf/ClausuladosPDF/ClausuladoOS.pdf'));
                        $ln = 11.23;
                    }
                }
            }
            else if($this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 4){
                if(Context::getDatabase()  == "SAO1814_QUERETARO_SAN_LUIS" && Context::getIdObra() == 5){
                    $this->setSourceFile(public_path('pdf/ClausuladosPDF/ClausuladoOT_CCSL.pdf'));
                    $ln = 14.6;
                }else{
                    if(strtotime($this->subcontrato->fecha) >= strtotime('2023-01-01'))
                    {
                        $this->setSourceFile(public_path('pdf/ClausuladosPDF/ClausuladoOT2025.pdf'));
                        $ln = 19;
                        $nuevo = 1;
                        $this->paginaClausulado = 1;
                    }
                    else if(strtotime($this->subcontrato->fecha) >= strtotime('2024-02-29'))
                    {
                        $this->setSourceFile(public_path('pdf/ClausuladosPDF/ClausuladoOT2024.pdf'));
                        $ln = 19;
                        $nuevo = 1;
                    }else{
                        $this->setSourceFile(public_path('pdf/ClausuladosPDF/ClausuladoOT.pdf'));
                        $ln = 14.6;
                    }
                }
            }
            else{
                $this->setSourceFile(public_path('pdf/ClausuladosPDF/Clausulado_oc.pdf'));
            }

            $this->clausulado = $this->importPage(1);
            if($this->paginaClausulado == 1)
            {
                $this->clausulado2 = $this->importPage(2);
            }

            $this->setSourceFile(public_path('pdf/ClausuladosPDF/SinTexto.pdf'));
            $this->sin_texto =  $this->importPage(1);

            $this->SetTextColor('0,0,0');
            $this->SetFont('Arial', 'B', 10);

            $this->Cell(11.5);
            $this->Cell(2.5,.5, 'OBRA','LT',0,'L');
            $this->Cell(5.5,.5,  utf8_decode($this->obra->nombre) .' ','RT',0,'R');
            $this->Ln(.5);


            $this->SetFont('Arial', 'B', 10);
            $this->Cell(11.5);
            $this->Cell(2.5,.5, 'FECHA:','L',0,'L');
            $this->Cell(5.5,.5, $this->subcontrato->fecha_format.' ','R',0,'R');
            $this->Ln(.5);

            $this->SetFont('Arial', 'B', 10);
            $this->Cell(11.5);
            $this->Cell(2.5,.5, 'FOLIO:','L',0,'L');
            $this->Cell(5.5,.5, $this->subcontrato->clasificacionSubcontrato->folio_format.' ','R',0,'R');
            $this->Ln(.5);

            $this->SetFont('Arial', 'B', 10);
            $this->Cell(11.5);
            $this->Cell(2.5,.5, 'CONTRATO:','L',0,'L');
            $this->Cell(5.5,.5, $this->subcontrato->contratoProyectado->folio_format.' ','R',0,'R');
            $this->Ln(.5);

            $this->Cell(11.5);
            $this->Cell(2.5,.5,'TOTAL:','LB',0,'L');
            $this->Cell(5.5,.5, "$ ". number_format($this->subcontrato->monto, 2, ',', '.'),'RB',1,'R');
            if($ln > 0 && $this->encola == 'clausulado'){
                $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                $fecha_exp = explode('/', $this->subcontrato->fecha_format);

                $this->Ln($ln);
                $this->SetFont('Arial', 'B', 5);
                if($nuevo == 1)
                {
                    $this->Cell(19.7);
                    $this->Cell(2.5,.5,$fecha_exp[0],'',0,'L');

                    $this->Ln(.20);
                    $this->Cell(11.5);
                    $this->Cell(1.5,.5,$meses[$fecha_exp[1]-1],'',0,'L');

                    $this->Ln(.175);
                    $this->Cell(13.1);
                    $this->SetFillColor('255,255,255');
                    $this->Cell(.7,.15,substr($fecha_exp[2], -2),'',0,'L', 1);
                }else{
                    $this->Cell(18.15);
                    $this->Cell(2.5,.5,$fecha_exp[0],'',0,'L');

                    $this->Ln(.22);
                    $this->Cell(10.3);
                    $this->Cell(1.5,.5,$meses[$fecha_exp[1]-1],'',0,'L');

                    $this->Ln(.18);
                    $this->Cell(12.5);
                    $this->SetFillColor('255,255,255');
                    $this->Cell(.7,.15,substr($fecha_exp[2],-2),'',0,'L', 1);
                }
            }
        }
        else{
            $postTitle=.7;
            if( Context::getDatabase() == "SAO1814" && Context::getIdObra() == 41){
                $this->image('../../img/subcontrato/LOGOTIPO_REHABILITACION_ATLACOMULCO.png',1,.3,5,2);
                $postTitle=3.5;
            }
            $referencia = \substr($this->subcontrato->referencia, 0, 24) ;

            $this->SetTextColor('0,0,0');
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(11.5);
            $this->Cell(1.5,.7,'No. '.($this->subcontrato->clasificacionSubcontrato?$this->subcontrato->clasificacionSubcontrato->tipo->descripcion_corta:'').': ','LT',0,'L');
            $this->Cell(6.5,.7, $referencia,'RT',0,'R');
            $this->Ln(.7);

            $this->SetFont('Arial', 'B', 18);
            $this->Cell(11.5, $postTitle, ($this->subcontrato->clasificacionSubcontrato?utf8_decode($this->subcontrato->clasificacionSubcontrato->tipo->descripcion):'SUBCONTRATO') , 0, 0, 'C', 0);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(4.5,.7, 'FECHA:','BL',0,'L');
            $this->Cell(3.5,.7, $this->subcontrato->fecha_format.' ','RB',0,'R');
            $this->Ln(.7);

            $this->Ln(.6);
            $y_inicial = $this->getY();
            $x_inicial = $this->getX();

            $alto = abs(1);
            $this->SetWidths(array(19.5));
            $this->SetRounds(array('1234'));
            $this->SetRadius(array(0.1));
            $this->SetFills(array('255,255,255'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetHeights(array($alto));
            $this->SetStyles(array('DF'));
            $this->SetAligns("L");
            $this->SetFont('Arial', 'B', 13);
            $this->setY($y_inicial);
            $this->Row(array(""));
            $this->setY($y_inicial);
            $this->setX($x_inicial);
            $this->MultiCell(19.5, 1,  utf8_decode($this->obra->nombre_obra_formatos), '', 'C');

            $this->Ln(.3);
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
            $this->SetFont('Arial', '', 10);
            $this->Cell(9.5,.7,'Subcontratista',0,0,'L');
            $this->Cell(.5);
            $this->Cell(9.5,.7,'Cliente (Facturar a)',0,0,'L');
            $this->Ln(.6);
            $y_inicial = $this->getY();
            $x_inicial = $this->getX();
            $this->MultiCell(9.5, .5,$this->subcontrato->empresa->razon_social.''. ($this->subcontrato->sucursal? $this->subcontrato->sucursal->direccion . ', C.P.'. $this->subcontrato->sucursal->codigo_postal  .', '. $this->subcontrato->sucursal->estado:'') .', '.$this->subcontrato->empresa->rfc, '', 'L');
            $y_final_1 = $this->getY();
            $this->setY($y_inicial);
            $this->setX($x_inicial+10);
            $this->MultiCell(9.5, .5,$this->obra->facturar.''.$this->obra->direccion.''.$this->obra->rfc, '', 'L');
            $y_final_2 = $this->getY();
            if($y_final_1>$y_final_2){$y_alto = $y_final_1;}else{$y_alto = $y_final_2;}
            $alto = abs($y_inicial-$y_alto);
            $this->SetWidths(array(9.5));
            $this->SetRounds(array('1234'));
            $this->SetRadius(array(0.2));
            $this->SetFills(array('255,255,255'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetHeights(array($alto));
            $this->SetStyles(array('DF'));
            $this->SetAligns("L");
            $this->SetFont('Arial', '', 10);
            $this->setY($y_inicial);
            $this->Row(array(""));
            $this->setY($y_inicial);
            $this->setX($x_inicial);
            $this->MultiCell(9.5, .5,$this->subcontrato->empresa->razon_social.''. ($this->subcontrato->sucursal? $this->subcontrato->sucursal->direccion . ', C.P.'. $this->subcontrato->sucursal->codigo_postal  .', '. $this->subcontrato->sucursal->estado:'') .', '.$this->subcontrato->empresa->rfc, 0, 'L');
            $this->setY($y_inicial);
            $this->setX($x_inicial+10);
            $this->Row(array(""));

            $this->setY($y_inicial);
            $this->setX($x_inicial+10);
            $this->MultiCell(9.5, .5,utf8_decode($this->obra->facturar.''.$this->obra->direccion.''.$this->obra->rfc), '', 'L');
            $this->Ln(.9);
            $y_inicial = $this->getY();
            $x_inicial = $this->getX();


            $this->setY($y_alto+0.5);

            $this->SetFont('Arial', 'B', 10);
            $this->CellFit(2.5, .5, utf8_decode('Descripción: ') , 0, 0, 'C', 0);
            $this->SetFont('Arial', '', 10);
            $this->MultiCell(17, .5,  $this->subcontrato->subcontratos?utf8_decode($this->subcontrato->subcontratos->observacion):'', '', 'L');

            $this->encabezados();
        }

    }

    function encabezados(){
        $this->SetFont('Arial', '', 6);
        $this->SetHeights(array(0.5));
        if($this->encola=="partida"){
            $this->Ln(.7);
            $this->SetFillColor(180,180,180);
            $this->SetWidths(array(0.5,1.5,1.5,2,7,2,1,2,2));
            $this->SetStyles(array('DF','DF','DF','DF','DF','FD','FD','DF'));
            $this->SetRounds(array('1','','','','','','','','2'));
            $this->SetRadius(array(0.2,0,0,0,0,0,0,0,0.2));
            $this->SetFills(array('180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180'));
            $this->SetTextColors(array('0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0'));
            $this->SetHeights(array(0.4));
            $this->SetAligns(array('C','C','C','C','C','C','C','C','C'));
            $this->Row(array("#","Cantidad", "Unidad", "Clave Concepto", utf8_decode("Descripción"), "Precio", "% Descto.","Precio Neto", "Importe"));
            $this->SetRounds(array('','','','','','','','',''));
            $this->SetFills(array('255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255'));
            $this->SetAligns(array('C','R','C','L','L','R','R','R', 'R'));
            $this->SetTextColors(array('0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0'));
        }
        else if($this->encola == "observaciones_partida"){
            $this->SetRadius(array(0));
            $this->SetTextColors(array('150,150,150'));
            $this->SetWidths(array(19.5));
            $this->SetAligns(array('J'));
        }
        else if($this->encola == "observaciones_encabezado"){
            $this->SetWidths(array(19.5));
            $this->SetRounds(array('12'));
            $this->SetRadius(array(0.2));
            $this->SetFills(array('180,180,180'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 9);
            $this->SetAligns(array('C'));
        }
        else if($this->encola == "observaciones"){
            $this->SetRounds(array('34'));
            $this->SetRadius(array(0.2));
            $this->SetAligns(array('J'));
            $this->SetStyles(array('DF'));
            $this->SetFills(array('255,255,255'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 9);
            $this->SetWidths(array(19.5));
        }
        else if($this->encola == "path_concepto"){
            $this->Ln(.7);
            $this->SetFillColor(180,180,180);
            $this->SetWidths(array(0.5,1.5,1.5,2,7,2,1,2,2));
            $this->SetStyles(array('DF','DF','DF','DF','DF','FD','FD','DF'));
            $this->SetRounds(array('1','','','','','','','','2'));
            $this->SetRadius(array(0.2,0,0,0,0,0,0,0,0.2));
            $this->SetFills(array('180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180'));
            $this->SetTextColors(array('0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0'));
            $this->SetHeights(array(0.4));
            $this->SetAligns(array('C','C','C','C','C','C','C','C','C'));
            $this->Row(array("#","Cantidad", "Unidad", "Clave Concepto", utf8_decode("Descripción"), "Precio", "% Descto.","Precio Neto", "Importe"));
            $this->SetRounds(array('','','','','','','','',''));
            $this->SetFills(array('255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255'));
            $this->SetAligns(array('C','R','C','L','L','R','R','R', 'R'));
            $this->SetTextColors(array('0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0'));

            $this->SetWidths(array(19.5));
            $this->SetRadius(array(0,0,0,0,0,0,0,0,0));
        }
        else{
            $this->Ln(.7);
        }
    }

    function partidas(){
        $this->SetFillColor(180,180,180);
        $this->SetWidths(array(0.5,1.5,1.5,2,7,2,1,2,2));
        $this->SetStyles(array('DF','DF','DF','DF','DF','FD','FD','DF'));
        $this->SetRounds(array('1','','','','','','','','2'));
        $this->SetRadius(array(0.2,0,0,0,0,0,0,0,0.2));
        $this->SetFills(array('180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180'));
        $this->SetTextColors(array('0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0'));
        $this->SetHeights(array(0.4));
        $this->SetAligns(array('C','C','C','C','C','C','C','C','C'));
        $this->Row(array("#","Cantidad", "Unidad", "Clave Concepto", utf8_decode("Descripción"), "Precio", "% Descto.","Precio Neto", "Importe"));
        $this->SetRounds(array('','','','','','','','',''));
        $this->SetFills(array('255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255'));
        $this->SetAligns(array('C','R','C','L','L','R','R','R', 'R'));
        $this->SetTextColors(array('0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0'));
        $this->SetWidths(array(0.5,1.5,1.5,2,7,2,1,2,2));
        $this->encola="partida";
        $this->SetRounds(array('','','','','','','','',''));
        $this->SetFills(array('255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255'));
        $this->SetAligns(array('C','R','C','L','L','R','R','R','R'));
        $this->SetTextColors(array('0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0'));

        $subtotal = 0;
        foreach ($this->subcontrato->partidas as $key => $partida) {

            $importe = $partida->precio_unitario * $partida->cantidad;
            $this->Row(array($key+1,
                $partida->cantidad_format,
                $partida->contrato->unidad,
                $partida->contrato->destino->concepto->clave_concepto,
                utf8_decode($partida->contrato->descripcion),
                number_format($partida->precio_unitario_mas_descuento,2, '.', ','),
                number_format($partida->descuento,2, '.', ','),
                number_format($partida->precio_unitario,2, '.', ','),
                number_format($importe,2, '.', ','),
                )
            );
            $subtotal += $importe;

        }
        $this->SetRounds(array('4','','','','','','','','3'));
        $this->SetRadius(array(0.2,0,0,0,0,0,0,0,0.2));

        $this->totales();
    }

    function totales()
    {
        $desc_monetario = (($this->subcontrato->monto - $this->subcontrato->impuesto + $this->subcontrato->impuesto_retenido) * 100) / (100 - $this->subcontrato->PorcentajeDescuento) -
            ($this->subcontrato->monto - $this->subcontrato->impuesto + $this->subcontrato->impuesto_retenido);
        $fg_monto = ($this->subcontrato->monto - $this->subcontrato->impuesto + $this->subcontrato->impuesto_retenido) * ($this->subcontrato->retencion / 100);

        $this->encola = "";
        $y_subtotal = $this->GetY();
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 7);
        $this->Cell(17.5, .5, 'Subtotal Antes Descuento:', 0, 0, 'R');
        $this->Cell(2, .5, number_format($this->subcontrato->subtotal_antes_descuento, 2, '.', ','), 1, 0, 'R');
        $this->Ln(.5);
        $this->Cell(17.5, .5, 'Descuento Global (' . $this->subcontrato->PorcentajeDescuento . '%):', 0, 0, 'R');
        $this->Cell(2, .5, number_format($desc_monetario, 2, '.', ','), 1, 0, 'R');
        $this->Ln(.5);

        $this->Cell(17.5, .5, 'Subtotal:', 0, 0, 'R');
        $this->Cell(2, .5, number_format($this->subcontrato->subtotal, 2, '.', ','), 1, 0, 'R');
        $this->Ln(.5);
        $this->Cell(17.5, .5, 'IVA (' . $this->subcontrato->tasa_iva . '%):', 0, 0, 'R');
        $this->Cell(2, .5, number_format($this->subcontrato->impuesto, 2, '.', ','), 1, 0, 'R');
        $this->Ln(.5);
        $this->Cell(17.5, .5, 'Total:', 0, 0, 'R');
        $this->Cell(2, .5, number_format($this->subcontrato->monto, 2, '.', ','), 1, 0, 'R');
        $this->Ln(.5);
        $this->Cell(17.5, .5, 'Moneda:', 0, 0, 'R');
        $this->Cell(2, .5, $this->subcontrato->moneda->nombre, 1, 0, 'R');
        $this->Ln(.5);

        $this->SetTextColor(0, 0, 0);
        $this->Cell(17.5, .5, 'Anticipo (' . $this->subcontrato->anticipo . '%): ', 0, 0, 'R');
        $this->Cell(2, .5, number_format($this->subcontrato->anticipo_monto, 2, '.', ','), 1, 0, 'R');
        $this->Ln(.5);

        $this->SetTextColor(0, 0, 0);
        $this->Cell(17.5, .5, utf8_decode('Fondo de garantía (' . $this->subcontrato->retencion) . '%): ', 0, 0, 'R');
        $this->Cell(2, .5, number_format($fg_monto, 2, '.', ','), 1, 0, 'R');

        $fecha =New DateTime($this->subcontrato->fecha);
        if ($this->subcontrato->id_moneda != 1)
        {
            $tipo = $this->subcontrato->tipo_cambio;
            if($tipo == null)
            {
                $moneda = $this->subcontrato->id_moneda == 2 ? 1 : 2;
                $tipo_cambio = TipoCambio::where('fecha', $fecha->format("Y-m-d"))->where('moneda', $moneda)->first();
                if($tipo_cambio == null){
                    $tipo_cambio = TipoCambio::where('moneda', $moneda)->orderBy('fecha', 'desc')->first();
                }
                $tipo = $tipo_cambio->tipo_cambio;
            }
            $this->Ln(.5);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(17.5, .5, utf8_decode('Tipo Cambio MN') . ': ', 0, 0, 'R');
            $this->Cell(2, .5, number_format($tipo, 4, '.', ','), 1, 0, 'R');
        }
        $this->Ln(.7);

        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(3, .5, utf8_decode('Plazo de Ejecución:'), 0, 0,'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(1, .5, utf8_decode(' del'), 0, 0,'L');
        $this->Cell(2, .5, ' '.($this->subcontrato->subcontratos?$this->subcontrato->subcontratos->fecha_inicio_ejecucion_format:''), 1, 0,'L');
        $this->Cell(1, .5, utf8_decode(' al'), 0, 0,'L');
        $this->Cell(2, .5, ' '.($this->subcontrato->subcontratos?$this->subcontrato->subcontratos->fecha_fin_ejecucion_format:''), 1, 0,'L');
        $this->Ln(.7);

        $this->SetWidths(array(19.5));
        $this->SetRounds(array('12'));
        $this->SetRadius(array(0.2));
        $this->SetFills(array('180,180,180'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetFont('Arial', '', 9);
        $this->SetAligns(array('C'));
        $this->encola="observaciones_encabezado";
        $this->Row(array("Observaciones"));
        $this->SetRounds(array('34'));
        $this->SetRadius(array(0.2));
        $this->SetAligns(array('J'));
        $this->SetStyles(array('DF'));
        $this->SetFills(array('255,255,255'));
        $this->SetTextColors(array('0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetFont('Arial', '', 9);
        $this->SetWidths(array(19.5));
        $this->encola="observaciones";
        $this->Row(array(utf8_decode($this->subcontrato->observaciones)));
        $this->encola = 'firmas';

    }

    function footer(){
        $residuo = $this->PageNo() % 2;
        $this->SetTextColor('0,0,0');
        if($this->encola == 'firmas' && ($this->subcontrato->clasificacionSubcontrato && ($this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 4 || $this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 3 || $this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 7))){
            if(Context::getDatabase() == "SAO1814_TERMINAL_NAICM"){
                $this->SetFont('Arial', 'B', 6);
                $this->SetFont('Arial', 'B', 5);
                $this->SetY(-2.7);
                $this->Cell(2);
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->Ln(.4);
                $this->Cell(2);
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->Ln(.8);
                $this->Cell(2);
                $this->CellFitScale(4, .3, utf8_decode('Gerente / Director de Área Solicitante'), 1, 0,'C');
                $this->CellFitScale(4, .3, utf8_decode('Coordinador de Procuración'), 1, 0,'C');
                $this->CellFitScale(4, .3, utf8_decode('Gerente de Procuración'), 1, 0,'C');
                $this->CellFitScale(4, .3, ('Administrador de Subcontratos de Control de Proyectos'), 1, 0,'C');
            //$this->SetFillColor(255, 255, 255);
			}
            else if(Context::getDatabase() == "SAO1814_TUNEL_DRENAJE_PRO"){
                 //if(true){
                $this->SetFont('Arial', 'B', 6);
                $this->SetFont('Arial', 'B', 5);
                $this->SetY(-2.7);
                $this->Cell(2);
                $this->CellFitScale(5.3, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(5.3, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(5.3, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->Ln(.4);
                $this->Cell(2);
                $this->CellFitScale(5.3, .8, '', 1, 0,'C');
                $this->CellFitScale(5.3, .8, '', 1, 0,'C');
                $this->CellFitScale(5.3, .8, '', 1, 0,'C');
                $this->Ln(.8);
                $this->Cell(2);
                $this->CellFitScale(5.3, .3, utf8_decode('Jefe de Contratos'), 1, 0,'C');
                $this->CellFitScale(5.3, .3, utf8_decode('Gerente Administrativo'), 1, 0,'C');
                $this->CellFitScale(5.3, .3, ('Gerente de Proyecto'), 1, 0,'C');
                //$this->SetFillColor(255, 255, 255);
             }
            else if(Context::getDatabase() == "SAO1814_QUERETARO_SAN_LUIS" && Context::getIdObra() == 5){

                $this->SetFont('Arial', 'B', 6);
                $this->SetY(-5.7);
                // $this->Cell(3);
                $this->CellFitScale(6.6, .5, utf8_decode($this->subcontrato->empresa->razon_social), 1, 0,'C');
                $this->CellFitScale(13.3, .5, utf8_decode($this->obra->facturar), 1, 0,'C');

                $this->Ln(.5);
                // $this->Cell(3);
                $this->CellFitScale(6.6, 1.2, ' ', 1, 0,'R');
                $this->CellFitScale(6.7, 1.2, ' ', 1, 0,'R');
                $this->CellFitScale(6.6, 1.2, ' ', 1, 0,'R');
                $this->Ln(1.2);
                // $this->Cell(3);
                $this->CellFitScale(6.6, .7, ' ', 1, 0,'R');
                $this->CellFitScale(6.7, .7, ' ', 1, 0,'R');
                $this->CellFitScale(6.6, .7, ' ', 1, 0,'R');
                $this->SetY(-4);
                // $this->Cell(3);
                $this->SetFont('Arial', '', 5);
                $this->CellFitScale(6.6, .3, '', 0, 0,'C');
                $this->CellFitScale(6.7, .3, 'Apoderado Legal:', 0, 0,'C');
                $this->CellFitScale(6.6, .3, 'Apoderado Legal:', 0, 0,'C');
                $this->SetY(-3.7);
                // $this->Cell(3);
                $this->SetFont('Arial', 'B', 7);
                $this->CellFitScale(6.6, .3, 'Acepta', 0, 0,'C');
                $this->CellFitScale(6.7, .3, utf8_decode('JOSÉ ANTONIO MAGALLANES GARCÍA'), 0, 0,'C');
                $this->CellFitScale(6.6, .3, utf8_decode('ANGEL TRINIDAD MARTÍNEZ ARBOLEYA'), 0, 0,'C');

                $this->SetFont('Arial', 'B', 5);
                $this->SetY(-2.7);
                $this->Cell(2);
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->Ln(.4);
                $this->Cell(2);
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->Ln(.8);
                $this->Cell(2);
                $this->CellFitScale(4, .3, utf8_decode('Jurídico Corporativo'), 1, 0,'C');
                $this->CellFitScale(4, .3, ('Gerente de Subcontratos Corporativo'), 1, 0,'C');
                $this->CellFitScale(4, .3, ('Gerente de Seguros y Fianzas'), 1, 0,'C');
                $this->CellFitScale(4, .3, ('Gerente de Proyecto'), 1, 0,'C');
			}
            else if($this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 7 && $this->subcontrato->contratoProyectado->contratoAreaSubcontratante->id_area_subcontratante != 1 )
            {
                $this->SetFont('Arial', 'B', 6);
                $this->SetY(-5.7);
                $this->Cell(3);
                $this->CellFitScale(7, .5, utf8_decode($this->subcontrato->empresa->razon_social), 1, 0,'C');
                $this->CellFitScale(7, .5, utf8_decode($this->obra->facturar), 1, 0,'C');
                $this->Ln(.5);
                $this->Cell(3);
                $this->CellFitScale(7, 1.2, ' ', 1, 0,'R');
                $this->CellFitScale(7, 1.2, ' ', 1, 0,'R');
                $this->Ln(1.2);
                $this->Cell(3);
                $this->CellFitScale(7, .7, ' ', 1, 0,'R');
                $this->CellFitScale(7, .7, ' ', 1, 0,'R');
                $this->SetY(-4);
                $this->Cell(3);
                $this->SetFont('Arial', '', 5);
                $this->CellFitScale(7, .3, 'Acepta:', 0, 0,'C');
                $this->CellFitScale(7, .3, 'Autoriza: Director de Proyecto', 0, 2,'C');
                $this->SetY(-3.7);
                $this->Cell(3);
                $this->CellFitScale(7, .3, '', 0, 0,'C');
                $this->CellFitScale(7, .3, '', 0, 1,'C');

                $this->SetFont('Arial', 'B', 5);
                $this->SetY(-2.7);
                $this->Cell(2);
                $this->CellFitScale(5.5, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(5.5, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(5.5, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->Ln(.4);
                $this->Cell(2);
                $this->CellFitScale(5.5, .8, '', 1, 0,'C');
                $this->CellFitScale(5.5, .8, '', 1, 0,'C');
                $this->CellFitScale(5.5, .8, '', 1, 0,'C');
                $this->Ln(.8);
                $this->Cell(2);
                $this->CellFitScale(5.5, .3, utf8_decode('ELABORO'), 1, 0,'C');
                $this->CellFitScale(5.5, .3, ('CONTROL DE PROYECTO'), 1, 0,'C');
                $this->CellFitScale(5.5, .3, utf8_decode('ADMINISTRACIÓN'), 1, 0,'C');
            }
            else{
                $this->SetFont('Arial', 'B', 6);
                $this->SetY(-5.7);
                $this->Cell(3);
                $this->CellFitScale(7, .5, utf8_decode($this->subcontrato->empresa->razon_social), 1, 0,'C');
                $this->CellFitScale(7, .5, utf8_decode($this->obra->facturar), 1, 0,'C');
                $this->Ln(.5);
                $this->Cell(3);
                $this->CellFitScale(7, 1.2, ' ', 1, 0,'R');
                $this->CellFitScale(7, 1.2, ' ', 1, 0,'R');
                $this->Ln(1.2);
                $this->Cell(3);
                $this->CellFitScale(7, .7, ' ', 1, 0,'R');
                $this->CellFitScale(7, .7, ' ', 1, 0,'R');
                $this->SetY(-4);
                $this->Cell(3);
                $this->SetFont('Arial', '', 5);
                $this->CellFitScale(7, .3, 'Acepta:', 0, 0,'C');
                $this->CellFitScale(7, .3, 'Autoriza:', 0, 2,'C');
                $this->SetY(-3.7);
                $this->Cell(3);
                $this->CellFitScale(7, .3, '', 0, 0,'C');
                $this->CellFitScale(7, .3, '', 0, 1,'C');

                $this->SetFont('Arial', 'B', 5);
                $this->SetY(-2.7);
                $this->Cell(2);
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0,'C');
                $this->Ln(.4);
                $this->Cell(2);
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->CellFitScale(4, .8, '', 1, 0,'C');
                $this->Ln(.8);
                $this->Cell(2);
                $this->CellFitScale(4, .3, utf8_decode('Jurídico Corporativo'), 1, 0,'C');
                $this->CellFitScale(4, .3, ('Gerente de Subcontratos Corporativo'), 1, 0,'C');
                $this->CellFitScale(4, .3, ('Gerente de Seguros y Fianzas'), 1, 0,'C');
                $this->CellFitScale(4, .3, ('Gerente de Proyecto'), 1, 0,'C');
            }
            $this->encola = 'clausulado';
        }

        $this->SetY(-0.8);
        $this->SetFont('Arial','B',8);
        if($residuo>0 && ($this->subcontrato->clasificacionSubcontrato &&$this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 4)){
            $this->Cell(10,.3,('Terminos y condiciones adicionales al reverso.'),0,1,'L');
        }else{
            $this->Cell(10,.3,(''),0,1,'L');
        }
        $this->SetFont('Arial','BI',6);
        $this->Cell(10,.3,(utf8_decode('Formato generado desde el módulo de contratos. Fecha de registro: '. $this->subcontrato->fecha_format). ' Fecha de consulta: '.date("d-m-Y H:i:s")),0,0,'L');
        $this->Cell(9.5,.3,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
    }

    function create() {
        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,6.80);
        $this->partidas();
        if($this->subcontrato->clasificacionSubcontrato && ($this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 3 || $this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 7 || $this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 4)){
            $this->AddPage();
            $this->useTemplate($this->clausulado,0, -0.5, 22);

            if($this->paginaClausulado == 1) {
                $this->AddPage();
                $this->useTemplate($this->clausulado2, 0, -0.5, 22);
            }
        }


        try {
            $this->Output('I', "Formato - Subcontrato ".$this->subcontrato->numero_folio_format.".pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

    public function agregaPagina()
    {
        if($this->encola != 'clausulado' && ($this->subcontrato->clasificacionSubcontrato && ($this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 3 || $this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 7 || $this->subcontrato->clasificacionSubcontrato->id_tipo_contrato == 4))){
            $this->AddPageSH($this->CurOrientation, $this->CurPageSize, $this->CurRotation);
            $this->SetFont('Arial','B',90);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(2,5,utf8_decode("S I N   T E X T O"),315);
            $this->SetTextColor('0,0,0');
            $this->SetFont('Arial', '', 9);
            $this->AddPage($this->CurOrientation,  $this->CurPageSize, $this->CurRotation);
        }else{
            $this->AddPage();
        }
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->agregaPagina();
    }
    function Close()
    {
        //Terminate document
        if($this->state==3)
            return;
        if($this->page==0)
            $this->agregaPagina();
        //Page footer
        $this->InFooter=true;
        $this->Footer();
        $this->InFooter=false;
        //Close page
        $this->_endpage();
        //Close document
        $this->_enddoc();
    }

    function AddPageSH($orientation='',$size='', $rotation=0)
    {
        if($this->state==3)
            $this->Error('The document is closed');
        $family = $this->FontFamily;
        $style = $this->FontStyle.($this->underline ? 'U' : '');
        $fontsize = $this->FontSizePt;
        $lw = $this->LineWidth;
        $dc = $this->DrawColor;
        $fc = $this->FillColor;
        $tc = $this->TextColor;
        $cf = $this->ColorFlag;
        if($this->page>0)
        {
            // Page footer
            $this->InFooter = true;
            $this->Footer();
            $this->InFooter = false;
            // Close page
            $this->_endpage();
        }
        // Start new page
        $this->_beginpage($orientation,$size,$rotation);
        // Set line cap style to square
        $this->_out('2 J');
        // Set line width
        $this->LineWidth = $lw;
        $this->_out(sprintf('%.2F w',$lw*$this->k));
        // Set font
        if($family)
            $this->SetFont($family,$style,$fontsize);
        // Set colors
        $this->DrawColor = $dc;
        if($dc!='0 G')
            $this->_out($dc);
        $this->FillColor = $fc;
        if($fc!='0 g')
            $this->_out($fc);
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
        // Page header
        $this->InHeader = true;
        //$this->Header();
        $this->InHeader = false;
        // Restore line width
        if($this->LineWidth!=$lw)
        {
            $this->LineWidth = $lw;
            $this->_out(sprintf('%.2F w',$lw*$this->k));
        }
        // Restore font
        if($family)
            $this->SetFont($family,$style,$fontsize);
        // Restore colors
        if($this->DrawColor!=$dc)
        {
            $this->DrawColor = $dc;
            $this->_out($dc);
        }
        if($this->FillColor!=$fc)
        {
            $this->FillColor = $fc;
            $this->_out($fc);
        }
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
    }

    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        // Output a cell
        $k = $this->k;
        if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
        {
            // Automatic page break
            $x = $this->x;
            $ws = $this->ws;
            if($ws>0)
            {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            $this->agregaPagina();
            //$this->AddPage($this->CurOrientation,$this->CurPageSize,$this->CurRotation);
            $this->x = $x;
            if($ws>0)
            {
                $this->ws = $ws;
                $this->_out(sprintf('%.3F Tw',$ws*$k));
            }
        }
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $s = '';
        if($fill || $border==1)
        {
            if($fill)
                $op = ($border==1) ? 'B' : 'f';
            else
                $op = 'S';
            $s = sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
        }
        if(is_string($border))
        {
            $x = $this->x;
            $y = $this->y;
            if(strpos($border,'L')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'T')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
            if(strpos($border,'R')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'B')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        }
        if($txt!=='')
        {
            if(!isset($this->CurrentFont))
                $this->Error('No font has been set');
            if($align=='R')
                $dx = $w-$this->cMargin-$this->GetStringWidth($txt);
            elseif($align=='C')
                $dx = ($w-$this->GetStringWidth($txt))/2;
            else
                $dx = $this->cMargin;
            if($this->ColorFlag)
                $s .= 'q '.$this->TextColor.' ';
            $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$this->_escape($txt));
            if($this->underline)
                $s .= ' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
            if($this->ColorFlag)
                $s .= ' Q';
            if($link)
                $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
        }
        if($s)
            $this->_out($s);
        $this->lasth = $h;
        if($ln>0)
        {
            // Go to next line
            $this->y += $h;
            if($ln==1)
                $this->x = $this->lMargin;
        }
        else
            $this->x += $w;
    }

    function Cell1($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
    {
        if(is_numeric($txt)){
            if(!($txt>0) && !($txt<0)){
                $txt = "-";
            }
        }

        //Output a cell
        $k=$this->k;
        if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
        {
            //Automatic page break
            $x=$this->x;
            $ws=$this->ws;
            if($ws>0)
            {
                $this->ws=0;
                $this->_out('0 Tw');
            }
            $this->agregaPagina();
            /*$this->AddPage($this->CurOrientation);*/
            $this->x=$x;
            if($ws>0)
            {
                $this->ws=$ws;
                $this->_out(sprintf('%.3f Tw',$ws*$k));
            }
        }
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $s='';
        if($fill==1 || $border==1)
        {
            if($fill==1)
                $op=($border==1) ? 'B' : 'f';
            else
                $op='S';
            $s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
        }
        if(is_string($border))
        {
            $x=$this->x;
            $y=$this->y;
            if(strpos($border,'L')!==false)
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'T')!==false)
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
            if(strpos($border,'R')!==false)
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'B')!==false)
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        }
        if($txt!=='')
        {
            if($align=='R')
                $dx=$w-$this->cMargin-$this->GetStringWidth($txt);
            elseif($align=='C')
                $dx=($w-$this->GetStringWidth($txt))/2;
            else
                $dx=$this->cMargin;
            if($this->ColorFlag)
                $s.='q '.$this->TextColor.' ';
            $txt2=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
            $s.=sprintf('BT %.2f %.2f Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt2);
            if($this->underline)
                $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
            if($this->ColorFlag)
                $s.=' Q';
            if($link)
                $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
        }
        if($s)
            $this->_out($s);
        $this->lasth=$h;
        if($ln>0)
        {
            //Go to next line
            $this->y+=$h;
            if($ln==1)
                $this->x=$this->lMargin;
        }
        else
            $this->x+=$w;
    }


}
