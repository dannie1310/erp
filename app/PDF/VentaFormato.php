<?php


namespace App\PDF;


use App\Facades\Context;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Venta;
use Illuminate\Support\Facades\App;

class VentaFormato extends Rotation
{
    protected $obra;
    protected $venta;
    private $dim_aux=0;
    var $encola = '';
    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     *
     */
    public function __construct(Venta $venta)
    {
        parent::__construct('P', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());
        $this->venta = $venta;
    }

    public function Header(){
        $postTitle = .7;

        $this->Cell(11.5);
        $x_f = $this->GetX();
        $y_f = $this->GetY();

        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(4.5, .7, utf8_decode('FOLIO'), 'LT', 0, 'L');
        $this->Cell(3.5, .7, ''.$this->venta->numero_folio_format.'', 'RT', 0, 'L');
        $this->Ln(.7);
        $y_f = $this->GetY();

        $this->SetFont('Arial', 'B', 24);
        $this->Cell(11.5, $postTitle, utf8_decode( 'Venta'), 0, 0, 'C', 0);

        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->SetY($y_f);

        // $this->SetX($x_f);
        // $this->Cell(4.5, .7, 'FOLIO ALM ', 'L', 0, 'L');
        // $this->Cell(3.5, .7, 'Panda', 'R', 0, 'L');
        // $this->Ln(.7);

        $this->SetX($x_f);
        $this->Cell(4.5, .7, 'FOLIO CLIENTE', 'L', 0, 'L');
        $this->Cell(3.5, .7, $this->venta->numero_folio_alt_format, 'R', 0, 'L');
        $this->Ln(.7);

        $this->SetX($x_f);
        $this->Cell(4.5, .7, 'FECHA', 'LB', 0, 'L');
        $this->Cell(3.5, .7, $this->venta->fecha_format, 'RB', 1, 'L');
        $this->Ln(.5);

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
        $this->Row([utf8_decode($this->obra->descripcion . '  ' . " ")]);
        $this->Ln(.2);

        $this->SetFont('Arial', '', 10);
        $this->Cell(9.5, .5, utf8_decode("Cliente"), 0, 0, 'L');
        $this->Cell(.5);
        $this->Cell(9.5, .5, 'Vendedor', 0, 0, 'L');
        $this->Ln(.5);
        $y_inicial = $this->getY();
        $x_inicial = $this->getX();
        $this->MultiCell(9.5, .5,'', '', 'L');


        $y_final_1 = $this->getY();
        $this->setY($y_inicial);
        $this->setX($x_inicial + 10);
        $this->MultiCell(9.5, .5,
                    utf8_decode("hora") . '
        ' . "dir factura" . '
        ' . "obra rfc", '', 'L');
        $y_final_2 = $this->getY();


        if ($y_final_1 > $y_final_2)
            $y_alto = $y_final_1;

        else
            $y_alto = $y_final_2;

        $alto = abs($y_inicial - $y_alto) + 1.5;
        $this->SetWidths([9.5]);
        $this->SetRounds(['1234']);
        $this->SetRadius([0.2]);
        $this->SetFills(['255,255,255']);
        $this->SetTextColors(['0,0,0']);
        $this->SetHeights([$alto]);
        $this->SetStyles(['DF']);
        $this->SetAligns("L");
        $this->SetFont('Arial', '', 10);
        $this->setY($y_inicial);
        $this->Row([""]);
        $this->setY($y_inicial);
        $this->setX($x_inicial);
        $this->MultiCell(9.5, .5, utf8_decode(strtoupper(utf8_decode($this->venta->empresa->razon_social) . '
' . 'R.F.C.: ' . $this->venta->empresa->rfc)), '', '');
// dd($this->venta->empresa->razon_social);
        $this->setY($y_inicial);
        $this->setX($x_inicial + 10);
        $this->Row([""]);

        $this->setY($y_inicial);
        $this->setX($x_inicial + 10);
        // $this->MultiCell(9.5, .5,' Dirección Cliente', '', 'L');
        $this->MultiCell(9.5, .5,
                    utf8_decode($this->venta->obra->cliente) . '
        ' . preg_replace( "/\r|\n/", " ", utf8_decode($this->obra->direccion)) . '
        ' . $this->obra->rfc, '', 'L');

        $this->setY($y_alto);
        $this->Ln(.5);

        $this->SetFont('Arial', '', 6);
        $this->SetHeights([0.8]);

        $this->almacen();
    }

    public function partidas()
    {
        if($this->PageNo()==1){
            $this->tableHeader();
        }

        foreach ($this->venta->partidas_total as $i => $partida) {
            if($this->GetY() > 23.5){
                $this->AddPage();
                $this->tableHeader();
            }
            $this->dim = $this->GetY();
            $this->SetWidths([1,2.5,8,2,2,2,2]);
            $this->SetRounds([]);
            $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->SetAligns(['C', 'L', 'L', 'C', 'R', 'R', 'R']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);

            $this->Row([
                $i + 1,
                $partida->material->numero_parte,
                utf8_decode($partida->material->descripcion),
                $partida->material->unidad,
                number_format($partida->total, 2, '.', ','),
                $partida->precio_unitario_format,
                $partida->importe_format,
            ], '');
        }

        if($this->GetY() > 23.5){
            $this->AddPage();
        }

        $this->SetX(16.5);
        $this->Cell(2, .5, 'Subtotal', 0, 0, 'L');
        $this->Cell(2, .5, $this->venta->subtotal_format, 1, 0, 'R');
        $this->Ln(.5);
        $this->SetX(16.5);
        $this->Cell(2, .5, 'IVA (16%)', 0, 0, 'L');
        $this->Cell(2, .5, $this->venta->impuesto_format, 1, 0, 'R');
        $this->Ln(.5);
        $this->SetX(16.5);
        $this->Cell(2, .5, 'Total', 0, 0, 'L');
        $this->Cell(2, .5, $this->venta->monto_format, 1, 0, 'R');

        $this->observaciones();
    }

    public function tableHeader()
    {

        $this->Ln(.5);
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180,180,180,180,180,180,180);
        $this->SetWidths([1,2.5,8,2,2,2,2]);
        $this->SetStyles(['DF','DF','DF','DF','DF','DF','DF']);
        $this->SetRounds(['1','','','','','','2']);
        $this->SetRadius([0.2,0,0,0,0,0,0.2]);
        $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
        $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C','C','C','C','C','C','C']);
        $this->Row(["#","No. Parte",utf8_decode("Descripción"), "Unidad", "Cantidad", "Precio de Venta", "Importe"]);
    }

    public function observaciones(){
        if($this->GetY() > 22.6) {
            $this->AddPage();
            $this->Ln(1.8);
        }

        $this->Ln(.7);
        $this->SetWidths([19.5]);
        $this->SetRounds(['12']);
        $this->SetRadius([0.2]);
        $this->SetFills(['180,180,180']);
        $this->SetTextColors(['0,0,0']);
        $this->SetHeights([0.5]);
        $this->SetFont('Arial', '', 9);
        $this->SetAligns(['C']);
        $this->encola="observaciones_encabezado";
        $this->Row(["Observaciones"]);
        $this->SetRounds(['34']);
        $this->SetRadius([0.2]);
        $this->SetAligns(['J']);
        $this->SetStyles(['DF']);
        $this->SetFills(['255,255,255']);
        $this->SetTextColors(['0,0,0']);
        $this->SetHeights([0.5]);
        $this->SetFont('Arial', '', 9);
        $this->SetWidths([19.5]);
        $this->encola="observaciones";

        $this->Row([utf8_decode($this->venta['observaciones'])]);

    }

    public function almacen(){
        $this->Ln(1.5);
        $this->SetWidths([19.5]);
        $this->SetRounds(['']);
        $this->SetRadius([0.2]);
        $this->SetAligns(['J']);
        $this->SetStyles(['']);
        $this->SetFills(['255,255,255']);
        $this->SetTextColors(['0,0,0']);
        $this->SetHeights([0.5]);
        $this->SetWidths([19.5]);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(1.6,0,utf8_decode('Almacén : '), 0,0 , 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0,0,utf8_decode($this->venta->almacen->descripcion), 0,0 , 'L');

    }

    public function Footer()
    {
        if (!App::environment('production')) {
            $this->SetFont('Arial','B',80);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(5,20,utf8_decode("MUESTRA"),45);
            $this->RotatedText(6,26,utf8_decode("SIN VALOR"),45);
            $this->SetTextColor('0,0,0');
        }
        //Capturó
        $this->SetY(-2.5);
        $this->SetX(4);
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);


        $this->CellFitScale(4.89, .4, utf8_decode('Capturó'), 'TRLB', 0, 'C', 1);
        $this->Ln();

        $this->SetX(4);
        $this->CellFitScale(4.89, 1, '', 'TRL', 0, 'C');
        $this->Ln();
        $this->SetX(4);
        $this->CellFitScale(4.89, .4, "Nombre         Fecha         Firma", 'RLB', 0, 'C');

        if($this->venta->estado == -1){
            $this->SetFont('Arial', 'B', 95);
            $this->SetTextColor(215,215,215);
            $this->RotatedText(4,25.5,'C A N C E L A D A',55);
        }
        //Revisó
        $this->SetY(-2.5);
        $this->SetX(12);
        $this->SetFont('Arial', '', 6);
        $this->SetTextColor(0,0,0);
        $this->SetFillColor(180, 180, 180);


        $this->CellFitScale(4.89, .4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
        $this->Ln();

        $this->SetX(12);
        $this->CellFitScale(4.89, 1, '', 'TRL', 0, 'C');
        $this->Ln();
        $this->SetX(12);
        $this->CellFitScale(4.89, .4, "Nombre         Fecha         Firma", 'RLB', 0, 'C');


        //PAGINA Y LEYENDA
        $this->SetY(-0.8);
        $this->SetX(14.7);
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(10, .3, (''), 0, 1, 'L');

        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(10, .3, utf8_decode('Formato generado desde el sistema de ventas. Fecha de registro: '  .$this->venta->fecha_hora_registro_format . ' ') , 0, 0, 'L');
        $this->Cell(9.5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create()
    {
        $this->SetMargins(1, .5, 2);
        $this->SetAutoPageBreak(true, 2);
        $this->AliasNbPages();
        $this->AddPage();
        $this->partidas();

        try {
            $this->Output('I', 'Formato - Venta '.$this->venta->numero_folio_format.'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }

    function RotatedText($x, $y, $txt, $angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }

}
