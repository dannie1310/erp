<?php

namespace App\PDF\Fiscal;

use Ghidev\Fpdf\Rotation;

class InformeREPProveedorEmpresa extends Rotation
{
    protected $informe;
    protected $etiqueta_subtitulo;
    protected $es_del_grupo;
    protected $omitir_encabezado_tabla;

    const DPI = 96;
    const MAX_WIDTH = 180;
    const MAX_HEIGHT = 150;
    const MM_IN_INCH = 25.4;

    private $en_cola = '';

    public function __construct($informe)
    {
        parent::__construct("P", "cm", "Letter");
        $this->informe = $informe;
        $this->omitir_encabezado_tabla = true;
    }

    function logo()
    {
        list($width, $height) = $this->resizeToFit(public_path('/img/logo_hc.png'));
        $this->Image(public_path('/img/logo_hc.png'), 1, 0.5, $width - 2, $height - 1);
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
        $this->setXY(4.59, 1.2);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Helvetica', 'B', 12);
        $this->MultiCell(16, .5, utf8_decode('Informe de REP pendientes'), 0, 'C', 0);
        $this->setX(4.59);
        $this->SetFont('Helvetica', '', 9);
        $this->MultiCell(16, .3, utf8_decode('Desglosado por empresa'), 0, 'C', 0);
        $this->setXY(4.59, 2.0);

        $this->partidasTitle();

        if($this->en_cola != "subtitulo"){
            $this->subtitulo();
        }else{
            $this->omitir_encabezado_tabla = true;
        }

        if ($this->en_cola != '') {
            $this->setEstilos($this->en_cola);
        }
    }

    function Footer()
    {
        $this->SetY($this->GetPageHeight() - 1);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', '', 6);
        $this->Cell(6.5, .4, utf8_decode('Fecha de Consulta ') . date("d/m/Y H:i:s"), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 6);
        $this->Cell(6.5, .4, '', 0, 0, 'C');
        $this->Cell(6.5, .4, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }


    public function partidas()
    {
        foreach ($this->informe["informe"] as $tipo) {
            foreach ($tipo as $partida) {

                if (key_exists("tipo", $partida)) {
                    $this->en_cola = $partida["tipo"];
                    $this->setEstilos($partida["tipo"]);
                    if ($partida["tipo"] == "partida") {
                        $this->Row([
                            $partida["indice"]
                            , utf8_decode($partida["rfc_empresa"])
                            , utf8_decode($partida["empresa"])
                            , $partida["cantidad_cfdi_f"]
                            , $partida["total_cfdi_f"]
                            , $partida["total_rep_f"]
                            , $partida["pendiente_rep_f"]
                            , $partida["acumulado_pendiente_rep_f"]
                            , $partida["porcentaje"]
                            ]
                        );
                    }
                    else if ($partida["tipo"] == "subtitulo") {
                        $this->etiqueta_subtitulo = $partida["etiqueta"];
                        $this->es_del_grupo = $partida["es_empresa_hermes"];
                        if($this->omitir_encabezado_tabla){
                            $this->subtitulo();
                            $this->omitir_encabezado_tabla = false;
                        } else {
                            if($this->es_del_grupo == 1){
                                $this->SetTextColors(["194,8,8"]);
                            }
                            $this->Row([utf8_decode($partida["etiqueta"])]);
                        }
                    }

                    else if ($partida["tipo"] == "total") {
                        $this->Row([
                            $partida["contador"]
                            , utf8_decode($partida["etiqueta"])
                            , $partida["cantidad_cfdi_f"]
                            , $partida["total_cfdi_f"]
                            , $partida["total_rep_f"]
                            , $partida["pendiente_rep_f"]
                            , ''
                            , ''
                        ]);
                    } else {
                        $this->Row([
                            $partida["contador"]
                            , utf8_decode($partida["etiqueta"])
                            , $partida["cantidad_cfdi_f"]
                            , $partida["total_cfdi_f"]
                            , $partida["total_rep_f"]
                            , $partida["pendiente_rep_f"]
                            , ''
                            , ''
                        ]);

                        $this->Ln();
                    }
                }
            }
        }
        $this->Ln();
    }

    private function subtitulo()
    {
        if(!is_null($this->etiqueta_subtitulo))
        {
            $this->setEstilos("subtitulo");
            if($this->es_del_grupo == 1){
                $this->SetTextColors(["194,8,8"]);
            }
            $this->Row([utf8_decode($this->etiqueta_subtitulo)]);
        }
    }

    public function partidasTitle()
    {
        $this->setXY(1, 2.6);

        $this->SetFont('Arial', '', 6);

        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([0.8, 2, 7, 0.9, 2.1, 2.1, 2.1, 2.1, 0.6]);
        $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF']);
        $this->SetRounds(['', '', '', '', '', '', '', '', '']);
        $this->SetRadius([0.2, 0, 0, 0, 0, 0, 0, 0, 0.2]);

        $this->SetFills(['117,117,117', '117,117,117', '117,117,117', '117,117,117', '117,117,117', '117,117,117', '117,117,117', '117,117,117', '117,117,117']);
        $this->SetTextColors(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
        $this->SetDrawColor(117, 117, 117);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C']);
        $this->Row(["#", "RFC", utf8_decode("Razón Social"), "# CFDI", "Monto CFDI", "Monto REP", "Pendiente REP", "Pendiete REP (Acumulado)", "%"]);

    }

    private function setEstilos($tipo)
    {
        if ($tipo == "partida") {
            $this->SetDrawColor(213, 213, 213);
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(255, 255, 255);
            $this->SetWidths([0.8, 2, 7, 0.9, 2.1, 2.1, 2.1, 2.1, 0.6]);
            $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF']);
            $this->SetRounds(['', '', '', '', '', '', '', '', '']);
            $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0]);
            $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            $this->SetHeights([0.4]);
            $this->SetAligns(['C', 'C', 'L', 'R', 'R', 'R', 'R', 'R', 'R']);
        } else if ($tipo == "subtotal") {
            $this->SetDrawColor(213, 213, 213);
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(255, 255, 255);
            $this->SetWidths([0.8, 9, 0.9, 2.1, 2.1, 2.1, 2.1, 0.6]);
            $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF']);
            $this->SetRounds(['', '', '', '', '', '', '', '']);
            $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0]);
            $this->SetFills(['213,213,213', '213,213,213', '213,213,213', '213,213,213', '213,213,213', '213,213,213', '213,213,213', '213,213,213']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            $this->SetHeights([0.4]);
            $this->SetAligns(['C', 'R', 'R', 'R', 'R', 'R','R', 'R']);
        } else if ($tipo == "total") {
            $this->SetDrawColor(117, 117, 117);
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(255, 255, 255);
            $this->SetWidths([0.8, 8.9, 1, 2.1, 2.1, 2.1, 2.1, 0.6]);
            $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF', 'DF']);
            $this->SetRounds(['', '', '', '', '', '', '', '']);
            $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0]);
            $this->SetFills(['117,117,117', '117,117,117', '117,117,117', '117,117,117', '117,117,117', '117,117,117', '117,117,117', '117,117,117']);
            $this->SetTextColors(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->SetHeights([0.4]);
            $this->SetAligns(['C', 'R', 'R', 'R', 'R', 'R','R', 'R']);
        } else if ($tipo == "titulo") {
            $this->SetDrawColor(255, 255, 255);
            $this->SetFont('Arial', 'B', 9);
            $this->SetFillColor(255, 255, 255);
            $this->SetWidths([19.7]);
            $this->SetStyles(['DF']);
            $this->SetRounds(['']);
            $this->SetRadius([0]);
            $this->SetFills(['255,255,255']);
            $this->SetTextColors(['0,0,0']);
            $this->SetHeights([0.6]);
            $this->SetAligns(['L']);
        } else if ($tipo == "subtitulo") {
            $this->SetDrawColor(255, 255, 255);
            $this->SetFont('Arial', 'B', 7);
            $this->SetFillColor(255, 255, 255);
            $this->SetWidths([19.7]);
            $this->SetStyles(['DF']);
            $this->SetRounds(['']);
            $this->SetRadius([0]);
            $this->SetFills(['255,255,255']);
            $this->SetTextColors(['0,0,0']);
            $this->SetHeights([0.4]);
            $this->SetAligns(['L']);
        }
    }

    function create()
    {
        $this->SetMargins(1, .5, 2);
        $this->SetAutoPageBreak(true, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->partidas();

        try {
            $this->Output('I', 'Informe - REP pendientes desglosado por empresas_' . date("d-m-Y h:i:s") . '.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }
}
