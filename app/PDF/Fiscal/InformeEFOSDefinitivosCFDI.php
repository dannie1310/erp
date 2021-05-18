<?php

namespace App\PDF\Fiscal;


use Ghidev\Fpdf\Rotation;

class InformeEFOSDefinitivosCFDI extends Rotation
{
    protected $informe;
    protected $etiqueta_titulo;

    const DPI = 96;
    const MAX_WIDTH = 180;
    const MAX_HEIGHT = 150;
    const MM_IN_INCH = 25.4;

    private $en_cola = '';

    public function __construct($informe)
    {
        parent::__construct("P", "cm", "Letter");
        $this->informe = $informe;
        $this->SetMargins(.7, .5, 1);
        $this->SetAutoPageBreak(true, 1);
        $this->AliasNbPages();
        $this->partidas();
    }

    function logo()
    {
        list($width, $height) = $this->resizeToFit(public_path('/img/logo_hc.png'));
        $this->Image(public_path('/img/logo_hc.png'), 0.5, 0.5, $width-2, $height-1);
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
        $this->setXY(3.59, 1.2);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Helvetica', 'B', 12);
        $this->MultiCell(17, .5, utf8_decode('Informe de Revisión de Listado de EFOS Definitivos del SAT vs CFDI Recibidos') , '0', 'C', 0);
        $this->setXY(7.59, 1.7);
        $this->SetFont('Helvetica', '', 7);
        $this->Cell(16,.3,utf8_decode($this->informe["fechas"]["lista_efos"]).' / '.utf8_decode($this->informe["fechas"]["cfd_recibidos"]),0,1,"L");
        $this->titulo();
        if($this->en_cola != "subtotal" && $this->en_cola != "total"){
            $this->partidasTitle();
        }
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
        $this->Cell(7.3, .4, '', 0, 0, 'C');
        $this->Cell(6.5, .4, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    public function partidasTitle()
    {
        $this->SetFont('Arial', '', 7);

        $this->setXY(0.7, 3);

        $this->SetFillColor(180,180,180);
        $this->SetWidths([0.5,2.2,6,1.5,1.5,1.5,1.5,2.2,1,2.4]);
        $this->SetStyles(['DF','DF','DF','DF','DF','DF','DF','DF','DF','DF']);
        $this->SetRounds(['','','','','','','','','','']);
        $this->SetRadius([0.2,0,0,0,0,0,0,0,0,0.2]);

        $this->SetFills(['117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117','117,117,117']);
        $this->SetTextColors(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
        $this->SetDrawColor(117,117,117);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C','C','C','C','C','C','C','C','C','C']);
        if($this->etiqueta_titulo == "DEFINITIVOS"){
            $this->Row(["#","RFC", utf8_decode("Razón Social"), "Fecha Definitivo SAT", "Fecha Definitivo DOF", utf8_decode("Fecha Límite de Aclaración (SAT)"), utf8_decode("Fecha Límite de Aclaración (DOF)"), "Empresa", "# CFDI", "Importe Incluyendo IVA"]);
        }
    }

    public function partidas()
    {
        foreach($this->informe["informe"] as $tipo){
            foreach($tipo as $partida){
                if(key_exists("tipo", $partida)){
                    $this->en_cola = $partida["tipo"];
                    $this->setEstilos($partida["tipo"]);
                    if($partida["tipo"]== "partida"){
                        if($this->etiqueta_titulo == "DEFINITIVOS"){
                            $this->Row([$partida["indice"],$partida["rfc"], utf8_decode($partida["razon_social"]), $partida["fecha_definitivo"], $partida["fecha_definitivo_dof"],$partida["fecha_limite_sat"], $partida["fecha_limite_dof"], utf8_decode($partida["empresa"]), $partida["no_CFDI"], $partida["importe_format"]]);
                        }
                    } else if($partida["tipo"]== "titulo"){
                        $this->etiqueta_titulo = $partida["etiqueta"];
                        $this->AddPage();
                    } else if($partida["tipo"]== "total"){
                        $this->Row([$partida["contador"],'', utf8_decode($partida["etiqueta"]), $partida["contador_cfdi"], $partida["importe_format"]]);
                    } else {
                        $this->Row([$partida["contador"],'', utf8_decode($partida["etiqueta"]), $partida["contador_cfdi"], $partida["importe_format"]]);
                    }
                }
            }
        }
        $this->Ln();
    }
    private function titulo(){
        $this->setXY(0.7, 2.32);
        $this->setEstilos("titulo");
        $this->Row([utf8_decode($this->etiqueta_titulo)]);
    }
    private function  setEstilos($tipo){
        if($tipo == "partida"){
            $this->SetDrawColor(213,213,213);
            $this->SetFont('Arial', '', 7);
            $this->SetFillColor(255,255,255);
            $this->SetWidths([0.5,2.2,6,1.5,1.5,1.5,1.5,2.2,1,2.4]);
            $this->SetStyles(['DF','DF','DF','DF','DF','DF','DF','DF','DF','DF']);
            $this->SetRounds(['','','','','','','','','','']);
            $this->SetRadius([0,0,0,0,0,0,0,0,0,0]);
            $this->SetFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
            $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
            $this->SetHeights([0.4]);
            $this->SetAligns(['C','C','L','C','C','C','C','L','R','R']);
        } else if($tipo == "subtotal"){
            $this->SetDrawColor(213,213,213);
            $this->SetFont('Arial', '', 7);
            $this->SetFillColor(255,255,255);
            $this->SetWidths([0.5,2.2,14.2,1,2.4]);
            $this->SetStyles(['DF','DF','DF','DF','DF']);
            $this->SetRounds(['','','','','','','','']);
            $this->SetRadius([0,0,0,0,0]);
            $this->SetFills(['213,213,213','213,213,213','213,213,213','213,213,213','213,213,213']);
            $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
            $this->SetHeights([0.4]);
            $this->SetAligns(['C','C','L','R','R']);
        }  else if($tipo == "total"){
            $this->SetDrawColor(117,117,117);
            $this->SetFont('Arial', '', 7);
            $this->SetFillColor(255,255,255);
            $this->SetWidths([0.5,2.2,14.2,1,2.4]);
            $this->SetStyles(['DF','DF','DF','DF','DF']);
            $this->SetRounds(['','','','','']);
            $this->SetRadius([0.2,0,0,0,0.2]);
            $this->SetFills(['117,117,117','117,117,117','117,117,117','117,117,117','117,117,117']);
            $this->SetTextColors(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
            $this->SetHeights([0.4]);
            $this->SetAligns(['C','C','L','R','R']);
        }
        else if( $tipo == "titulo"){
            $this->SetDrawColor(255,255,255);
            $this->SetFont('Arial', 'B', 9);
            $this->SetFillColor(255,255,255);
            $this->SetWidths([20.3]);
            $this->SetStyles(['DF']);
            $this->SetRounds(['']);
            $this->SetRadius([0]);
            $this->SetFills(['255,255,255']);
            $this->SetTextColors(['0,0,0']);
            $this->SetHeights([0.6]);
            $this->SetAligns(['L']);
        }
    }

    function create()
    {
        try {
            $this->Output('I', 'Informe - EFOSDefinitivosVsCFD_'.date("d-m-Y h:i:s").'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }

}
