<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 14/01/2020
 * Time: 09:36 PM
 */

namespace App\PDF\Finanzas;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\Factura ;
use Illuminate\Support\Facades\App;

class ContrareciboPDF extends Rotation
{
    protected $obra;
    protected $factura;
    private $encabezado_pdf = '';
    var $encola = '';


    public function __construct($id)
    {

        parent::__construct('P', 'cm', 'Letter');

        $this->obra = Obra::find(Context::getIdObra());
        $this->factura = Factura::find($id);

        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,3.75);
        $this->factura();
    }
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(13.5);
        $this->Cell(3.5, .7, 'FOLIO ', 'LT', 0, 'L');
        $this->Cell(2.5, .7, $this->factura->contra_recibo->numero_folio_format . ' ', 'RT', 0, 'L');
        $this->Ln(.7);
        $this->SetFont('Arial', 'B', 25);
        $this->Cell(13.5, .7, 'CONTRARECIBO', 0, 0, 'C', 0);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(3.5, .7, 'FECHA ', 'LB', 0, 'L');
        $this->Cell(2.5, .7, $this->factura->contra_recibo->fecha_format . ' ', 'RB', 0, 'L');
        $this->Ln(1);


        $this->SetFont('Arial', 'B', 13);
        $this->Cell(19.5, 0.7,  utf8_decode($this->factura->obra->nombre_obra_formatos). ' ', 1, 0, 'C');
        $this->Ln(1);

        $this->SetFont('Arial', '', 10);
        $this->Cell(19.5, .5, 'Recibimos de:', 0, 0, 'L');
        $this->Ln(0.5);
        $this->MultiCell(19.5, .5, $this->factura->empresa->razon_social , 1, 'J',  0);
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

        $this->Row(array(1, $this->factura->referencia, $this->factura->fecha_format, $this->factura->vencimiento_format, $this->factura->monto_format, $this->factura->moneda->nombre));

        $this->Ln(.5);
        $this->SetFont('Arial', '', 10);
        $this->CellFitScale(19.5, .5, 'Observaciones:', 0, 0, 'L');
        $this->Ln(.5);
        $this->MultiCell(19.5, .5, utf8_decode($this->factura->contra_recibo->observaciones) . ' ', 1, 'J',  0);
        $this->Ln(.5);
        $this->SetFont('Arial', 'B', 15);
        $this->CellFitScale(19.5, .5, utf8_decode('Los presente factura se toma a revisión SIN originar obligación alguna para pago'), 0, 0, 'L');
    }

    function firmas(){
        $this->SetY(-3.8);
        $this->Cell(12.5);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', '', 8);
        $this->CellFitScale(7, .5, utf8_decode('Recibí'), 1, 0, 'C');
        $this->Ln(.5);
        $this->Cell(12.5);
        $this->CellFitScale(7, 1.5,'', 1, 1, 'R');
        $this->Cell(12.5);
        $this->SetFont('Arial', '', 8);
        $this->CellFitScale(7,.5,$this->factura->usuario ? utf8_decode($this->factura->usuario->nombre_completo) : '',1,1,"C");
    }

    function Footer(){
        if (!App::environment('production')) {
            $this->SetFont('Arial','B',80);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(5,20,utf8_decode("MUESTRA"),45);
            $this->RotatedText(6,26,utf8_decode("SIN VALOR"),45);
            $this->SetTextColor('0,0,0');
        }
        $this->firmas();
        $this->SetY(-3.8);
        $this->SetY(-1);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(15, .4, utf8_decode('Formato generado desde el sistema de finanzas. Fecha de registro: '
            .$this->factura->fecha_hora_registro_format)
            .' Fecha de consulta: '.date("d-m-Y h:i:s")
            , 0, 0, 'L');

        $this->SetFont('Arial', 'B', 6);
        $this->SetTextColor('100,100,100');
        $this->Cell(4.5, .4, utf8_decode('Sistema de Administración de Obra'), 0, 0, 'R');

        $this->SetY(-0.7);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(19.5, .5, utf8_decode('Página') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create() {
        try {
            $this->Output('I', 'Formato - Contrarecibo.pdf', 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }
}
