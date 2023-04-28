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

    function etiquetas()
    {
        $this->SetRounds(array('1234'));
        $this->SetRadius(array(.2));
        $this->SetFont('Arial', 'b', 9);
        $this->SetAligns(array('L'));
        $this->SetWidths(array(4.4));
        $this->SetFills(array('255,255,255'));
        $this->SetHeights(array(1.3));
       // $this->SetY($this->GetY() + 0.5);
        for($i=0; $i<20; $i++) {
            $this->Row(array(utf8_decode($i+1 . "  Prueba")));
            $this->SetX(5.2);
            $this->SetY($this->GetY() );
            $this->Row(array(utf8_decode($i+1 . "  Prueba")));
        }

    }

    function caracteristicas(){

        $cantP = count($this->partidas);
        $part = $this->partidas;
        /*foreach($part as $p){
            $p->push($p->partidaCaracteristicas);
        }
        dd($p->push($p->partidaCaracteristicas),$p->partidaCaracteristicas);
       */
        $partidas = $part->toArray();
      //  $caract1 = 0;

        for($i = 0; $i < $cantP; $i++){
            $j = 0;
            $this->x_c = $this->GetX();//0.7
            $this->y_c = $this->GetY();//1.1
            $this->x_p = $this->GetX();//0.7

            $this->SetXY($this->x_c,$this->y_c);
            $this->SetFont('code39', '', 7);
            $this->Cell(3, 2, '*'.$partidas[$i]['CodigoEquipo'].'*', 0, 0, 'C');
            if(array_key_exists($i+1, $partidas)){
                $this->SetFont('code39', '', 7);
                $this->x_p = $this->x_c+5;
                $this->SetXY(($this->x_c + 3.8),$this->y_c);
                $this->Cell(3, 2, '*'.$partidas[$i+1]['CodigoEquipo'].'*', 0, 0, 'C');
            }

        }$this->SetXY(($this->x_c),$this->y_c+1.3);


 }


    function create()
    {
        $this->AddFont('code39', '', 'code39.php');
        $this->SetMargins(0.7, 1.1, 0.7);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 0.5);
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

