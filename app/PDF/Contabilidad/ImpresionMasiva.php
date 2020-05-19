<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 15/05/2020
 * Time: 12:36 PM
 */

namespace App\PDF\Contabilidad;

use App\Facades\Context;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Concepto;
use App\Models\CADECO\Factura ;
use Illuminate\Support\Facades\App;

class ImpresionMasiva extends Rotation
{
    protected $dato;
    protected $data;
    private $encabezado_pdf = '';
    var $encola = '';


    public function __construct($data)
    {

        parent::__construct('P', 'cm', 'Letter');

        $conceptos = Concepto::whereIn('id_concepto', $data)->get();

        $this->data = $conceptos;

        // dd($data, $conceptos);

        // $this->obra = Obra::find(Context::getIdObra());
        // $this->factura = Factura::find($id);
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(13.5);
        $this->Cell(3.5, .7, 'FOLIO ', 'LT', 0, 'L');
        $this->Cell(2.5, .7, 'PANDA ', 'RT', 0, 'L');
        $this->Ln(.7);
        $this->SetFont('Arial', 'B', 25);
        $this->Cell(13.5, .7, 'CONTRARECIBO', 0, 0, 'C', 0);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(3.5, .7, 'FECHA ', 'LB', 0, 'L');
        $this->Cell(2.5, .7, 'POLAR ', 'RB', 0, 'L');
        $this->Ln(1);


        $this->SetFont('Arial', 'B', 13);
        $this->Cell(19.5, 0.7, 'PARDO ' . $this->dato->descripcion, 1, 0, 'C');
        $this->Ln(1);

        $this->SetFont('Arial', '', 10);
        $this->Cell(19.5, .5, 'Recibimos de:', 0, 0, 'L');
        $this->Ln(0.5);
        $this->MultiCell(19.5, .5, 'KOALA', 1, 'J',  0);
        $this->Ln(0.5);

        $this->Cell(19.5, .5, utf8_decode('La siguiente factura para su revisión: '), 0, 'L');
        $this->Ln(0.5);


    }

    function factura(){

        $this->SetFont('Arial', '', 9);
        $this->SetFillColor(180, 180, 180);
        $this->SetWidths(array(0.60,  4.45, 3.15, 3.15, 5, 3.15));
        $this->SetStyles(array('DF', 'DF', 'DF', 'FD', 'DF'));
        $this->SetRounds(array('1', '', '', '', '', '2'));
        $this->SetRadius(array(0.2, 0, 0,  0, 0, 0.2));
        $this->SetFills(array('180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
        $this->SetHeights(array(0.5));
        $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
        $this->Row(array("#",  utf8_decode("Número"), "Fecha", "Vencimiento", "Importe", "Moneda"));

        $this->SetWidths(array(0.60, 4.45, 3.15, 3.15, 5, 3.15));
        $this->SetRounds(array('4', '', '', '', '', '3'));
        $this->SetRadius(array(0.2, 0, 0, 0, 0, 0.2));
        $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
        $this->SetAligns(array('C', 'L', 'C', 'C', 'R', 'C'));
        $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));

        $this->Row(array(1, 'A', 'B', 'C', 'D', 'E'));

        $this->Ln(.5);
        $this->SetFont('Arial', '', 10);
        $this->CellFitScale(19.5, .5, 'Observaciones:', 0, 0, 'L');
        $this->Ln(.5);
        $this->MultiCell(19.5, .5, 'Panditas ', 1, 'J',  0);
        $this->Ln(.5);
        $this->SetFont('Arial', 'B', 15);
        $this->CellFitScale(19.5, .5, utf8_decode('Los presente factura se toma a revisión SIN originar obligación alguna para pago'), 0, 0, 'L');
    }

    function Footer(){
        $this->firmas();
        $this->SetY(-3.8);
        $this->SetY(-1);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(15, .4, utf8_decode('Formato generado desde el sistema de finanzas. Fecha de registro: ') .' Fecha de consulta: '.date("d-m-Y h:i:s")
            , 0, 0, 'L');

        $this->SetFont('Arial', 'B', 6);
        $this->SetTextColor('100,100,100');
        $this->Cell(4.5, .4, utf8_decode('Sistema de Administración de Obra'), 0, 0, 'R');

        $this->SetY(-0.7);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(19.5, .5, utf8_decode('Página') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function firmas(){
        $this->SetY(-3.8);
        $this->Cell(12.5);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', '', 10);
        $this->CellFitScale(7, .5, ('Recibi '), 1, 0, 'C');
        $this->Ln(.5);
        $this->Cell(12.5);
        $this->CellFitScale(7, 2, ' ', 1, 0, 'R');
    }

    function create() {
        foreach($this->data as $dato){
            $this->dato = $dato;
            $this->SetMargins(1, 0.5, 1);
            $this->AliasNbPages();
            $this->AddPage();
            $this->SetAutoPageBreak(true,3.75);
            $this->factura();
        }
        


        try {
            $this->Output('I', 'Formato - Contrarecibo.pdf', 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }
}