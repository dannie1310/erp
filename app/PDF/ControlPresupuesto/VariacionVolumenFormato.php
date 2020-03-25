<?php


namespace App\PDF\ControlPresupuesto;


use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\Obra;
use App\Facades\Context;
use Illuminate\Support\Facades\App;

class VariacionVolumenFormato extends Rotation
{
    protected $obra;
    protected $entrada_almacen;
    private $dim_aux=0;
    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     *EntradaAlmacenFormato constructor.
     * @param $entrada
     */

    public function __construct($id)
    {}

    public function Header()
    {

           $postTitle = .7;

           $this->Cell(11.5);
           $x_f = $this->GetX();
           $y_f = $this->GetY();

           $this->SetTextColor('0,0,0');
           $this->SetFont('Arial', 'B', 14);
           $this->Cell(4.5, .7, utf8_decode('FOLIO'), 'LT', 0, 'L');
           $this->Cell(3.5, .7, 'panda', 'RT', 0, 'L');
    }

    function create()
    {
        $this->SetMargins(1, .5, 2);
        $this->AliasNbPages();
        // $this->AddPage();
        $this->SetAutoPageBreak(true, 4);
        // $this->partidas();

        try {
            $this->Output('I', 'Formato - Variación de Volumen.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }
}