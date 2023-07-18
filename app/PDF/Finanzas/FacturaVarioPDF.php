<?php

namespace App\PDF\Finanzas;

use App\Facades\Context;
use Ghidev\Fpdf\Rotation;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Factura ;
use App\Utils\ValidacionSistema;
use Illuminate\Support\Facades\App;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FacturaVarioPDF extends Rotation
{
    protected $obra;
    protected $factura;
    private $cadena_qr = '';
    var $encola = '';


   public function __construct($id){
      parent::__construct('P', 'cm', 'Letter');

      $this->obra = Obra::find(Context::getIdObra());
      $this->factura = Factura::find($id);

      $this->SetMargins(1, 0.5, 2);
      $this->AliasNbPages();
      $this->AddPage();
      if (Context::getDatabase() == "SAO1814" && Context::getIdObra() == 41) {
         $this->SetAutoPageBreak(true, 7.3);
      } else {
            $this->SetAutoPageBreak(true, 6.3);
      }
      $this->partidas();
   }

   function Header(){
      $postTitle = .7;
      $tamanotitle=18;
      $xtitle=13.5;
      $title_proyecto=13;
      if(Context::getDatabase() == "SAO1814" && Context::getIdObra() == 41){
         $this->image(public_path('/img/LOGOTIPO_REHABILITACION_ATLACOMULCO.png'), 1, .2, 4, 2);
         $postTitle = 0;
         $tamanotitle=12.5;
         $xtitle=17;
         $title_proyecto=12;

      }

      $this->SetTextColor('0,0,0');
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(13.5);
      $this->Cell(2.3, .5, 'FOLIO', 'LT', 0, 'L');
      $this->CellFitScale(3.7, .5, $this->factura->numero_folio_format, 'RT', 0, 'L');
      $this->Ln(.5);
      $this->SetFont('Arial', 'B', $tamanotitle);

      $this->Cell($xtitle, $postTitle, "FACTURA DE VARIOS", 0, 0, 'C', 0);
      $this->SetFont('Arial', 'B', 10);
      if (Context::getDatabase() == "SAO1814" && Context::getIdObra() == 41) {
         $this->SetX(14.5);
      }
      $this->Cell(2.3, .5, 'FECHA ', 'L B', 0, 'L');
      $this->Cell(3.7, .5, $this->factura->fecha_format, 'R B', 0, 'L');
      $this->Ln(1);

      $this->SetFont('Arial', 'B', $title_proyecto);

      $this->SetWidths(array(19.5));
      $this->SetRounds(array('1234'));
      $this->SetRadius(array(0.2));
      $this->SetFills(array('255,255,255'));
      $this->SetTextColors(array('0,0,0'));
      $this->SetHeights(array(0.7));
      $this->SetRounds(array('1234'));
      $this->SetRadius(array(0.2));
      $this->SetAligns("C");
      $this->Row(array( $this->obra->descripcion != null ? $this->obra->descripcion : $this->obra->nombre));
      $this->Ln(.2);
      $y_inicial = $this->getY();
      ###########################

      $this->SetFont('Arial', '', 10);
      $this->Cell(9.5, .7, utf8_decode('DATOS CONTRARECIBO'), 0, 0, 'L');
      $this->Ln(.6);

      $this->SetFont('Arial', '', 10);
      $this->CellFitScale(2.5, .5, "Folio:", '', 'J');
      $this->CellFitScale(7, .5, $this->factura->contra_recibo->numero_folio_format, '', 'L');
      $this->Ln(.5);
      $this->CellFitScale(2.5, .5, "Naturaleza:", '', 'J');
      $this->CellFitScale(7, .5, "Gastos Varios", '', 'L');
      $this->Ln(.5);
      $this->CellFitScale(2.5, .5, "Fecha:", '', 'J');
      $this->CellFitScale(7, .5, $this->factura->fecha_format, '', 'L');
      $this->Ln(.5);
      $this->CellFitScale(2.5, .5, "Vencimiento:", '', 'J');
      $this->CellFitScale(7, .5, $this->factura->vencimiento_form, '', 'L');

      $y_final_1 = $this->getY();

      $this->setY($y_inicial + 0.6);
      ###########################
      $direccion = '';
      if($sucursales = $this->factura->empresa->sucursales){
         $direccion = $sucursales[0]->direccion;
      }

      $this->setY($y_inicial);
      $this->setX(11);
      $this->SetFont('Arial', '', 10);
      $this->Cell(9.5, .7, utf8_decode('PROVEEDOR'), 0, 0, 'L');
      $this->Ln(.6);
      $this->setX(11);
      $this->SetFont('Arial', 'B', 10);
      $this->CellFitScale(9.5, .5, $this->factura->empresa->razon_social . ' ', '', 'J');
      $this->Ln(.5);
      $this->setX(11);
      $this->SetFont('Arial', '', 10);
      $this->Multicell(9.5, .5, $direccion . '
RFC: ' . $this->factura->empresa->rfc, '', 'J');
      $y_final_2 = $this->getY();
      if($y_final_1>=$y_final_2){
          $y_final = $y_final_1;
      }else{
          $y_final = $y_final_2;
      }
      $alto = $y_final - $y_inicial;

      $this->SetWidths(array(9.5,0.5, 9.5));
      $this->SetRounds(array('1234','', '1234'));
      $this->SetRadius(array(0.2,0, 0.2));
      $this->SetFills(array('255,255,255', '255,255,255', '255,255,255'));
      $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0'));
      $this->SetHeights(array($alto));
      $this->SetStyles(array('DF', "F", 'DF'));
      $this->SetAligns("L");
      $this->SetFont('Arial', 'B', 10);
      $this->setY($y_inicial+0.6);
      $this->Row(array("","",""));
      $this->setY($y_inicial + 0.6);

       //se sobre escribe la información
       $this->setX(1);
       $this->SetFont('Arial', 'B', 10);
       $this->CellFitScale(2.5, .5, "Folio:", '', 'J');
       $this->SetFont('Arial', '', 10);
       $this->CellFitScale(7, .5, $this->factura->contra_recibo->numero_folio_format, '', 'L');
       $this->Ln(.5);
       $this->SetFont('Arial', 'B', 10);
       $this->CellFitScale(2.5, .5, "Naturaleza:", '', 'J');
       $this->SetFont('Arial', '', 10);
       $this->CellFitScale(7, .5, "Gastos Varios", '', 'L');
       $this->Ln(.5);
       $this->SetFont('Arial', 'B', 10);
       $this->CellFitScale(2.5, .5, "Fecha:", '', 'J');
       $this->SetFont('Arial', '', 10);
       $this->CellFitScale(7, .5, $this->factura->fecha_format, '', 'L');
       $this->Ln(.5);
       $this->SetFont('Arial', 'B', 10);
       $this->CellFitScale(2.5, .5, "Vencimiento:", '', 'J');
       $this->SetFont('Arial', '', 10);
       $this->CellFitScale(7, .5, $this->factura->vencimiento_form, '', 'L');

      //se sobre escribe info
      $this->setY($y_inicial + 0.6);
      $this->SetFont('Arial', 'B', 10);
      $this->setX(11);
      $this->CellFitScale(9.5, .5, $this->factura->empresa->razon_social . ' ', '', 'J');
      $this->Ln(.5);
      $this->setX(11);
      $this->SetFont('Arial', '', 10);
      $this->Multicell(9.5, .5, $direccion . '
RFC: ' . $this->factura->empresa->rfc, '', 'J');

      $this->Ln(.5);

      if ($this->encola == "partida") {
         $this->SetWidths(array(0.5, 2, 7, 1.5, 1.5, 1.5, 1.5, 4));
         $this->SetFont('Arial', '', 8);
         $this->SetStyles(array('DF',  'DF', 'DF', 'DF', 'DF', 'DF', 'DF'));
         $this->SetWidths(array(0.5, 2, 7, 1.5, 1.5, 1.5, 1.5, 4));
         $this->SetRounds(array('1',  '', '', '',  '', '', '', '2'));
         $this->SetRadius(array(0.5, 2, 7, 1.5, 1.5, 1.5, 1.5, 4));
         $this->SetFills(array('180,180,180',  '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180'));
         $this->SetTextColors(array('0,0,0',  '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
         $this->SetHeights(array(0.3));
         $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
         $this->Row(array("#",  "No. Parte", utf8_decode("Descripción"), "Unidad",  "Cantidad", "Precio", "Monto", "Destino"));
         $this->SetTextColors(array('0,0,0'));
         $this->SetWidths(array(0.5, 2, 7, 1.5, 1.5, 1.5, 1.5, 4));
         $this->SetRounds(array('', '', '', '', '', '', '', ''));
         $this->SetRadius(array(0, 0, 0, 0, 0, 0, 0, 0));
         $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
         $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
         $this->SetHeights(array(0.3));
         $this->SetAligns(array('C', 'C', 'L', 'L', 'R', 'R', 'R', 'L'));
      } else if ($this->encola == "observaciones_partida") {
         $this->SetRadius(array(0));
         $this->SetTextColors(array('150,150,150'));
         $this->SetWidths(array(19.5));
         $this->SetAligns(array('J'));
      } else if ($this->encola == "observaciones_encabezado") {
         $this->SetWidths(array(19.5));
         $this->SetRounds(array('12'));
         $this->SetRadius(array(0.2));
         $this->SetFills(array('180,180,180'));
         $this->SetTextColors(array('0,0,0'));
         $this->SetHeights(array(0.3));
         $this->SetFont('Arial', '', 8);
         $this->SetAligns(array('C'));
      } else if ($this->encola == "observaciones") {
         $this->SetRounds(array('34'));
         $this->SetRadius(array(0.2));
         $this->SetAligns(array('J'));
         $this->SetStyles(array('DF'));
         $this->SetFills(array('255,255,255'));
         $this->SetTextColors(array('0,0,0'));
         $this->SetHeights(array(0.3));
         $this->SetFont('Arial', '', 8);
         $this->SetWidths(array(19.5));
      }else if ($this->encola == ""){
         $this->Ln();
         $this->SetFont('Arial', 'B', 8);
         $this->Cell(3, 0.5, "Concepto:");
         $this->SetFont('Arial', '', 8);
         $this->MultiCell(15, 0.5, $this->factura->concepto->descripcion);

         if($this->factura->costo) {
             $this->SetFont('Arial', 'B', 8);
             $this->Cell(3, 0.5, "Tipo de Gasto:");
             $this->SetFont('Arial', '', 8);
             $this->MultiCell(16, 0.5, $this->factura->costo->descripcion);
         }
      }
   }

   function partidas(){
      $this->Ln();
      if ($this->factura->partidas->count() > 0) {
         $i = 1;
         $this->SetWidths(array(0.5, 2, 7, 1.5, 1.5, 1.5, 1.5, 4));
         $this->SetFont('Arial', '', 8);
         $this->SetStyles(array('DF', 'DF', 'DF', 'FD', 'DF', 'DF', 'DF', 'DF'));
         $this->SetRounds(array('1', '', '', '', '', '',  '', '2'));
         $this->SetRadius(array(0.2,  0, 0, 0, 0, 0, 0, 0.2));
         $this->SetFills(array('180,180,180',  '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180'));
         $this->SetTextColors(array('0,0,0',  '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
         $this->SetHeights(array(0.4));
         $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
         $this->Row(array("#",  "No. Parte", utf8_decode("Descripción"), "Unidad",  "Cantidad", "Precio", "Monto", "Destino"));
         $ultima = $this->factura->partidas->count();
         foreach ($this->factura->partidas as $partida) {
            $this->SetFont('Arial', '', 8);
            $this->SetWidths(array(0.5, 2, 7, 1.5, 1.5, 1.5, 1.5, 4));
            $this->SetRounds(array('', '', '', '', '', '', '', ''));
            $this->SetRadius(array(0, 0, 0, 0, 0, 0, 0, 0));
            $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
            $this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
            $this->SetHeights(array(0.4));
            $this->SetAligns(array('C', 'C', 'L', 'L', 'R', 'R', 'R', 'L'));

            if($i == $ultima){
                  $this->SetRounds(array('4', '', '', '', '', '',  '', '3'));
                  $this->SetRadius(array(0.2,  0, 0, 0, 0, 0, 0, 0.2));
            }

            $this->SetWidths(array(0.5, 2, 7, 1.5, 1.5, 1.5, 1.5, 4));
            $this->encola = "partida";
            $this->Row(array(
                  $i,
                  "",
                  $partida->referencia,
                  $partida->unidad,
                  $partida->cantidad,
                  $partida->importe,
                  $partida->cantidad * $partida->importe,
                  $partida->concepto->descripcion,
                  ));
            $i++;
         }
      } else {
         $this->CellFitScale(19.5, 1, 'FACTURA SIN PARTIDAS', 1, 0, 'C');
         $this->Ln(12);
      }
      $this->Ln(.5);

      $subtotal = $this->factura->monto - $this->factura->impuesto + $this->factura->retencion;
      $this->SetFillColor(180,180,180);
      $this->setX(15.5);
      $this->cell(2, 0.5, "Subtotal:",1,"","",1);
      $this->setX(17.5);
      $this->Cell(3, 0.5, number_format($subtotal,2,'.',','),1,0,"R");

      $this->Ln();

      $this->setX(15.5);
      $this->Cell(2, 0.5, "IVA:",1,0,"",1);
      $this->setX(17.5);
      $this->Cell(3, 0.5, number_format($this->factura->impuesto,2,'.',','),1,0,"R");

      $this->Ln();

      $this->setX(15.5);
      $this->Cell(2, 0.5, utf8_decode("Retención:"),1,0,"",1);
      $this->setX(17.5);
      $this->Cell(3, 0.5, number_format($this->factura->retencion,2,'.',','),1,0,"R");

      $this->Ln();

      $this->setX(15.5);
      $this->Cell(2, 0.5, "Total:",1,0,"",1);
      $this->setX(17.5);
      $this->Cell(3, 0.5, number_format($this->factura->monto,2,'.',','),1,0,"R");

      $this->Ln(1);

      $this->SetWidths(array(19.5));
      $this->SetRounds(array('12'));
      $this->SetRadius(array(0.2));
      $this->SetFills(array('180,180,180'));
      $this->SetTextColors(array('0,0,0'));
      $this->SetHeights(array(0.4));
      $this->SetFont('Arial', '', 8);
      $this->SetAligns(array('C'));
      $this->encola = "observaciones_encabezado";
      $this->Row(array("Observaciones"));
      $this->SetRounds(array('34'));
      $this->SetRadius(array(0.2));
      $this->SetAligns(array('J'));
      $this->SetStyles(array('DF'));
      $this->SetFills(array('255,255,255'));
      $this->SetTextColors(array('0,0,0'));
      $this->SetHeights(array(0.4));
      $this->SetFont('Arial', '', 8);
      $this->SetWidths(array(19.5));
      $this->encola = "observaciones";
      $this->Row(array(utf8_decode($this->factura->observaciones)));

   }

   function Footer() {
      if (!App::environment('production')) {
         $this->SetFont('Arial', 'B', 80);
         $this->SetTextColor(155, 155, 155);
         $this->RotatedText(5, 15, utf8_decode("MUESTRA"), 45);
         $this->RotatedText(6, 21, utf8_decode("SIN VALOR"), 45);
         $this->SetTextColor('0,0,0');
      }

      $this->SetFont('Arial', '', 6);
      if (Context::getDatabase() == "SAO1814" && Context::getIdObra() == 41) {
         $this->SetY(-7);
         $this->SetFont('Arial', '', 6);
         $this->SetFillColor(180, 180, 180);
         $this->Cell(4.8, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
         $this->Cell(4.8, .4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
         $this->Cell(10, .4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
         $this->Ln();

         $this->Cell(4.8, .4, utf8_decode('Jefe Almacén'), 'TRLB', 0, 'C', 1);
         $this->Cell(4.8, .4, 'Gerente Administrativo', 'TRLB', 0, 'C', 1);
         $this->Cell(5, .4, utf8_decode('Control de Costos'), 'TRLB', 0, 'C', 1);
         $this->Cell(5, .4, 'Director de proyecto', 'TRLB', 0, 'C', 1);
         $this->Ln();

         $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
         $this->Cell(4.8, 1.2, '', 'TRLB', 0, 'C');
         $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
         $this->Cell(5, 1.2, '', 'TRLB', 0, 'C');
         $this->Ln();

         $this->Cell(4.8, .4, 'C.P. HEDGAR GARCIA GAYTAN', 'TRLB', 0, 'C', 1);
         $this->Cell(4.8, .4, 'C.P. JAVIER MENDEZ JOSE', 'TRLB', 0, 'C', 1);
         $this->Cell(5, .4, 'ING. JUAN CARLOS MARTINEZ ANTUNA', 'TRLB', 0, 'C', 1);
         $this->Cell(5, .4, 'ING. PEDRO ALFONSO MIRANDA REYES', 'TRLB', 0, 'C', 1);
      }else{
         $this->SetY(-6);

         $this->CellFitScale(6, .5, utf8_decode('Solicitó'), 1, 0, 'C');
         $this->Cell(.7);
         $this->CellFitScale(6, .5, utf8_decode('Capturó'), 1, 0, 'C');
         $this->Cell(.8);
         $this->CellFitScale(6, .5, utf8_decode('Aprobó'), 1, 0, 'C');
         $this->Ln(.5);
         $this->CellFitScale(6, 1, ' ', 1, 0, 'C');
         $this->Cell(.7);
         $this->CellFitScale(6, 1, ' ', 1, 0, 'C');
         $this->Cell(.8);
         $this->CellFitScale(6, 1, ' ', 1, 0, 'R');

      }
      $this->SetY(23.5);
      $this->image("data:image/png;base64," . base64_encode(QrCode::format('png')->generate($this->generaQr())), $this->GetX(), $this->GetY(), 3.5, 3.5, 'PNG');

      $this->SetY(23.7);
      $this->setX(4.5);
      $this->MultiCell(16, 0.4, utf8_decode($this->cadena_qr));
      $this->SetFont('Arial', '', 6);
      $this->SetY(26.5);
      $this->setX(4.5);
      $this->Cell(2.5, .3, utf8_decode('* Cantidad Por Comprar'), 0, 0, 'L');

      $this->Cell(4, .3, utf8_decode('** Importe Por Comprar'), 0, 0, 'L');

      $this->SetFont('Arial', 'B', 6);
      $this->SetTextColor('100,100,100');
      $this->SetY(26.5);
      $this->Cell(19.5, .4, (utf8_decode('Sistema de Administración de Obra')), 0, 0, 'R');

      $this->SetFont('Arial', 'BI', 6);
      $this->SetY(27);
      $this->SetTextColor('0,0,0');
      $this->Cell(7, .4, (utf8_decode('Formato generado desde el módulo de ordenes de compra.')), 0, 0, 'L');

      $this->Ln(.4);
      $this->SetY(-0.9);
      $this->SetTextColor('0,0,0');
      $this->SetFont('Arial', 'BI', 6);
      $this->Cell(19.5, .4, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
   }

   function generaQr(){
      $verifica = new ValidacionSistema();
      $fecha_fac = date_create($this->factura->fecha);
      $fecha_cr = date_create($this->factura->contra_recibo->fecha);
      $cadena = [
        $this->factura->numero_folio,
        $this->factura->numero_folio_format,
        $this->factura->contra_recibo->numero_folio,
        $this->factura->contra_recibo->numero_folio_format,
        $this->factura->opciones,$this->factura->opciones,
        'Gastos Varios','Gastos Varios',
        $this->factura->referencia,$this->factura->referencia,
        $this->factura->empresa->razon_social,$this->factura->empresa->razon_social,
        date_format($fecha_fac,"d-m-Y"),date_format($fecha_fac,"d-m-Y"),
        date_format($fecha_cr,"d-m-Y"),date_format($fecha_cr,"d-m-Y"),
        date_format($fecha_fac,"d-m-Y"),date_format($fecha_fac,"d-m-Y"),
        number_format($this->factura->monto,2,'.',','),number_format($this->factura->monto,2,'.',','),
        number_format($this->factura->monto,2,'.',','),number_format($this->factura->monto,2,'.',','),
     ];

     $firmada = $verifica->encripta(implode("_", $cadena));
     $this->cadena_qr = $firmada;
     return "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/api/finanzas/factura/leerQR?data=" . urlencode($firmada);
  }

   function create() {
      try {
          $this->Output('I', 'Formato - Factura Varios.pdf', 1);
      } catch (\Exception $ex) {
          dd("error",$ex);
      }
      exit;
  }
}
