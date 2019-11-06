<?php


namespace App\PDF;


use App\Facades\Context;
use App\Models\CADECO\Compras\Asignacion;
use App\Models\CADECO\Obra;
use Ghidev\Fpdf\Rotation;

class AsignacionFormato extends Rotation
{
    protected $obra;

    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     * AsignacionFormato constructor
     * @param $asignacion
     */

    public function __construct($id)
    {
        parent::__construct('P', 'cm', 'A4');
        $this->obra = Obra::find(Context::getIdObra());
        $this->asignacion = Asignacion::query()->find($id);

//        var_dump('asignacion Formato',$id,$this->asignacion['observaciones'],auth()->user()->usuario,auth()->id(),$this->obra['id_obra']);
    }

    public function Header()
    {
        $postTitle = .7;

        $this->Cell(11.5);
        $x_f = $this->GetX();
        $y_f = $this->GetY();

        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'B', 14);
        $this->Ln(.7);
        $y_f = $this->GetY();


        $this->Ln();

        $this->SetY($y_f);
        $this->SetX($x_f);
        $this->SetFont('Arial', 'B', 10);

        $this->Ln(.7);

        $this->Cell(11.5);
        $this->Cell(4.5, .7, 'FECHA', 'LB', 0, 'L');

        $this->SetFont('Arial', 'B', 13);
        $this->SetWidths([19.5]);
        $this->SetRounds(['1234']);
        $this->SetRadius([0.2]);
        $this->SetFills(['255,255,255']);
        $this->SetTextColors(['0,0,0']);
        $this->SetHeights([0.7]);
        $this->SetRounds(['1234']);
        $this->SetRadius([0.2]);
        $this->SetAligns("C");






        $this->SetFont('Arial', '', 6);
        $this->SetHeights([0.8]);
    }

    public function Footer()
    {
        $this->SetY(-3.5);
        $this->SetX(14.7);
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);


        $this->CellFitScale(4.89, .4, utf8_decode('Recibi'), 'TRLB', 0, 'C', 1);
        $this->Ln();

        $this->SetX(14.7);
        $this->CellFitScale(4.89, 1.2, '', 'TRLB', 0, 'C');
        $this->Ln();
        $this->SetX(14.7);
        $this->CellFitScale(4.89, .4, '', 'TRLB', 0, 'C', 1);


        $this->SetY(-0.8);
        $this->SetX(14.7);
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(10, .3, (''), 0, 1, 'L');

        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(9.5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create()
    {
        $this->SetMargins(1, .5, 2);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 4);
//        var_dump('Listo');

        try {
            $this->Output('I', 'Formato - Salida de Almacen.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }
}
