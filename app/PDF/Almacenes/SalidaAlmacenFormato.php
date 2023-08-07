<?php


namespace App\PDF\Almacenes;


use App\Models\CADECO\EntradaMaterial;
use App\Models\CADECO\SalidaAlmacen;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\Obra;
use App\Facades\Context;
use Illuminate\Support\Facades\App;

class SalidaAlmacenFormato extends Rotation
{
    protected $obra;
    var $encola = '';
    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     * SalidaAlmacenFormato constructor.
     * @param $id
     */
    public function __construct($id)
    {
        parent::__construct('P', 'cm', 'A4');
        $this->obra = Obra::find(Context::getIdObra());
        $this->SetAutoPageBreak(true, 3);
        $this->salida = SalidaAlmacen::find($id);
        $this->numero_folio = $this->salida->numero_folio_format;
        $this->fecha = $this->salida->fecha_format;
        $this->fecha_registro = $this->salida->fecha_hora_registro_format;
        $this->almacen = $this->salida->almacen->descripcion;
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
        $this->Cell(3.5, .7, ''.$this->numero_folio.'', 'RT', 0, 'L');
        $this->Ln(.7);
        $y_f = $this->GetY();
        $this->SetY(1);
        if($this->salida['opciones']==='65537'){
            $this->SetFont('Arial', 'B', 18);
            $this->Cell(11.5, $postTitle, utf8_decode( 'TRANSFERENCIA DE MATERIALES'), 0, 0, 'C', 0);
        }
        if($this->salida['opciones']==='1'){
            if($this->salida->entrega_contratista){
                $this->SetY(.7);
                $this->SetFont('Arial', 'B', 20);
                $this->Cell(11.5, $postTitle, utf8_decode( 'SALIDA DE MATERIALES'), 0, 0, 'C', 0);
                $this->Ln();
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(11.5, $postTitle, utf8_decode( 'Entrega a Contratista ('. $this->salida->entrega_contratista->tipo_string . ')' ), 0, 0, 'C', 0);
            }else{
                $this->SetFont('Arial', 'B', 24);
                $this->Cell(11.5, $postTitle, utf8_decode( 'SALIDA DE MATERIALES'), 0, 0, 'C', 0);
            }
        }
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->SetY($y_f);

        if($this->salida['NumeroFolioAlt']>0){
            $this->SetX($x_f);
            $this->Cell(4.5, .7, 'FOLIO ALM ', 'L', 0, 'L');
            $this->Cell(3.5, .7, $this->salida->NumeroFolioAlt, 'R', 0, 'L');
            $this->Ln(.7);
        }

        if($this->salida->entrega_contratista){
            $this->SetX($x_f);
            $this->Cell(4.5, .7, 'FOLIO CONTRATISTA ', 'L', 0, 'L');
            $this->Cell(3.5, .7, $this->salida->entrega_contratista->numero_folio_format, 'R', 0, 'L');
            $this->Ln(.7);
        }

        $this->SetX($x_f);
        $this->Cell(4.5, .7, 'FECHA', 'LB', 0, 'L');
        $this->Cell(3.5, .7, $this->fecha, 'RB', 1, 'L');
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


        $this->Row([utf8_decode($this->obra->nombre_obra_formatos . '  ' . " ")]);
        $this->Ln(.2);
        if($this->salida->entrega_contratista) {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(3, .5, utf8_decode("Contratista:"), 0, 0, 'L');
            $this->SetFont('Arial', '', 12);
            $this->MultiCell(16.5, .5, utf8_decode($this->salida->empresa->razon_social), 0, 'L');
            $this->Ln(.2);
        }
        $this->SetFont('Arial', '', 10);
        $this->Cell(9.5, .5, utf8_decode("Almacén"), 0, 0, 'L');
        $this->Cell(.5);
        $this->Cell(9.5, .5, 'Empresa', 0, 0, 'L');
        $this->Ln(.5);
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
        $this->MultiCell(9.5, .5, utf8_decode(strtoupper($this->almacen)), '', 'L');

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

        $currentPage = $this->PageNo();
        if($currentPage>1){
            $this->Ln();
        }
    }

    public function tableHeader()
    {
        $this->Ln(1.8);
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180,180,180);
        $this->SetWidths([1,2.5,12,2,2]);
        $this->SetStyles(['DF','DF','DF','DF','DF']);
        $this->SetRounds(['1','','','','2']);
        $this->SetRadius([0.2,0,0,0,0.2]);
        $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
        $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C','C','C','C','C',]);
        $this->Row(["#","No. Parte",utf8_decode("Descripción"), "Unidad", "Cantidad"]);
    }

    public function partidas()
    {
        $this->tableHeader();
            foreach ($this->salida->partidas as $i => $p) {
                $this->dim = $this->GetY();
                $this->SetWidths([1, 2.5, 12, 2, 2]);
                $this->SetRounds(['', '', '', '', '']);
                $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
                $this->SetAligns(['L', 'L', 'L', 'L', 'R']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
                $this->Row([
                       $i + 1,
                       $p->material['numero_parte'],
                       utf8_decode($p->material['descripcion']),
                       $p['unidad'],
                       $p->cantidad_format
                ]);

                /*Guiones*/
                $this->SetRounds(['4', '', '', '', '', '', '', '', '3']);
                $this->SetFills(['255,255,255']);
                $this->SetRadius([0, 0, 0, 0, 0, 0, 0, 0, 0]);
                $this->SetWidths([19.5]);
                $this->SetAligns(['L']);
                $this->Row(["---"]);

                $this->dim_2 = $this->GetY();
                if($this->dim_2>24) {
                    $this->AddPage();
                    $this->tableHeader();
                    if ($p->concepto['nivel'] > 0) {
                        $nivel = $p->concepto['nivel'];
                        $nivel = $p->concepto->getAncestrosAttribute($nivel);
                        $this->SetHeights([0.4]);
                        $this->SetWidths([1, 18.5]);
                        $this->SetFont('Arial', '', 6);
                        $this->SetRounds(['', '']);
                        $this->SetFills(['180,180,180', '255,255,255']);
                        $this->SetAligns(['L', 'L']);
                        $this->SetTextColors(['0,0,0', '0,0,0']);
                        $this->Row([
                            "Destino:",
                            utf8_decode($nivel),
                        ]);

                    } else {
                        $this->SetWidths([1, 18.5]);
                        $this->SetFont('Arial', '', 6);
                        $this->SetRounds(['', '']);
                        $this->SetFills(['180,180,180', '255,255,255']);
                        $this->SetAligns(['L', 'L']);
                        $this->SetTextColors(['0,0,0', '0,0,0']);

                        if (empty($p->almacen['descripcion'])) {
                            $destino = '';
                        } else {
                            $destino = $p->almacen['descripcion'];
                        }
                        $this->Row([
                            "Destino:",
                            utf8_decode($destino),
                        ]);
                    }
                }
                else{
                    if ($p->concepto['nivel'] > 0) {
                        $nivel = $p->concepto['nivel'];
                        $nivel = $p->concepto->getAncestrosAttribute($nivel);
                        $this->SetWidths([1, 18.5]);
                        $this->SetRounds(['', '']);
                        $this->SetFills(['180,180,180', '255,255,255']);
                        $this->SetAligns(['L', 'L']);
                        $this->SetTextColors(['0,0,0', '0,0,0']);
                        $this->Row([
                            "Destino:",
                            utf8_decode($nivel),
                        ]);
                    } else {
                        $this->SetWidths([1, 18.5]);
                        $this->SetRounds(['', '']);
                        $this->SetFills(['180,180,180', '255,255,255']);
                        $this->SetAligns(['L', 'L']);
                        $this->SetTextColors(['0,0,0', '0,0,0']);

                        if (empty($p->almacen['descripcion'])) {
                            $destino = '';
                        } else {
                            $destino = $p->almacen['descripcion'];
                        }
                        $this->Row([
                            "Destino:",
                            utf8_decode($destino),
                        ]);
                    }
                }
            }

        $this->dim_2 = $this->GetY();

        if($this->dim_2>24) {
            $this->AddPage();
            $this->Ln(1.8);
            $this->Ln(.7);
            $this->SetWidths([19.5]);
            $this->SetRounds(['12']);
            $this->SetRadius([0.2]);
            $this->SetFills(['180,180,180']);
            $this->SetTextColors(['0,0,0']);
            $this->SetHeights([0.4]);
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
            $this->SetHeights([0.4]);
            $this->SetFont('Arial', '', 9);
            $this->SetWidths([19.5]);
            $this->encola="observaciones";

            $this->Row([utf8_decode($this->salida['observaciones'])]);
        }else{
            $this->Ln(.7);
            $this->SetWidths([19.5]);
            $this->SetRounds(['12']);
            $this->SetRadius([0.2]);
            $this->SetFills(['180,180,180']);
            $this->SetTextColors(['0,0,0']);
            $this->SetHeights([0.4]);
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
            $this->SetHeights([0.4]);
            $this->SetFont('Arial', '', 9);
            $this->SetWidths([19.5]);
            $this->encola="observaciones";

            $this->Row([utf8_decode($this->salida['observaciones'])]);
        }
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
        $this->Cell(10, .3, utf8_decode('Formato generado desde el sistema de almacenes. Fecha y hora de registro: '  .$this->fecha_registro . ' ') , 0, 0, 'L');
        $this->Cell(9.5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create()
    {
        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 3);
        $this->partidas();

        try {
            $this->Output('I', 'Formato - Salida de Almacen.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }
}
