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
        $this->SetFont('Helvetica', 'B', 12);
        $this->Cell(17., .5, utf8_decode('Resultados de Apretura de Concurso') , 0, 1, 'C');
        $this->setX(3.53);
        $this->Cell(17.2, .5, utf8_decode($this->concurso->nombre) , 0, 1, 'C');

    }

    public function partidasTitle()
    {
        $this->SetFont('Arial', '', 6);

        $this->setXY(1, 3.5);

        $this->SetFillColor(180,180,180);
        $this->SetWidths([0.5,1.5,8.2,2,1.5,2,2,2]);
        $this->SetStyles(['DF','DF','DF','DF','DF','DF','DF','DF']);
        $this->SetRounds(['','','','','','','','']);
        $this->SetRadius([0.2,0,0,0,0,0,0,0.2]);

        $this->SetFills(['117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117']);
        $this->SetTextColors(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
        $this->SetDrawColor(100,100,100);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C','C','C','C','C','C','C','C']);

        $this->Row(["#", "Clave SAT", utf8_decode("Descripción"),"Unidad","Cantidad", "Precio Unitario", "Total", "Descuento"]);

    }

    public function partidas()
    {
        $i = 1;
        foreach($this->concurso->participantes as $participante){
            $this->SetWidths([0.5,1.5,8.2,2,1.5,2,2,2]);
            $this->SetDrawColor(100,100,100);
            $this->SetFont('Arial', '', 7);
            $this->SetAligns(['C','L','L','C','R','R','R','R']);
            $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
            $this->SetFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
            $this->Row([
                $i,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ]);
                        $i++;
        }

    }

    function create()
    {
        return $this;
    }

}
