<?php


namespace App\PDF;


use App\Models\CADECO\EntradaMaterial;
use App\Models\CADECO\OrdenCompra;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\Obra;
use App\Facades\Context;

class EntradaAlmacenFormato extends Rotation
{
    protected $obra;
    protected $entrada;
    protected $numero_folio;
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
    {
//        dd($entrada);
        parent::__construct('P', 'cm', 'A4');
        $this->obra = Obra::find(Context::getIdObra());
//        dd($this->obra);

        $entrada_almacen=EntradaMaterial::query()->where('id_transaccion', $id)->with('ordenCompra', 'empresa', 'sucursal', 'partidas')->get()->toArray();
        $this->numero_folio = '#'.str_pad($entrada_almacen[0]['numero_folio'],5,0, STR_PAD_LEFT);
        $this->fecha = substr($entrada_almacen[0]['fecha'], 0, 10);
//        dd($entrada_almacen);


        $this->oc_folio = '#'.str_pad($entrada_almacen[0]['orden_compra']['numero_folio'],5,0,STR_PAD_LEFT);

        $this->empresa = $entrada_almacen[0]['empresa']['razon_social'];
        $this->empresa_rfc = $entrada_almacen[0]['empresa']['rfc'];
        $this->empresa_direccion = $entrada_almacen[0]['sucursal']['direccion'];

        $this->partidas = $entrada_almacen[0]['partidas'];
//        dd($this->obra);
    }
    public function Header()
    {
        $residuo = $this->PageNo() % 2;
        $postTitle = .7;

        $this->Cell(11.5);
        $x_f = $this->GetX();
        $y_f = $this->GetY();

        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(4.5, .7, utf8_decode('FOLIO'), 'LT', 0, 'L');
        $this->Cell(3.5, .7, $this->numero_folio, 'RT', 0, 'L');
        $this->Ln(.7);
        $y_f = $this->GetY();

        $this->SetFont('Arial', 'B', 24);
        $this->Cell(11.5, $postTitle, utf8_decode('ENTRADA DE ALMACÉN'), 0, 0, 'C', 0);
        $this->Ln();

        $this->SetY($y_f);
        $this->SetX($x_f);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(4.5, .7, 'FECHA ', 'L', 0, 'L');
        $this->Cell(3.5, .7, date("d-m-Y", strtotime($this->fecha))  . ' ', 'R', 0, 'L');
        $this->Ln(.7);

        $this->Cell(11.5);
        $this->Cell(4.5, .7, 'ORDEN DE COMPRA', 'LB', 0, 'L');
        $this->Cell(3.5, .7, $this->oc_folio, 'RB', 1, 'L');
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


        $this->Row([utf8_decode($this->obra->nombre . '  ' . " ")]);
        $this->Ln(.5);
        $this->SetFont('Arial', '', 10);
        $this->Cell(9.5, .7, 'Proveedor/Sucursal', 0, 0, 'L');
        $this->Cell(.5);
        $this->Cell(9.5, .7, 'Empresa', 0, 0, 'L');
        $this->Ln(.8);
        $y_inicial = $this->getY();
        $x_inicial = $this->getX();
        $this->MultiCell(9.5, .5,
            "empresa" . '
' . "sucrus" . '
' . "dsad", '', 'L');


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
        $this->MultiCell(9.5, .5,
            $this->empresa .'
' . utf8_decode(strtoupper($this->empresa_direccion)) . '
' . $this->empresa_rfc, '', 'L');

        $this->setY($y_inicial);
        $this->setX($x_inicial + 10);
        $this->Row([""]);

        $this->setY($y_inicial);
        $this->setX($x_inicial + 10);
        $this->MultiCell(9.5, .5,
            utf8_decode($this->obra->facturar) . '
' . utf8_decode($this->obra->direccion) . '
' . 'Estado: '.utf8_decode($this->obra->estado) . ' C.P:'.$this->obra->codigo_postal.' 
' . $this->obra->rfc, '', 'L');

        $this->setY($y_alto);
        $this->Ln(.5);

        $this->SetFont('Arial', '', 6);
        $this->SetHeights([0.8]);
    }



    public function Footer()
    {

        $this->SetY(-0.8);
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(10, .3, (''), 0, 1, 'L');

        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(10, .3, utf8_decode('Formato generado desde el módulo de ordenes de compra. Fecha de registro: '.date("Y-m-d", strtotime($this->fecha))  . ' ') , 0, 0, 'L');
        $this->Cell(9.5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create()
    {
        $this->SetMargins(1, .5, 2);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 4);
        try {
            $this->Output('I', 'Formato - Entrada de Almacen.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }


}
