<?php


namespace App\PDF\Almacenes;



use App\Models\CADECO\EntradaMaterial;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\Obra;
use App\Facades\Context;
use Illuminate\Support\Facades\App;

class EntradaAlmacenFormato extends Rotation
{
    protected $obra;
    var $encola = '';
    private $dim_aux = 0;
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
        parent::__construct('P', 'cm', 'A4');
        $this->obra = Obra::find(Context::getIdObra());

        $this->entrada = EntradaMaterial::query()->find($id);
        $this->numero_folio = $this->entrada->numero_folio_format;
        $this->fecha = substr($this->entrada->fecha_format, 0, 10);
        $this->fecha_registro = $this->entrada->fecha_hora_registro_format;

        $this->oc_folio = $this->entrada->ordenCompra->numero_folio_format;

        $this->empresa = $this->entrada->empresa['razon_social'];
        $this->empresa_rfc = $this->entrada->empresa['rfc'];
        $this->empresa_direccion = $this->entrada->sucursal['direccion'];
    }

    public function Header()
    {
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
        $this->Cell(3.5, .7, $this->fecha, 'R', 0, 'L');
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
            utf8_decode($this->empresa) . '
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
' . 'Estado: ' . utf8_decode($this->obra->estado) . ' C.P:' . $this->obra->codigo_postal . '
' . $this->obra->rfc, '', 'L');

        $this->setY($y_alto);
        $this->Ln(.5);

        $this->SetFont('Arial', '', 6);
        $this->SetHeights([0.8]);

        if ($this->encola == 'partida') {
            $this->Ln(1.8);
            $this->SetFont('Arial', '', 6);
            $this->SetFillColor(180, 180, 180);
            $this->SetWidths([1, 2.5, 10, 2, 2, 2]);
            $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF']);
            $this->SetRounds(['1', '', '', '', '', '2']);
            $this->SetRadius([0.2, 0, 0, 0, 0, 0.2]);
            $this->SetFills(['180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            $this->SetHeights([0.4]);
            $this->SetAligns(['C', 'C', 'C', 'C', 'C', 'C',]);
            $this->Row(["#", "No. Parte", utf8_decode("Descripción"), "Unidad", "Cantidad", "Fecha de Entrega Requerida"]);

            $this->SetWidths([1, 2.5, 10, 2, 2, 2]);
            $this->SetRounds(['', '', '', '', '', '']);
            $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->SetAligns(['L', 'L', 'L', 'L', 'R', 'C']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        }
    }


    public function partidas()
    {
        $this->Ln(1.8);
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([1, 2.5, 10, 2, 2, 2]);
        $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '2']);
        $this->SetRadius([0.2, 0, 0, 0, 0, 0.2]);
        $this->SetFills(['180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C', 'C', 'C', 'C', 'C', 'C',]);
        $this->Row(["#", "No. Parte", utf8_decode("Descripción"), "Unidad", "Cantidad", "Fecha de Entrega Requerida"]);


        foreach ($this->entrada->partidas as $i => $p) {
            $this->encola = 'partida';

            $this->SetWidths([1, 2.5, 10, 2, 2, 2]);
            $this->SetRounds(['', '', '', '', '', '']);
            $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->SetAligns(['L', 'L', 'L', 'L', 'R', 'C']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);

            $this->Row([
                $i + 1,
                $p->material['numero_parte'],
                utf8_decode($p->material['descripcion']),
                $p['unidad'],
                $p->cantidad_format,
                $p->itemSolicitud->entrega->fecha_format
            ]);


            if (empty($p->almacen['descripcion'])) {
                $nivel = $p->concepto['nivel'];

                /*Observaciones*/
                $this->SetRounds(['4', '', '', '', '', '', '', '', '3']);
                $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0]);
                $this->SetWidths([19.5]);
                $this->SetAligns(['L']);


                $this->Row([utf8_decode($p->concepto->getAncestrosAttribute($nivel))]);

            } else {


                /*Observaciones*/
                $this->SetRounds(['4', '', '', '', '', '', '', '', '3']);
                $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0]);
                $this->SetWidths([19.5]);
                $this->SetAligns(['L']);
                $this->Row([utf8_decode($p->almacen['descripcion'])]);


            }

            /*Guiones*/
            $this->SetRounds(['4', '', '', '', '', '', '', '', '3']);
            $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0]);
            $this->SetWidths([19.5]);
            $this->SetAligns(['L']);
            $this->Row([utf8_decode("---")]);
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
        $this->encola = "observaciones_encabezado";
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
        $this->encola = "observaciones";

        $this->Row([utf8_decode($this->entrada['observaciones'])]);


    }


    public function Footer()
    {
        if (!App::environment('production')) {
            $this->SetFont('Arial', 'B', 80);
            $this->SetTextColor(155, 155, 155);
            $this->RotatedText(5, 20, utf8_decode("MUESTRA"), 45);
            $this->RotatedText(6, 26, utf8_decode("SIN VALOR"), 45);
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

        //Revisó
        $this->SetY(-2.5);
        $this->SetX(12);
        $this->SetFont('Arial', '', 6);
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
        $this->Cell(10, .3, utf8_decode('Formato generado desde el módulo de ordenes de compra. Fecha y hora de registro: '.$this->fecha_registro  . ' ') , 0, 0, 'L');
        $this->Cell(9.5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create()
    {
        $this->SetMargins(1, .5, 2);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 4);
        $this->partidas();

        try {
            $this->Output('I', 'Formato - Entrada de Almacen.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }
}
