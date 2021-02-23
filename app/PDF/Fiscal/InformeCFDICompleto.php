<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/07/2020
 * Time: 04:56 PM
 */

namespace App\PDF\Fiscal;


use Ghidev\Fpdf\Rotation;

class InformeCFDICompleto extends Rotation
{
    protected $informe;
    protected $empresa_en_cola;
    protected $pintar_empresa;

    const DPI = 96;
    const MAX_WIDTH = 180;
    const MAX_HEIGHT = 150;
    const MM_IN_INCH = 25.4;

    private $en_cola = '';

    public function __construct($informe)
    {
        parent::__construct("L", "cm", array(27.94,43.18));
        $this->informe = $informe["informe"];
        foreach($this->informe["empresas"] as $rfc=>$empresa){
            $this->empresa_en_cola = $empresa;
            break;
        }
        $this->SetDrawColor(213,213,213);
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
        $this->MultiCell(39, .5, utf8_decode('Informe Completo de CFDI Recibidos') , 0, 'C', 0);
        $this->setXY(7.59, 1.7);
        $this->SetFont('Helvetica', '', 7);
        $this->titulo();
        $this->tituloEmpresa();
        if($this->en_cola != ''){
            $this->setEstilos($this->en_cola);
        }
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



    public function partidas()
    {
        $this->SetFont('Arial', '', 6);
        foreach($this->informe["empresas"] as $rfc=>$empresa){

            $this->empresa_en_cola = $empresa;
            $this->setEstilos("empresa");
            $this->en_cola = "empresa";
            $this->Cell(1, .4, $empresa["k"], 1, 0, 'C',true);
            $this->Cell(41, .4, utf8_decode($empresa["valor"]), 1, 1, 'L',true);

            foreach ($this->informe["anios_empresa"][$rfc] as $anio_empresa)
            {

                $this->setEstilos("partida");
                $this->en_cola = "partida";
                $this->Cell(1, .4, $anio_empresa, 1, 0, 'C',true);
                foreach($this->informe["meses"] as $mes)
                {
                    if(key_exists($mes["id"],$this->informe["valores"][$rfc][$anio_empresa]))
                    {
                        $this->Cell(1, .4, $this->informe["valores"][$rfc][$anio_empresa][$mes["id"]]["cantidad"], 1, 0, 'R',true);
                    } else {
                        $this->Cell(1, .4, "-", 1, 0, 'R',true);
                    }

                    if(key_exists($mes["id"],$this->informe["valores"][$rfc][$anio_empresa]))
                    {
                        $this->Cell(2.15, .4, $this->informe["valores"][$rfc][$anio_empresa][$mes["id"]]["total"], 1, 0, 'R',true);
                    } else {
                        $this->Cell(2.15, .4, "-", 1, 0, 'R',true);
                    }

                }

                $this->Cell(1, .4, $this->informe["valores"][$rfc][$anio_empresa]["totales"]["cantidad_f"], 1, 0, 'R',true);
                $this->Cell(2.15, .4, $this->informe["valores"][$rfc][$anio_empresa]["totales"]["total_f"], 1, 0, 'R',true);

                $this->Ln();

            }

            if(count($this->informe["anios_empresa"][$rfc])>1)
            {
                $this->setEstilos("subtotal");
                $this->en_cola = "subtotal";
                $this->Cell(1, .4, '', 1, 0, 'C',true);
                foreach($this->informe["meses"] as $mes)
                {
                    if(key_exists($mes["id"],$this->informe["valores"][$rfc]["totales"]))
                    {
                        $this->Cell(1, .4, $this->informe["valores"][$rfc]["totales"][$mes["id"]]["cantidad_f"], 1, 0, 'R',true);
                    } else {
                        $this->Cell(1, .4, "-", 1, 0, 'R',true);
                    }

                    if(key_exists($mes["id"],$this->informe["valores"][$rfc]["totales"]))
                    {
                        $this->Cell(2.15, .4, $this->informe["valores"][$rfc]["totales"][$mes["id"]]["total_f"], 1, 0, 'R',true);
                    } else {
                        $this->Cell(2.15, .4, "-", 1, 0, 'R',true);
                    }

                }

                $this->Cell(1, .4, $this->informe["valores"][$rfc]["totales"]["cantidad_f"], 1, 0, 'R',true);
                $this->Cell(2.15, .4, $this->informe["valores"][$rfc]["totales"]["total_f"], 1, 0, 'R',true);
                $this->Ln();
            }
            $this->Ln();
        }

        $this->setEstilos("total");
        $this->Cell(1, .4, '', 1, 0, 'C',true);
        foreach($this->informe["meses"] as $mes)
        {
            if(key_exists($mes["id"],$this->informe["valores"]["totales"]))
            {
                $this->Cell(1, .4, $this->informe["valores"]["totales"][$mes["id"]]["cantidad_f"], 1, 0, 'R',true);
            } else {
                $this->Cell(1, .4, "-", 1, 0, 'R',true);
            }

            if(key_exists($mes["id"],$this->informe["valores"]["totales"]))
            {
                $this->Cell(2.15, .4, $this->informe["valores"]["totales"][$mes["id"]]["total_f"], 1, 0, 'R',true);
            } else {
                $this->Cell(2.15, .4, "-", 1, 0, 'R',true);
            }

        }

        $this->Cell(1, .4, $this->informe["valores"]["totales"]["cantidad_f"], 1, 0, 'R',true);
        $this->Cell(2.15, .4, $this->informe["valores"]["totales"]["total_f"], 1, 0, 'R',true);
        $this->Ln();

    }
    private function titulo(){
        $this->setEstilos("titulo");
        $this->setXY(0.6, 3);
        $this->Cell(1,0.4,'');
        foreach ($this->informe["meses"] as $mes)
        {
            $this->Cell(3.15,0.4,$mes["mes_txt"],1,0,"C",true);
        }
        $this->Cell(3.15,0.4,"TOTAL",1,0,"C", true);
        $this->Ln();

    }
    private function tituloEmpresa()
    {
        $this->setEstilos("empresa");
        $this->Cell(1, .4, $this->empresa_en_cola["k"], 1, 0, 'C',true);
        $this->Cell(41, .4, utf8_decode($this->empresa_en_cola["valor"]), 1, 1, 'L',true);
        if($this->en_cola == "empresa" || $this->en_cola == "" )
        {
            $y = $this->getY();
            $this->setY( $y-0.4);
        }

    }
    private function  setEstilos($tipo){
        if($tipo == "partida"){
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(255,255,255);
            $this->SetDrawColor(213,213,213);
        } else if($tipo == "empresa"){
            $this->SetFont('Arial', '', 7);
            $this->SetFillColor(190,190,190);
            $this->SetDrawColor(213,213,213);
        } else if($tipo == "subtotal"){
            $this->SetDrawColor(213,213,213);
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(190,190,190);
        } else if($tipo == "total"){
            $this->SetDrawColor(100,100,100);
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(190,190,190);
        } else if( $tipo == "titulo"){
            $this->SetFont('Arial', 'B', 7);
            $this->SetFillColor(255,255,255);
            $this->SetDrawColor(213,213,213);
        }
    }

    function create()
    {
        $this->SetMargins(0.6, .5, 0.6);
        $this->SetAutoPageBreak(true, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->partidas();

        try {
            $this->Output('I', 'Informe Completo de CFDI Recibidos _'.date("d-m-Y h:i:s").'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }

}
