<?php

namespace App\PDF\Concurso;


use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Graph\PieGraph;
use Amenadiel\JpGraph\Plot\AccBarPlot;
use Amenadiel\JpGraph\Plot\BarPlot;
use Amenadiel\JpGraph\Plot\GroupBarPlot;
use Amenadiel\JpGraph\Plot\LinePlot;
use Amenadiel\JpGraph\Plot\PiePlot;
use Amenadiel\JpGraph\Plot\PlotBand;
use Amenadiel\JpGraph\Themes\UniversalTheme;
use Ghidev\Fpdf\Rotation;


class InformeCierre extends Rotation
{
    protected $concurso;

    const DPI = 96;
    const MAX_WIDTH = 180;
    const MAX_HEIGHT = 150;
    const MAX_WIDTH_GRAPH = 750;
    const MAX_HEIGHT_GRAPH = 525;
    const MM_IN_INCH = 25.4;

    private $en_cola = '';

    public function __construct($concurso)
    {
        parent::__construct("P", "cm", "Letter");
        $this->concurso = $concurso;

        $this->SetMargins(1, 1, 2);
        $this->SetAutoPageBreak(true, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->datosGenerales();
        $this->partidasTitle();
        $this->partidas();
        $this->resumen();
        if($this->concurso->participantes()->count()>0)
        {
            $this->generaGrafica();
            $this->muestraGrafica();
        }
    }

    function SetFillColor($r, $g=null, $b=null)
    {
        $datos = explode(',', $r);
        if(count($datos)==3){
            $r = $datos[0];
            $g = $datos[1];
            $b = $datos[2];
        }else{
            $r = $r;
            $g = $g;
            $b = $b;
        }
        // Set color for all filling operations
        if(($r==0 && $g==0 && $b==0) || $g===null) {
            $this->FillColor = sprintf('%.3F g', $datos[0]/ 255);
        }
        else {
            $this->FillColor = sprintf('%.3F %.3F %.3F rg', $r / 255, $g / 255, $b / 255);
        }
        $this->ColorFlag = ($this->FillColor!=$this->TextColor);
        if($this->page>0)
            $this->_out($this->FillColor);
    }

    function SetTextColor($r, $g=null, $b=null)
    {
        $datos = explode(',', $r);
        if(count($datos)==3){
            $r = $datos[0];
            $g = $datos[1];
            $b = $datos[2];
        }else{
            $r = $r;
            $g = $g;
            $b = $b;
        }

        // Set color for text
        if(($r==0 && $g==0 && $b==0) || $g===null) {

            $this->TextColor = sprintf('%.3F g', $datos[0] / 255);
        }
        else {
            $this->TextColor = sprintf('%.3F %.3F %.3F rg'
                , ($r/255)
                , ($g/255)
                , ($b/255)
            );
        }
        $this->ColorFlag = ($this->FillColor!=$this->TextColor);
    }

    function logo()
    {
        list($width, $height) = $this->resizeToFit(public_path('/img/logo_hc.png'));
        $this->Image(public_path('/img/logo_hc.png'), 0.6, 0.5, $width-2, $height-1);
    }
    function pixelsToCM($val)
    {
        return ($val * self::MM_IN_INCH / self::DPI) / 10;
    }
    function resizeToFit($imgFilename)
    {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);
        return [
            round($this->pixelsToCM($scale * $width)),
            round($this->pixelsToCM($scale * $height))
        ];
    }

    function resizeToFitGraph($imgFilename)
    {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH_GRAPH / $width;
        $heightScale = self::MAX_HEIGHT_GRAPH / $height;
        $scale = min($widthScale, $heightScale);
        return [
            round($this->pixelsToCM($scale * $width)),
            round($this->pixelsToCM($scale * $height))
        ];
    }

    function Header()
    {
        $this->logo();
        $this->setXY(3.53, 1.2);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Helvetica', '', 13);
        $this->Cell(17., .5, utf8_decode('Resultados de Apertura de Concurso') , 0, 1, 'C');
        $this->setX(3.53);
        $this->SetFont('Helvetica', 'B', 13);
        $this->Cell(17.2, .5, utf8_decode($this->concurso->nombre) , 0, 1, 'C');
    }

    function Footer() {
        $this->SetY($this->GetPageHeight() - 1);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', '', 6);
        $this->Cell(6.5, .4, utf8_decode('Fecha de Consulta ').date("d/m/Y H:i:s"), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 6);
        $this->Cell(29, .4, '', 0, 0, 'C');
        $this->Cell(6.5, .4, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    public function datosGenerales()
    {
        $this->setXY(1, 3);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(4, .5, utf8_decode('Fecha: '), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(6.5, .5, $this->concurso->fecha_format, 0, 0, 'L');

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(7, .5, utf8_decode('Estado de Apertura: '), 0, 0, 'R');
        $this->SetFont('Arial', '', 10);
        $this->Cell(2, .5, $this->concurso->estado, 0, 1, 'R');

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(4, .5, utf8_decode('No. de Licitación: '), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(6.5, .5, $this->concurso->numero_licitacion, 0, 1, 'L');

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(4, .5, utf8_decode('Entidad Licitante: '), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(6.5, .5, $this->concurso->entidad_licitante, 0, 1, 'L');

        $this->ln();

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(4, .5, utf8_decode('Resultado: '), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(15.7, .5, $this->concurso->resultado_txt, 0, 1, 'L');
    }

    public function partidasTitle()
    {
        $this->SetFont('Arial', '', 9);



        $this->SetFillColor(180,180,180);

        $this->SetDrawColor(100,100,100);
        $this->SetHeights([0.7]);
        $this->SetAligns(['C','C','C','C']);

        $this->SetWidths([1,9.8,3,6]);
        $this->SetStyles(['DF','DF','DF','DF']);
        $this->SetRounds(['','','','']);
        $this->SetRadius([0.2,0,0,0.2]);

        $this->SetFills(['117,117,117','117,117,117','117,117,117','117,117,117']);
        $this->SetTextColors(['255,255,255','255,255,255','255,255,255','255,255,255']);

        $this->Row(["", "", "Montos", utf8_decode("% en relación a")]);

        $this->SetWidths([1,9.8,3,2,2,2]);
        $this->SetStyles(['DF','DF','DF','DF','DF','DF']);
        $this->SetRounds(['','','','','','']);
        $this->SetRadius([0.2,0,0,0,0,0.2]);
        $this->SetAligns(['C','C','C','C','C','C']);

        $this->SetFills(['117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117']);
        $this->SetTextColors(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
        $this->Row(["#", "Participante", "Sin IVA", "1er Lugar", "Promedio", "Hermes"]);

    }

    public function partidas()
    {
        $i = 1;
        foreach($this->concurso->participantes_para_informe as $participante){

            if ($participante->es_empresa_hermes == 1) {
                $this->SetFills([
                    "125,182,70"
                    , "125,182,70"
                    , "125,182,70"
                    , "125,182,70"
                    , "125,182,70"
                    , "125,182,70"
                ]);
                //$this->SetFills(['240,240,240','240,240,240','240,240,240','240,240,240','240,240,240','240,240,240']);
            }else{
                $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
                $this->SetFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
            }

            $this->SetWidths([1,9.8,3,2,2,2]);
            $this->SetDrawColor(100,100,100);
            $this->SetFont('Arial', '', 9);
            $this->SetAligns(['C','L','R','R','R','R']);

            if($participante->nombre != "PROMEDIO")
            {
                $this->Row([
                    $i,
                    utf8_decode($participante->nombre),
                    $participante->monto_format,
                    $participante->porcentaje_vs_primer_lugar,
                    $participante->porcentaje_vs_promedio,
                    $participante->porcentaje_vs_hermes,
                ]);
                $i++;
            }
            else{
                $this->SetFills(['249,203,152','249,203,152','249,203,152','249,203,152','249,203,152','249,203,152']);
                $this->SetAligns(['C','R','R','R','R','R']);
                $this->Row([
                    "",
                    utf8_decode($participante->nombre),
                    $participante->monto_format,
                    $participante->porcentaje_vs_primer_lugar,
                    $participante->porcentaje_vs_promedio,
                    $participante->porcentaje_vs_hermes,
                ]);
            }
        }

    }

    public function resumen()
    {
        $this->SetTextColor('0,0,0');
        $this->SetFont('Helvetica', 'B', 13);
        $this->SetFills('117,117,117');

        $this->ln();
        $this->SetFont('Arial', 'B', 9);
        $this->cell(3,.5,utf8_decode("Promedio:"),0,0,"L");
        $this->SetFont('Arial', '', 9);
        $this->cell(3,.5, $this->concurso->promedio_format,0,0,"R");
        $this->cell(.7,.5);
        $this->SetFont('Arial', 'B', 9);
        $this->cell(8,.5,utf8_decode("Diferencia Oferta Hermes vs Primer Lugar:"),0,0,"L");
        $this->SetFont('Arial', '', 9);
        if($this->concurso->participanteHermes)
        {
            $this->cell(5,.5, $this->concurso->participanteHermes->distancia_primer_lugar_format ." (".$this->concurso->participanteHermes->distancia_primer_lugar_porcentaje.")",0,0,"R");

        }else{
            $this->SetTextColor('255,99,99');

            $this->SetFont('Arial', 'I', 9);
            $this->cell(5,.5, "No Registrada",0,0,"R");

        }

        $this->ln();
    }

    public function generaGrafica()
    {
        $saltos_grafica = $this->concurso->saltos_grafica;
//ofertas
        $data1y = $this->concurso->datos_ofertas_grafica;
//oferta hermes
        $data2y = $this->concurso->datos_oferta_hermes_grafica;
//promedio
        $data6y = $this->concurso->datos_promedio_grafica;
//primer lugar
        $data5y = $this->concurso->datos_oferta_ganadora_grafica;

//hermes
        $data4y = $this->concurso->datos_oferta_hermes_linea;

        $indicador_hermes = $this->concurso->datos_indicador_hermes;


// Create the graph. These two calls are always required
        $graph = new Graph(750,320,'auto');
        $graph->SetScale("textlin");
        $graph->SetY2Scale("lin",0,90);
        $graph->SetY2OrderBack(false);

        $theme_class = new UniversalTheme();
        $graph->SetTheme($theme_class);

        $graph->SetMargin(40,20,46,80);

        $graph->yaxis->SetTickPositions($saltos_grafica[0]
            , $saltos_grafica[1]);

        $graph->SetBox(false);

        $graph->ygrid->SetFill(false);
        $graph->xaxis->SetTickLabels(array('A','B','C','D'));
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false,false);
// Setup participantes as labels on the X-axis
        $graph->xaxis->SetTickLabels($this->concurso->labels_participantes);

// Create the bar plot ofertas
        $b1plot = new BarPlot($data1y);
// Create the bar plot oferta hermes
        $b2plot = new BarPlot($data2y);
// Create the line plot promedio
        $lplot = new LinePlot($data6y);

// Create the line plot mejor oferta
        $l2plot = new LinePlot($data5y);
// Create the line plot oferta hermes
        $l3plot = new LinePlot($data4y);

// ...and add it to the graPH
        $graph->Add($b1plot);
        $graph->Add($b2plot);
        $graph->Add($lplot);
        $graph->Add($l2plot);
        $graph->Add($l3plot);

        $b1plot->SetColor("#757575");
        $b1plot->SetFillColor("#757575");
        $b1plot->SetLegend("Ofertas");

        $b2plot->SetColor("#7DB646");
        $b2plot->SetFillColor("#7DB646");
        $b2plot->SetLegend("Oferta Hermes");

        $lplot->SetBarCenter();
        $lplot->SetColor("red");
        $lplot->SetLegend("Promedio");
        $lplot->mark->SetType(MARK_X,'',1.0);
        $lplot->mark->SetWeight(2);
        $lplot->mark->SetWidth(8);
        $lplot->mark->setColor("red");
        $lplot->mark->setFillColor("red");

        $l2plot->SetBarCenter();
        $l2plot->SetColor("yellow");
        $l2plot->SetLegend("Primer Lugar");
        $l2plot->mark->SetType(MARK_X,'',1.0);
        $l2plot->mark->SetWeight(2);
        $l2plot->mark->SetWidth(8);
        $l2plot->mark->setColor("yellow");
        $l2plot->mark->setFillColor("yellow");

        $l3plot->SetBarCenter();
        $l3plot->SetColor([125,182,70]);
        $l3plot->SetLegend("Oferta Hermes");
        $l3plot->mark->SetType(MARK_X,'',1.0);
        $l3plot->mark->SetWeight(2);
        $l3plot->mark->SetWidth(8);
        $l3plot->mark->setColor([125,182,70]);
        $l3plot->mark->setFillColor([125,182,70]);

        $graph->legend->SetFrameWeight(1);
        $graph->legend->SetColumns(6);
        $graph->legend->SetColor('#4E4E4E','#00A78A');

        /*$band = new PlotBand(VERTICAL,BAND_RDIAG,$indicador_hermes[0],$indicador_hermes[1], [125,182,70]);
        $band->ShowFrame(true);
        $band->SetOrder(DEPTH_BACK);
        $graph->Add($band);*/

        $graph->title->Set("Ofertas de Licitación para Proyecto ".$this->concurso->nombre);
        @unlink(public_path('downloads/concursos/graficas/'.$this->concurso->id.".png"));
        $graph->Stroke(public_path('downloads/concursos/graficas/'.$this->concurso->id.".png"));

    }

    public function generaGrafica1()
    {

        $data1y=array(115,130,135,130,110,130,130,150,130,130,150,120);
//bar2
        $data2y=array(180,200,220,190,170,195,190,210,200,205,195,150);
//bar3
        $data3y=array(220,230,210,175,185,195,200,230,200,195,180,130);
        $data4y=array(40,45,70,80,50,75,70,70,80,75,80,50);
        $data5y=array(20,20,25,22,30,25,35,30,27,25,25,45);
//line1
        $data6y=array(50,58,60,58,53,58,57,60,58,58,57,50);
        foreach ($data6y as &$y) { $y -=10; }

// Create the graph. These two calls are always required
        $graph = new Graph(750,320,'auto');
        $graph->SetScale("textlin");
        $graph->SetY2Scale("lin",0,90);
        $graph->SetY2OrderBack(false);

        $theme_class = new UniversalTheme();
        $graph->SetTheme($theme_class);

        $graph->SetMargin(40,20,46,80);

        $graph->yaxis->SetTickPositions(array(0,50,100,150,200,250,300,350), array(25,75,125,175,275,325));
        $graph->y2axis->SetTickPositions(array(30,40,50,60,70,80,90));

        $months = ["Ene","Feb","Mzo","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];
        $months = array_merge(array_slice($months,3,9), array_slice($months,0,3));
        $graph->SetBox(false);

        $graph->ygrid->SetFill(false);
        $graph->xaxis->SetTickLabels(array('A','B','C','D'));
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false,false);
// Setup month as labels on the X-axis
        $graph->xaxis->SetTickLabels($months);

// Create the bar plots
        $b1plot = new BarPlot($data1y);
        $b2plot = new BarPlot($data2y);

        $b3plot = new BarPlot($data3y);
        $b4plot = new BarPlot($data4y);
        $b5plot = new BarPlot($data5y);

        $lplot = new LinePlot($data6y);

// Create the grouped bar plot
        $gbbplot = new AccBarPlot(array($b3plot,$b4plot,$b5plot));
        $gbplot = new GroupBarPlot(array($b1plot,$b2plot,$gbbplot));

// ...and add it to the graPH
        $graph->Add($gbplot);
        $graph->AddY2($lplot);

        $b1plot->SetColor("#0000CD");
        $b1plot->SetFillColor("#0000CD");
        $b1plot->SetLegend("Cliants");

        $b2plot->SetColor("#B0C4DE");
        $b2plot->SetFillColor("#B0C4DE");
        $b2plot->SetLegend("Machines");

        $b3plot->SetColor("#8B008B");
        $b3plot->SetFillColor("#8B008B");
        $b3plot->SetLegend("First Track");

        $b4plot->SetColor("#DA70D6");
        $b4plot->SetFillColor("#DA70D6");
        $b4plot->SetLegend("All");

        $b5plot->SetColor("#9370DB");
        $b5plot->SetFillColor("#9370DB");
        $b5plot->SetLegend("Single Only");

        $lplot->SetBarCenter();
        $lplot->SetColor("yellow");
        $lplot->SetLegend("Houses");
        $lplot->mark->SetType(MARK_X,'',1.0);
        $lplot->mark->SetWeight(2);
        $lplot->mark->SetWidth(8);
        $lplot->mark->setColor("yellow");
        $lplot->mark->setFillColor("yellow");

        $graph->legend->SetFrameWeight(1);
        $graph->legend->SetColumns(6);
        $graph->legend->SetColor('#4E4E4E','#00A78A');

        $band = new PlotBand(VERTICAL,BAND_RDIAG,11,"max",'khaki4');
        $band->ShowFrame(true);
        $band->SetOrder(DEPTH_BACK);
        $graph->Add($band);

        $graph->title->Set("Combined Line and Bar plots");

        $graph->Stroke(public_path('downloads/concursos/graficas/'.$this->concurso->id.".png"));

    }

    public function muestraGrafica()
    {
        $y = $this->getY()+0.5;
        $x = $this->getX();
        list($width, $height) = $this->resizeToFitGraph(public_path('downloads/concursos/graficas/'.$this->concurso->id.".png"));
        $this->Image(public_path('downloads/concursos/graficas/'.$this->concurso->id.".png"), $x, $y,$width,$height);

    }

    public function grafica1()
    {
        $y = $this->getY();
        $x = $this->getX();

        $graph = new PieGraph(350, 250);
        $graph->title->Set("");
        $graph->SetBox(true);

        $data = array(40, 21, 17, 14, 23);
        $p1   = new PiePlot($data);
        $p1->ShowBorder();
        $p1->SetColor('black');
        $p1->SetSliceColors(array('#1E90FF', '#2E8B57', '#ADFF2F', '#DC143C', '#BA55D3'));

        $graph->Add($p1);
        $graph->Stroke(public_path('downloads/concursos/graficas/'.$this->concurso->id.".png"));
        $this->Image(public_path('downloads/concursos/graficas/'.$this->concurso->id.".png"),$x,$y,19.7,13);

    }

    function create()
    {
        return $this;
    }

}
