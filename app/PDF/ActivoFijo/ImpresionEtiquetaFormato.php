<?php

namespace App\PDF\ActivoFijo;

use Ghidev\Fpdf\Rotation;

class ImpresionEtiquetaFormato extends Rotation
{
    protected $etiquetas;
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

    public function __construct(Array $etiquetas)
    {
        parent::__construct('P', 'cm', 'Letter');

        $this->etiquetas = $etiquetas;
        $this->WidthTotal = $this->GetPageWidth();
    }

    function Header()
    {
        $this->SetX(0.7);
        $this->SetY(1.25);
        $this->x_c = $this->GetX();//0.7
        $this->y_c = $this->GetY();//1.1
        $this->x_p = $this->GetX();//0.7
        $this->SetXY($this->x_c, $this->y_c);
    }

    function logo($id_empresa, $x, $y)
    {
        $file = $this->archivo_logo($id_empresa);
        $data = unpack("H*", file_get_contents($file));
        $data = bin2hex($data[1]);
        $data = pack('H*', hex2bin($data));
        $file = public_path('/img/logo_'.$id_empresa.'.png');
        if (file_put_contents($file, $data) !== false) {
            $this->Image($file, $x+3.2, $y+0.5, 0.3, 0.3);
            if(file_exists(public_path('/img/logo_'.$id_empresa.'.png'))) {
                unlink($file);
            }
        }
    }

    function archivo_logo($id_empresa)
    {
        if($id_empresa == 1) //La nacional
        {
            return public_path('img\logo-empresa\GLN.png');
        }
        else if($id_empresa == 2)//LA PENINSULAR
        {
            return public_path('img\logo-empresa\LA_PENINSULAR.png');
        }
        else if($id_empresa == 3 || $id_empresa == 4) //CODRAMSA y OATSA
        {
            return public_path('img\logo-empresa\CODRAMSA.png');
        }
        else if($id_empresa == 6)
        {
            return public_path('img\logo-empresa\ELA.png');
        }
        else
        {
           return public_path('img\logo-empresa\PBC.png');
        }
    }

    function caracteristicas(){

        $cantP = count($this->etiquetas);
        $x= 4;

        for($i = 0; $i < $cantP; $i++)
        {
            for ($z=1; $z<=$this->etiquetas[$i]['CodigosImprimir']; $z++)
            {
                $this->x_c = $this->GetX();//0.7
                $this->y_c = $this->GetY();//1.1
                $this->x_p = $this->GetX();//0.7
                $x--;
                $this->SetXY($this->x_c, $this->y_c);

                $this->SetFont('code39', '', 6);
                $this->CellFitScale(4.4, 1.3, '*' . $this->etiquetas[$i]['Codigo'] . '*', 0, 0, 'C');

                $this->SetFont('Arial', '', 4);
                $this->SetXY($this->x_c, $this->y_c+0.6);
                $this->Cell(1, 0.5, 'ACTIVO FIJO', 0, 0, 'C');
                $this->logo($this->etiquetas[$i]['idEmpresa'], $this->x_c+0.2, $this->y_c+0.1);

                $this->SetFont('Arial', '', 4);
                $this->SetXY($this->x_c, $this->y_c+0.9);
                $this->CellFitScale(4.4, 0.2, utf8_decode($this->etiquetas[$i]['Familia']), 0, 0, 'C');

                $this->SetXY($this->x_c, $this->y_c+1.05);
                $this->Cell(4.4, 0.15, $this->etiquetas[$i]['NumeroSerie'], 0, 0, 'C');

                $this->x_c = $this->x_c+0.6;
                $this->SetXY(($this->x_c + 4.4),$this->y_c);

                if ($x == 0) {
                    $this->Ln(1.28);
                    $x = 4;
                }
            }
        }
    }

    function create()
    {
        $this->AddFont('code39', '', 'code39.php');
        $this->SetMargins(0.7, 1.25, 0.7);
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
}

