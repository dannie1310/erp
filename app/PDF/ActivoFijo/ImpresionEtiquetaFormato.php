<?php

namespace App\PDF\ActivoFijo;

use App\Models\ACTIVO_FIJO\Resguardo;
use Ghidev\Fpdf\Rotation;

class ImpresionEtiquetaFormato extends Rotation
{
    protected $resguardo;
    protected $partidas;
    protected $encola = '';
    protected $y_c = 0;
    protected $x_c = 0;
    protected $x_p = 0;

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 279;
    const A4_WIDTH = 216;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(Resguardo $resguardo)
    {
        parent::__construct('P', 'cm', 'Letter');
        $this->resguardo = $resguardo;
        $this->partidas = $this->resguardo->partidasEmpresa;
        $this->WidthTotal = $this->GetPageWidth();
    }

    function Header()
    {
       /* // TITULO DE TIPO DE RESGUARDO
        $this->SetFont('Arial', 'B', 90);
        $this->SetTextColor(200, 200, 200);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'BU', 11);        // Font: Arial Bold Underline Size:11
        $this->Cell(0, 0, 'a', 1, 0, 'C');

        //AREA
        $this->Ln(0.5);
        $this->Cell(0, 0, '-..-', 1, 0, 'C');
        $this->SetFont('Arial', 'B', 6);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(8.3, 0.4, utf8_decode("PRUEBA"), 0, 0, 'C');
*/
    }
    function logo($x, $y){
        $file = public_path('img/logo-empresa/GLN.png');
        $data = unpack("H*", file_get_contents($file));
        $data = bin2hex($data[1]);
        $data = pack('H*', hex2bin($data));
        $file = public_path('/img/logo_temp.png');
        if (file_put_contents($file, $data) !== false) {
            $this->Image($file, $x+3.5, $y+0.6, 0.5, 0.3);
            unlink($file);
        }
    }

    function caracteristicas(){

        $cantP = count($this->partidas)+157;
        $part = $this->partidas;
        /*foreach($part as $p){
            $p->push($p->partidaCaracteristicas);
        }
        dd($p->push($p->partidaCaracteristicas),$p->partidaCaracteristicas);
       */
        $partidas = $part->toArray();
      //  $caract1 = 0;
        $x= 4;

        for($i = 0; $i < $cantP; $i++){
            $this->x_c = $this->GetX();//0.7
            $this->y_c = $this->GetY();//1.1
            $this->x_p = $this->GetX();//0.7
            $x--;
            $this->SetXY($this->x_c, $this->y_c);
            $this->SetFont('code39', '', 6);
           // $this->Cell(3, 1.5, '*'.$partidas[$i]['CodigoEquipo'].'*', 0, 1, 'C');
            $this->Cell(4.4, 1.2, '*v7T82H6*', 1, 0, 'C');

            $this->SetFont('Arial', '', 4);
            $this->SetXY($this->x_c, $this->y_c);
            $this->Cell(4.4, 1.7, 'ACTIVO FIJO', 0, 0, 'L');

            $this->SetFont('Arial', '', 4);
            $this->SetXY($this->x_c, $this->y_c);
            $this->Cell(4.4, 1.9, 'MONITOR', 0, 0, 'C');
$this->logo($this->x_c, $this->y_c);
            //$this->image('../../img/subcontrato/LOGOTIPO_REHABILITACION_ATLACOMULCO.png',1,.3,5,2);

            $this->SetXY($this->x_c, $this->y_c);
            $this->Cell(4.4, 2.2, 'CN-05GND2-641806CK-04UT-A00-'.$x, 0, 0, 'C');

            $this->x_c = $this->x_c+0.8;
            $this->SetXY(($this->x_c + 4.4),$this->y_c);

            if($i == $cantP-4)
            {
               // dd($this->x_c, $this->y_c, $this->x_p,$x, $cantP, $i);
            }

            if($x == 0)
            {
                $this->Ln(1.28);
                $x = 4;
            }
            $currentPage = $this->PageNo();
            if($currentPage == 2) {
                dd($currentPage);
            }
            if($currentPage>1){

                $this->Ln(0.7);

                $this->SetX(0.7);
                $this->SetY(1.1);
            }
        }

 }


    function create()
    {
        $this->AddFont('code39', '', 'code39.php');
        $this->SetMargins(0.7, 1.1, 0.7);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 0.1);
        $this->caracteristicas();

        try {
            $this->Output('I', 'Formato - Impresión Etiquetas.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

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

    function pixelsToCM($val)
    {
        return ($val * self::MM_IN_INCH / self::DPI) / 10;
    }

}

