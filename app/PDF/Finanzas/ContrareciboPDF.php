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
use App\Models\CADECO\TipoTransaccion;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\ContraRecibo ;
use App\Models\CADECO\Factura ;

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
    }
    function Header()
    {


    }

    function Footer(){
        /*$this->firmas();*/
        $this->SetY(-3.8);





        $this->SetY(-1);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(10, .4, utf8_decode('Formato generado desde el sistema de finanzas. Fecha de registro: '.$this->factura->fecha_hora_registro_format), 0, 0, 'L');

        $this->SetFont('Arial', 'B', 6);
        $this->SetTextColor('100,100,100');
        $this->Cell(9.5, .4, utf8_decode('Sistema de Administración de Obra'), 0, 0, 'R');

        $this->SetY(-0.7);
        $this->SetTextColor('0,0,0');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(19.5, .5, utf8_decode('Página') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function create() {
        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,3.75);


        try {
            $this->Output('I', 'Formato - Contrarecibo.pdf', 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }
}