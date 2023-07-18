<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/11/2019
 * Time: 05:11 PM
 */

namespace App\PDF\Compras;


use App\Models\CADECO\Requisicion;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;

class RequisicionFormato extends Rotation
{
    protected $id_requisicion;
    protected $requisicion;

    private $encola = '',
        $encabezado_pdf,
        $conFirmaDAF = false,
        $id_tipo_fianza = 0,
        $folio_sao,
        $sin_texto,
        $NuevoClausulado = 0,
        $dim=0,
        $dim_aux=0,
        $obs_item='',
        $id_antecedente;

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;


    public function __construct($id_requisicion)
    {
        parent::__construct('P', 'cm', 'A4');

        $this->requisicion = Requisicion::find($id_requisicion);
    }

    public function Header()
    {
        $residuo = $this->PageNo() % 2;

        if (true) {
            $postTitle = .7;


            $this->Cell(11.5);
            $x_f = $this->GetX();
            $y_f = $this->GetY();


            $this->SetTextColor('0,0,0');
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(4.5, .7, utf8_decode('NÚMERO '), 'LT', 0, 'L');
            $this->Cell(3.5, .7, $this->requisicion->numero_folio_format, 'RT', 0, 'L');
            $this->Ln(.7);
            $y_f = $this->GetY();

            $this->SetFont('Arial', 'B', 24);
            $this->Cell(11.5, $postTitle, utf8_decode('REQUISICIÓN '), 0, 0, 'C', 0);
            $this->Ln();

            $this->SetY($y_f);
            $this->SetX($x_f);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(4.5, .7, 'FOLIO ', 'L', 0, 'L');
            $this->Cell(3.5, .7, $this->requisicion->complemento ? $this->requisicion->complemento->folio_compuesto : '', 'R', 0, 'L');
            $this->Ln(.7);

            $this->Cell(11.5);
            $this->Cell(4.5, .7, 'FECHA ', 'LB', 0, 'L');
            $this->Cell(3.5, .7, date("d/m/Y", strtotime($this->requisicion->fecha)) , 'RB', 1, 'L');
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

            $this->Row([$this->requisicion->obra->descripcion != null ? utf8_decode($this->requisicion->obra->descripcion . '  ' . " ") : utf8_decode($this->requisicion->obra->nombre . '  ' . " ")]);
            $this->Ln(.5);

            $this->Ln(.5);
            $this->SetFont('Arial', '', 10);
            $this->Cell(9.5, .7, '', 0, 0, 'L');
            $this->Cell(.5);
            $this->Cell(9.5, .7, 'EMPRESA', 0, 0, 'L');
            $this->Ln(.8);
            $y_inicial = $this->getY();
            $x_inicial = $this->getX();

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

            $alto = abs($y_inicial - $y_alto) + 1;
            $this->SetWidths([9.5]);
            $this->SetRounds(['1234']);
            $this->SetRadius([0.2]);
            $this->SetFills(['255,255,255']);
            $this->SetTextColors(['0,0,0']);
            $this->SetHeights([$alto]);
            $this->SetStyles(['DF']);
            $this->SetAligns("L");
            $this->setY($y_inicial);
            $this->setX($x_inicial + 10);
            $this->Row([""]);
            $this->setY($y_inicial);
            $this->setX($x_inicial + 10);
            $this->MultiCell(9.5, .5,
                utf8_decode($this->requisicion->obra->cliente)."\n". $this->requisicion->obra->direccion."\n". $this->requisicion->obra->rfc, '', 'L');
            $this->setY($y_alto);
            $this->Ln(.5);

            $this->Ln(.5);
            $this->Ln(.5);
            $this->SetFont('Arial', '', 6);
            $this->SetHeights([0.8]);


            $this->SetWidths(array(19.5));
            $this->SetRounds(array('12'));
            $this->SetRadius(array(0.2));
            $this->SetFills(array('180,180,180'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetStyles(array('DF'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 8);
            $this->SetAligns(array('C'));
            $this->Row(array("Concepto"));
            $this->SetRounds(array('34'));
            $this->SetRadius(array(0.2));
            $this->SetAligns(array('C'));
            $this->SetStyles(array('DF'));
            $this->SetFills(array('255,255,255'));
            $this->SetTextColors(array('0,0,0'));
            $this->SetHeights(array(0.5));
            $this->SetFont('Arial', '', 6);
            $this->Row(array(utf8_decode(str_replace(array("\r", "\n"), '', "".$this->requisicion->complemento ? $this->requisicion->complemento->concepto: ''))));
            $this->Ln(.8);

            // Cuadro partidas
            if ($this->encola == "partida") {
                $this->SetFillColor(180, 180, 180);
                $this->SetWidths([1, 4, 7, 2, 2.5, 3]);
                $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF']);
                $this->SetRounds(['1', '', '', '', '', '2']);
                $this->SetRadius([0.2, 0, 0, 0, 0, 0.2]);
                $this->SetFills(['180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
                $this->SetHeights([0.4]);
                $this->SetAligns(['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C']);
                $this->Row(["#", "No. Parte", utf8_decode("Descripción"), "Cantidad", "Unidad", "Fecha Requerida"]);
                $this->SetRounds(['', '', '', '', '', '', '', '', '']);
                $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
                $this->SetAligns(['C', 'R', 'C', 'L', 'L', 'R']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            } else if ($this->encola == "observaciones_partida") {
                $this->SetRadius([0]);
                $this->SetTextColors(['150,150,150']);
                $this->SetWidths([19.5]);
                $this->SetAligns(['J']);
            }
        }
    }

    public function partidas()
    {
        $this->encola = "partida";
        $count_partidas = count($this->requisicion->partidas);

        $this->SetFillColor(180, 180, 180);
        $this->SetWidths([1, 4, 7, 2, 2.5, 3]);
        $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF']);
        $this->SetRounds(['1', '', '', '', '', '2']);
        $this->SetRadius([0.2, 0, 0, 0, 0, 0.2]);
        $this->SetFills(['180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180']);
        $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C']);
        $this->Row(["#", "No. Parte", utf8_decode("Descripción"), "Cantidad", "Unidad", "Fecha Requerida"]);


        foreach ($this->requisicion->partidas as $i => $p) {
            $this->dim = $this->GetY();
            if ($this->dim > 23.0) {
                $this->AddPage();
                $this->dim_aux = 1;
            }
            $this->SetWidths([1, 4, 7, 2, 2.5, 3]);

            $this->SetRounds(['', '', '', '', '', '']);
            $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
            $this->SetAligns(['C', 'L', 'L', 'R', 'L', 'L']);
            $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);

            $this->Row([$i+1,
                $p->id_material ? $p->material->numero_parte : $p->complemento->numero_parte,
                utf8_decode($p->id_material ? $p->material->descripcion : $p->complemento->descripcion_material),
                number_format($p->cantidad, 2, '.', ','),
                $p->id_material ? $p->material->unidad : $p->complemento->unidad,
                date("d/m/Y", strtotime($p->complemento->fecha_entrega))
            ]);

            $this->SetWidths([19.5]);
            $this->SetAligns(['L']);


            if (!empty($p->complemento->observaciones)) {
                $this->SetTextColors(['150,150,150']);
                $this->SetWidths([19.5]);
                $this->SetAligns(['J']);
                if($count_partidas == ($i+1))
                {
                    $this->SetRounds(['4']);
                    $this->SetRadius([0.2]);
                }

//                $this->encola = "observaciones_partida";
                $this->Row([html_entity_decode(mb_convert_encoding($p->complemento->observaciones, 'HTML-ENTITIES', 'UTF-8'))]);
            }
            if(!empty($p->concepto))
                {
                    $nivel=$p->concepto['nivel'];

                    /*Concepto*/
                    $this->SetTextColors(['0,0,0']);
                    $this->SetRounds(['4','','','','','','','','3']);
                    $this->SetRadius([0,0,0,0,0,0,0,0,0]);
                    $this->SetWidths([19.5]);
                    $this->SetAligns(['L']);
                    $this->Row([utf8_decode($p->concepto->getAncestrosAttribute($nivel))]);
                 }
//            $this->dim = $this->GetY();


        }

        $this->Ln(.7);
        $this->SetWidths([19.5]);
        $this->SetRounds(['12']);
        $this->SetRadius([0.2]);
        $this->SetFills(['180,180,180']);
        $this->SetTextColors(['0,0,0']);
        $this->SetHeights([0.5]);
        $this->SetFont('Arial', '', 8);
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
        $this->SetFont('Arial', '', 6);
        $this->SetWidths([19.5]);
        $this->encola="observaciones";

        $this->Row([utf8_decode($this->requisicion->observaciones)]);
    }

    public function Footer()
    {
        if (!App::environment('production')) {
            $this->SetFont('Arial','B',80);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(5,15,utf8_decode("MUESTRA"),45);
            $this->RotatedText(6,21,utf8_decode("SIN VALOR"),45);
            $this->SetTextColor('0,0,0');
        }
        $this->SetTextColor('0,0,0');

        // Firmas.

        $this->SetY(-4.5);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180, 180, 180);


        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4, utf8_decode('Realizó'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);



        $this->SetY(-4.11);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 1.2, '', 'TRLB', 0, 'C');
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 1.2, '', 'TRLB', 0, 'C');
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 1.2, '', 'TRLB', 0, 'C');



        $this->SetY(-3.0);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4,  "", 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4,  "", 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);
        $this->Cell(($this->GetPageWidth() - 3) / 3, 0.4,  "", 'TRLB', 0, 'C', 1);
        $this->Cell(0.73);

        $this->SetY(-0.8);

        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(10, .3, utf8_decode('Formato generado desde el módulo de compra. Fecha de registro: ' . date("d/m/Y", strtotime($this->requisicion->fecha))), 0, 0, 'L');
        $this->Cell(9.5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');

    }

    function create()
    {
        $this->SetMargins(1, .5, 2);
        $this->SetAutoPageBreak(true, 2);
        $this->AliasNbPages();
        $this->AddPage();


        //     Partidas
        $this->partidas();

        try {
            $this->Output('I', 'Formato - Requisicion.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }
}
