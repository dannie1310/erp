<?php

namespace App\PDF\Concurso;


use Ghidev\Fpdf\Rotation;


class InformeCierre extends Rotation
{
    protected $concurso;

    const DPI = 96;
    const MAX_WIDTH = 180;
    const MAX_HEIGHT = 150;
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
        $this->partidasTitle();
        $this->partidas();
        $this->resumen();
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

    public function partidasTitle()
    {
        $this->SetFont('Arial', '', 12);

        $this->setXY(1, 3.5);

        $this->SetFillColor(180,180,180);
        $this->SetWidths([1,13.8,5]);
        $this->SetStyles(['DF','DF','DF','DF','DF','DF','DF','DF']);
        $this->SetRounds(['','','','','','','','']);
        $this->SetRadius([0.2,0,0,0,0,0,0,0.2]);

        $this->SetFills(['117,117,117','117,117,117','117,117,117']);
        $this->SetTextColors(['255,255,255','255,255,255','255,255,255']);
        $this->SetDrawColor(100,100,100);
        $this->SetHeights([0.7]);
        $this->SetAligns(['C','C','C']);


        $this->Row(["#", "Participante", "Oferta",]);

    }

    public function partidas()
    {
        $i = 1;
        foreach($this->concurso->participantesOrdenados as $participante){

            if ($participante->es_empresa_hermes == 1) {
                $this->SetTextColors([
                    "125,182,70"
                    , "125,182,70"
                    , "125,182,70"
                ]);
                $this->SetFills(['240,240,240','240,240,240','240,240,240']);
            }else{
                $this->SetTextColors(['0,0,0','0,0,0','0,0,0']);
                $this->SetFills(['255,255,255','255,255,255','255,255,255']);
            }

            $this->SetWidths([1,13.8,5]);
            $this->SetDrawColor(100,100,100);
            $this->SetFont('Arial', '', 12);
            $this->SetAligns(['C','L','R']);

            $this->Row([
                $i,
                utf8_decode($participante->nombre),
                $participante->monto_format,

            ]);
            $i++;
        }

    }

    public function resumen()
    {
        $this->SetFont('Helvetica', 'B', 13);
        $this->SetFills('117,117,117');

        $this->ln(1);
        $this->cell(19.7,.5,utf8_decode("Resúmen"),0,1,"C", true);
        $this->ln();
        $this->SetFont('Arial', 'B', 12);
        $this->cell(3,.5,utf8_decode("Promedio:"),0,0,"L");
        $this->SetFont('Arial', '', 12);
        $this->cell(5,.5, $this->concurso->promedio_format,0,0,"R");
        $this->cell(.7,.5);
        $this->SetFont('Arial', 'B', 12);
        $this->cell(6,.5,utf8_decode("Distancia al Primer Lugar:"),0,0,"L");
        $this->SetFont('Arial', '', 12);
        $this->cell(5,.5, $this->concurso->participanteHermes->distancia_primer_lugar_format ." (".$this->concurso->participanteHermes->distancia_primer_lugar_porcentaje.")",0,1,"R");

    }

    function create()
    {
        return $this;
    }

}
