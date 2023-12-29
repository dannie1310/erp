<?php

namespace App\PDF\ControlRecursos;

class prueba
{

require ("../../clases/rotation.php");
include ("../../clases/conexionf2.php");
include ("../../clases/conexionf.php");

include ("../../clases/funciones_genericas.php");
include ("../../clases/funciones_solpago.php");
include ("../../clases/funciones_firmas.php");
include ("../../clases/fecha.php");

$usr = $_REQUEST["usr"];
$serie = $_REQUEST["serie"];
$folio = $_REQUEST["folio"];
$id = base64_decode($_REQUEST["id"]);
$bd = "controlrec";
$bd2 = "igh";
    //$usr=134;

    //$alias_depto=obten_dato_usr(alias_depto, $usr);
    //$folio=756; $serie="SIS";

    /*Nos Traemos los datos de la Solicitud a partir de su Serie y Folio*/
$link = conectarf2();
if ($id>0)
{
$sql = "select * from solcheques where IdSolCheques = $id;";
}

else{
    $sql = "select * from solcheques where serie='$serie' and folio='$folio';";
}
//echo $sql."<br>";
$result = mysql_query($sql, $link);
mysql_close($link);

while ($row = mysql_fetch_array($result)) {
    $serie = $row["Serie"];
    $folio = $row["Folio"];
    $idempresa = $row["IdEmpresa"];
    $fecha = $row["Fecha"];
    $vencimiento = $row["Vencimiento"];
    $idforma_pago = $row["IdFormaPago"];
    $importe = $row["Importe"];
    $idmoneda = $row["IdMoneda"];
    $idproveedor = $row["IdProveedor"];
    $concepto = $row["Concepto"];
    $idsolcheque = $row["IdSolCheques"];
    $idtipopago = $row["IdTipoPago"];
    $banco = $row["Banco"];
    $cuenta = $row["Cuenta"];
    $cuenta2 = $row["Cuenta2"];
    $identrega = $row["IdEntrega"];
    $iddireccion = $row["IdDireccion"];
    $solicita = $row["Solicita"];
    $depto = $row["Departamento"];
    $Estatus = $row["Estatus"];
    $Solicita = $row["Solicita"];
    $Autoriza = $row["Autoriza"];
    $AutTramFinanzas = $row["AutTramFinanzas"];
    $DirGral = $row["DirGral"];
    $DirAdmonFin = $row["DirAdmonFin"];
}
$link = conectarf2();
if ($id > 0) {
    $sql2 = "SELECT
		if(relaciones_gastos.numero_folio > 0, relaciones_gastos.folio, if(rcc.IdRcc > 0, concat('RCC-', rcc.IdRcc), null)) as folio_relacion
		, solcheques.IdSolCheques, documentos.uuid
  FROM  (((
controlrec.solchequesdoctos solchequesdoctos right JOIN
controlrec.documentos documentos ON (solchequesdoctos.IdDocto = documentos.IdDocto))
left JOIN controlrec.solcheques solcheques ON (solchequesdoctos.IdSolCheque = solcheques.IdSolCheques))
LEFT JOIN controlrec.relaciones_gastos_x_documento relaciones_gastos_x_documento
ON (relaciones_gastos_x_documento.iddocumento = documentos.IdDocto))
LEFT JOIN controlrec.relaciones_gastos relaciones_gastos
ON (relaciones_gastos_x_documento.idrelaciones_gastos = relaciones_gastos.idrelaciones_gastos)
 left JOIN controlrec.rcc rcc ON(rcc.IdSol = solcheques.IdSolCheques)
 WHERE (solcheques.IdSolCheques = $id)";
} else {

    $sql2 = "SELECT
		if(relaciones_gastos.numero_folio > 0, relaciones_gastos.folio, if(rcc.IdRcc > 0, concat('RCC-', rcc.IdRcc), null)) as folio_relacion
		, solcheques.IdSolCheques, documentos.uuid
  FROM  (((
controlrec.solchequesdoctos solchequesdoctos right JOIN
controlrec.documentos documentos ON (solchequesdoctos.IdDocto = documentos.IdDocto))
left JOIN controlrec.solcheques solcheques ON (solchequesdoctos.IdSolCheque = solcheques.IdSolCheques))
LEFT JOIN controlrec.relaciones_gastos_x_documento relaciones_gastos_x_documento
ON (relaciones_gastos_x_documento.iddocumento = documentos.IdDocto))
LEFT JOIN controlrec.relaciones_gastos relaciones_gastos
ON (relaciones_gastos_x_documento.idrelaciones_gastos = relaciones_gastos.idrelaciones_gastos)
 left JOIN controlrec.rcc rcc ON(rcc.IdSol = solcheques.IdSolCheques)
 WHERE (solcheques.serie='$serie' and solcheques.folio='$folio')";

}
$uids_txt = '';
$result2 = mysql_query($sql2, $link);
while ($row2 = mysql_fetch_array($result2)) {
    $relacion = $row2["folio_relacion"];
    if ($row2["uuid"] != "") {
        $uuids[] = substr($row2["uuid"], 0, 5);
    }
}
$uids_txt = implode(" ", $uuids);
//echo $uuids;
//echo $sql2;

/*Obtenemos las descripciones para los Id's*/
$importe = number_format($importe, 2, '.', ',');
$empresa = regresa_descripcion($bd, empresas, RazonSocial, IdEmpresa, $idempresa);
$proveedor = regresa_descripcion_proveedor($idproveedor);
$moneda = regresa_descripcion($bd, 'ctg_monedas', moneda, id, $idmoneda);
$forma_pago = regresa_descripcion($bd, formaspago, Nombre, IdFormaPago, $idforma_pago);
$tipopago = regresa_descripcion($bd, tipopago, Descripcion, IdTipoPago, $idtipopago);
$entrega = regresa_descripcion($bd, entregas, Descripcion, IdEntrega, $identrega);


mysql_close($link);


/*Formateamos las Fechas*/
$fecha = fecha($fecha);
$vencimiento = fecha($vencimiento);

/*Obtenemos la Longitud del Concepto*/
$LongitudConcepto = number_format((strlen($concepto) / 105), 0, ' ', ' ');
//echo "La Long del CP es: ".$LongitudConcepto;


/*Obtenemos los Datos de la Cuenta*/
$CuentaProv = RegresaCuentaDelProveedor($cuenta2);
if ($CuentaProv == "") {
    $CuentaProv = "NO REGISTRADA";
}


class PDF extends PDF_Rotate
{
    var $Solicita;
    var $Autoriza;
    var $DirAdmonFin;
    var $DirGral;

    function PDF($p, $cm, $Letter, $Solicita, $Autoriza, $DirAdmonFin, $DirGral, $Serie, $Folio, $Estatus, $idtipopago)
    {
        parent::PDF_Rotate($p, $cm, $Letter);
        $this->Solicita = $Solicita;
        $this->Autoriza = $Autoriza;
        $this->DirAdmonFin = $DirAdmonFin;
        $this->DirGral = $DirGral;
        $this->Serie = $Serie;
        $this->Folio = $Folio;
        $this->Estatus = $Estatus;
        $this->idtipopago = $idtipopago;
        $this->calcula_importes();
        $this->es_pfp();
    }

    function RotatedText($x, $y, $txt, $angle)
    {
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

    function es_pfp()
    {
        $link = conectarf2();
        $SQLs = "
				SELECT IF(controlrec.solicitadasfp.Estatus in(0,1,2),1,0) as en_pfp,
IF(solicitadasfp.Estatus=0,concat('Solicitada PFP el ',date_format(FechaGenero,'%d-%m-%Y %H:%i:%s')),IF(solicitadasfp.Estatus=1,concat('Autorizada PFP el ',date_format(FechaGenero,'%d-%m-%Y %h:%i:%s')),IF(solicitadasfp.Estatus=2,concat('Lista para PFP el ',date_format(FechaGenero,'%d-%m-%Y %h:%i:%s')),''))) as Leyenda
 FROM controlrec
.solcheques solcheques
 left JOIN
 controlrec.solicitadasfp solicitadasfp
 ON (solcheques.IdSolCheques = solicitadasfp.IdSolicitud)
WHERE (solcheques.Serie = '" . $this->Serie . "')
 AND (solcheques.Folio = " . $this->Folio . ") order by IdRechazadasFP desc limit 1;
			";
        $r = mysql_query($SQLs, $link);
        $v = mysql_fetch_array($r);
        $this->en_pfp = $v["en_pfp"];
        $this->leyenda = $v["Leyenda"];

    }

    function calcula_importes()
    {
        $link = conectarf2();
        $sql = "
			SELECT
				   if(SUM(documentos.Total)=SUM(documentos.Importe),0.00,SUM(documentos.Importe)) AS Importe,
				   if(SUM(documentos.Total)=SUM(documentos.Importe),true,false) AS Equal,
				   SUM(documentos.Retenciones) AS Retenciones,
				   SUM(documentos.IVA) AS IVA,
				   SUM(documentos.OtrosImpuestos) AS OtrosImpuestos,
				   SUM(documentos.Total) AS Total
			  FROM    (   controlrec.solchequesdoctos solchequesdoctos
					   INNER JOIN
						  controlrec.solcheques solcheques
					   ON (solchequesdoctos.IdSolCheque = solcheques.IdSolCheques))
				   INNER JOIN
					  controlrec.documentos documentos
				   ON (solchequesdoctos.IdDocto = documentos.IdDocto)
			 WHERE (solcheques.Folio = " . $this->Folio . ") AND (solcheques.Serie = '" . $this->Serie . "')
			";
        $r = mysql_query($sql, $link);
        $v = mysql_fetch_array($r);

        $this->separacion = 1.7;
        if ($v["IVA"] == 0) $this->separacion += 2;
        if ($v["Retenciones"] == 0) $this->separacion += 2;
        if ($v["OtrosImpuestos"] == 0) $this->separacion += 2;
        if ($v["Total"] == 0) $this->separacion += 2.5;
        if ($v["Importe"] == 0) $this->separacion += 2.5;
        $this->importe = number_format($v["Importe"], 2, ".", ",");
        $this->retenciones = number_format($v["Retenciones"], 2, ".", ",");
        $this->iva = number_format($v["IVA"], 2, ".", ",");
        $this->total = number_format($v["Total"], 2, ".", ",");
        $this->otros = number_format($v["OtrosImpuestos"], 2, ".", ",");
        $this->equal = $v["Equal"];

    }

    function Header($serie = "", $folio = "")
    {
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetFillColor(204, 204, 204);

        $this->image('../../imgs/logo_hc.jpg', 1, .1, 2, 1);

        $this->Cell(9.5, .25, ' ', 0, 0, 'C');
        $this->SetTextColor(90, 90, 90);
        $this->SetFont('Arial', 'I', 7);
        if ($this->en_pfp == 1)
            $this->Cell(10, .25, $this->leyenda, 0, 1, 'R', 0);
        else
            $this->Cell(10, .25, '', 0, 1, 'C', 0);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(12.5, .25, ' ', 0, 0, 'C');
        $this->Cell(3.5, .25, 'SERIE', 1, 0, 'C', 1);
        $this->Cell(3.5, .25, 'FOLIO', 1, 1, 'C', 1);

        $this->Cell(3, .5, ' ', 0, 0, 'C');

        $this->SetFont('Arial', 'BU', 12);
        //ECHO $this->idtipopago;
        if ($this->Estatus == 1 || $this->Estatus == 2 || $this->Estatus == 3 || $this->Estatus == 4 || $this->Estatus == 5 || $this->Estatus == 6 || $this->Estatus == 7 || $this->Estatus == 8 || $this->Estatus == 9 || $this->Estatus == 102 || $this->Estatus == 103 || $this->Estatus == 104)
            $this->Cell(9.5, .5, ' SOLICITUD DE PAGO DE ORDEN DE COMPRA ', 0, 0, 'C');
        else
            if (($this->Estatus == 10 || $this->Estatus == 11 || $this->Estatus == 12 || $this->Estatus == 13 || $this->Estatus == 14 || $this->Estatus == 15 || $this->Estatus == 16 || $this->Estatus == 17 || $this->Estatus == 18 || $this->Estatus == 111 || $this->Estatus == 112 || $this->Estatus == 113) && $this->idtipopago != 73)
                $this->Cell(9.5, .5, ' SOLICITUD DE PAGO RECURRENTE ', 0, 0, 'C');
            else
                if ($this->Estatus == 10 || $this->Estatus == 11 || $this->Estatus == 12 || $this->Estatus == 13 || $this->Estatus == 14 || $this->Estatus == 15 || $this->Estatus == 16 || $this->Estatus == 17 || $this->Estatus == 18 || $this->Estatus == 111 || $this->Estatus == 112 || $this->Estatus == 113)
                    $this->Cell(9.5, .5, ' SOLICITUD DE PAGO A PROVEEDOR ', 0, 0, 'C');
                else
                    if ($this->Estatus == 20 || $this->Estatus == 21 || $this->Estatus == 22 || $this->Estatus == 23 || $this->Estatus == 24 || $this->Estatus == 25 || $this->Estatus == 26 || $this->Estatus == 27 || $this->Estatus == 28 || $this->Estatus == 121 || $this->Estatus == 122 || $this->Estatus == 123)
                        $this->Cell(9.5, .5, ' SOLICITUD DE GASTOS A COMPROBAR ', 0, 0, 'C');
                    else
                        if ($this->Estatus == 40 || $this->Estatus == 41 || $this->Estatus == 42 || $this->Estatus == 43 || $this->Estatus == 44 || $this->Estatus == 45 || $this->Estatus == 46 || $this->Estatus == 47 || $this->Estatus == 48 || $this->Estatus == 141 || $this->Estatus == 142 || $this->Estatus == 143)
                            $this->Cell(9.5, .5, utf8_decode(' SOLICITUD DE REPOSICIÓN DE CAJA CHICA '), 0, 0, 'C');
                        else
                            if (($this->Estatus == 30 || $this->Estatus == 31 || $this->Estatus == 32 || $this->Estatus == 33 || $this->Estatus == 34 || $this->Estatus == 35 || $this->Estatus == 36 || $this->Estatus == 37 || $this->Estatus == 38 || $this->Estatus == 131 || $this->Estatus == 132 || $this->Estatus == 133) && $this->idtipopago == 6)
                                $this->Cell(9.5, .5, ' SOLICITUD DE REEMBOLSO DE GASTOS', 0, 0, 'C');

                            else
                                if (($this->Estatus == 30 || $this->Estatus == 31 || $this->Estatus == 32 || $this->Estatus == 33 || $this->Estatus == 34 || $this->Estatus == 35 || $this->Estatus == 36 || $this->Estatus == 37 || $this->Estatus == 38 || $this->Estatus == 131 || $this->Estatus == 132 || $this->Estatus == 133) && $this->idtipopago == 4)
                                    $this->Cell(9.5, .5, ' SOLICITUD DE GASTOS A COMPROBAR', 0, 0, 'C');


        $this->SetFont('Arial', 'B', 12);
        $this->Cell(3.5, .5, $this->Serie, 1, 0, 'C');
        $this->Cell(3.5, .5, $this->Folio, 1, 0, 'C');
        $this->Cell(19.5, .7, ' ', 0, 1, 'C');

    }

    function Footer($Solicita = "", $Autoriza = "", $DirAdmonFin = "", $DirGral = "")
    {
        $this->SetY(-2.8);
        $this->SetFont('Arial', 'BI', 7);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);


        $this->Cell(19.5, .4, utf8_decode('LAS FIRMAS REQUERIDAS, ESTÁN EN FUNCIÓN DEL IMPORTE Y TIPO DE GASTO DE LA SOLICITUD '), 0, 1, 'C');
        $this->Cell(19.5, .2, ' ', 0, 1, 'C');
        $this->SetFont('Arial', '', 6);
        $sql = "
			select
				fe.descripcion as encabezado,
				ff.descripcion as firmante,
				fe.idfirmas_encabezados as idfirmas_encabezados,
				ff.idfirmas_firmantes as idfirmas_firmantes
			from
			 	firmas_solicitudes as fj,
			 	firmas_encabezados as fe,
			 	firmas_firmantes as ff,
			 	solcheques as sch
			where
			 	sch.Serie='" . $this->Serie . "' and
			 	sch.Folio='" . $this->Folio . "' and
				fe.idfirmas_encabezados!=10 and
				fj.idsolcheque=sch.IdSolCheques and
			 	fe.idfirmas_encabezados=fj.idfirmas_encabezados and
				ff.idfirmas_firmantes=fj.idfirmas_firmantes
			order by fe.orden;";
        $link = conectarf2();
        $r = mysql_query($sql, $link);
        $no_firmas = mysql_affected_rows($link);
        if ($no_firmas > 0) {
            if ($no_firmas >= 4) {
                $blancos = false;
                $ancho = 19.6 / $no_firmas;
            } else {
                $blancos = true;
                $ancho = 4.9;
                $ancho_blancos = (19.6 - ($ancho * $no_firmas)) / 2;
            }
            if ($blancos) {
                $this->Cell($ancho_blancos, 1, ' ', 0, 0, 'C');
            }
            $y = $this->getY();
            $x = $this->getX();
            while ($v = mysql_fetch_array($r)) {
                $this->setY($y);
                $this->setX($x);
                $this->SetFont('Arial', '', 6);
                $this->CellFitScale($ancho, .3, $v["encabezado"], 1, 2, 'C', 1);
                $this->Cell($ancho, 1, '', 1, 2, 'C');
                $this->SetFont('Arial', '', 5);
                $this->CellFitScale($ancho, .3, $v["firmante"], 1, 2, 'C', 1);
                $x += $ancho;
            }
        } else {

            if ($this->Serie != 'CDM' && $this->Serie != 'PND' && $this->Serie != 'CNC') {
                $this->Cell(4.9, .3, 'SOLICITA', 1, 0, 'C', 1);
                $this->Cell(4.9, .3, 'AUTORIZA', 1, 0, 'C', 1);
                $this->Cell(4.9, .3, utf8_decode('DIRECCIÓN ADMINISTRACIÓN Y FINANZAS'), 1, 0, 'C', 1);
                $this->Cell(4.9, .3, utf8_decode('DIRECCIÓN GENERAL'), 1, 1, 'C', 1);


                $this->Cell(4.9, 1, ' ', 1, 0, 'C');
                $this->Cell(4.9, 1, ' ', 1, 0, 'C');
                $this->Cell(4.9, 1, ' ', 1, 0, 'C');
                $this->Cell(4.9, 1, ' ', 1, 1, 'C');

                $this->SetFont('Arial', '', 5);

                $this->Cell(4.9, .3, $this->Solicita, 1, 0, 'C'); //Variable Seg�n el Depto
                $this->Cell(4.9, .3, $this->Autoriza, 1, 0, 'C');
                $this->Cell(4.9, .3, $this->DirAdmonFin, 1, 0, 'C');
                $this->Cell(4.9, .3, $this->DirGral, 1, 0, 'C');

            } else
                if ($this->Serie == 'PND') {
                    if ($this->Solicita == utf8_decode('ÁNGEL SILVA ARCURI') || $this->Solicita == 'GILBERTO FRANCISCO VILLEGAS NAVARRO') {
                        $this->Cell(3.9, .3, 'SOLICITA', 1, 0, 'C', 1);
                        $this->Cell(3.9, .3, 'DIR DE PROM Y DES DE NEGOCIOS', 1, 0, 'C', 1);
                        $this->Cell(3.9, .3, 'AUTORIZA', 1, 0, 'C', 1);
                        $this->Cell(3.9, .3, utf8_decode('DIR. ADMINISTRACIÓN Y FINANZAS'), 1, 0, 'C', 1);
                        $this->Cell(3.9, .3, utf8_decode('DIRECCIÓN GENERAL'), 1, 1, 'C', 1);


                        $this->Cell(3.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(3.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(3.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(3.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(3.9, 1, ' ', 1, 1, 'C');

                        $this->SetFont('Arial', '', 5);

                        $this->Cell(3.9, .3, $this->Solicita, 1, 0, 'C'); //Variable Seg�n el Depto
                        $this->Cell(3.9, .3, utf8_decode('LIC. ARTURO OROZCO GÓMEZ'), 1, 0, 'C'); //Variable Seg�n el Depto
                        $this->Cell(3.9, .3, $this->Autoriza, 1, 0, 'C');
                        $this->Cell(3.9, .3, 'ING. LUIS H. ESPINOSA HERN�NDEZ', 1, 0, 'C');
                        $this->Cell(3.9, .3, $this->DirGral, 1, 0, 'C');


                    } else {
                        $this->Cell(4.9, .3, 'SOLICITA', 1, 0, 'C', 1);
                        $this->Cell(4.9, .3, 'AUTORIZA', 1, 0, 'C', 1);
                        $this->Cell(4.9, .3, utf8_decode('DIRECCIÓN ADMINISTRACIÓN Y FINANZAS'), 1, 0, 'C', 1);
                        $this->Cell(4.9, .3, utf8_decode('DIRECCIÓN GENERAL'), 1, 1, 'C', 1);


                        $this->Cell(4.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(4.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(4.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(4.9, 1, ' ', 1, 1, 'C');

                        $this->SetFont('Arial', '', 5);

                        $this->Cell(4.9, .3, $this->Solicita, 1, 0, 'C'); //Variable Seg�n el Depto
                        $this->Cell(4.9, .3, $this->Autoriza, 1, 0, 'C');
                        $this->Cell(4.9, .3, $this->DirAdmonFin, 1, 0, 'C');
                        $this->Cell(4.9, .3, $this->DirGral, 1, 0, 'C');
                    }
                } else
                    if ($this->Serie == 'CNC') {
                        $this->Cell(3.9, .3, 'SOLICITA', 1, 0, 'C', 1);
                        $this->Cell(3.9, .3, 'DIR DE PROM Y DES DE NEGOCIOS', 1, 0, 'C', 1);
                        $this->Cell(3.9, .3, 'AUTORIZA', 1, 0, 'C', 1);
                        $this->Cell(3.9, .3, utf8_decode('DIR. ADMINISTRACIÓN Y FINANZAS'), 1, 0, 'C', 1);
                        $this->Cell(3.9, .3, utf8_decode('DIRECCIÓN GENERAL'), 1, 1, 'C', 1);


                        $this->Cell(3.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(3.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(3.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(3.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(3.9, 1, ' ', 1, 1, 'C');
                        $this->SetFont('Arial', '', 5);
                        $this->Cell(3.9, .3, $this->Solicita, 1, 0, 'C'); //Variable Seg�n el Depto
                        $this->Cell(3.9, .3, 'LIC. ARTURO OROZCO GÓMEZ', 1, 0, 'C'); //Variable Seg�n el Depto
                        $this->Cell(3.9, .3, $this->Autoriza, 1, 0, 'C');
                        $this->Cell(3.9, .3, 'ING. LUIS H. ESPINOSA HERN�NDEZ', 1, 0, 'C');
                        $this->Cell(3.9, .3, $this->DirGral, 1, 0, 'C');

                    } else {
                        $this->Cell(2.4, 1, ' ', 0, 0, 'C');
                        $this->Cell(4.9, .3, 'SOLICITA', 1, 0, 'C', 1);
                        $this->Cell(4.9, .3, 'AUTORIZA PENINSULAR', 1, 0, 'C', 1);
                        $this->Cell(4.9, .3, 'AUTORIZA FCC', 1, 1, 'C', 1);
                        $this->Cell(2.4, 1, ' ', 0, 0, 'C');
                        $this->Cell(4.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(4.9, 1, ' ', 1, 0, 'C');
                        $this->Cell(4.9, 1, ' ', 1, 1, 'C');
                        $this->SetFont('Arial', '', 5);
                        $this->Cell(2.4, 1, ' ', 0, 0, 'C');
                        $this->Cell(4.9, .3, $this->Solicita, 1, 0, 'C'); //Variable Seg�n el Depto
                        $this->Cell(4.9, .3, $this->Autoriza, 1, 0, 'C');
                        $this->Cell(4.9, .3, $this->DirAdmonFin, 1, 1, 'C');
                    }
        }

        $this->SetY(-0.5);
        $this->SetFont('Arial', 'I', 6);
        $this->Cell(10, .4, 'V.22062012', 0, 0, 'L');
        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(10, .4, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
    }
}

$pdf = new PDF('P', 'cm', 'Letter', $Solicita, $Autoriza, $DirAdmonFin, $DirGral, $serie, $folio, $Estatus, $idtipopago);
$pdf->SetTopMargin(.1);
$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 3);
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(204, 204, 204);

if (
    ($Estatus == 5) || ($Estatus == 6) ||
    ($Estatus == 14) || ($Estatus == 15) ||
    ($Estatus == 24) || ($Estatus == 25) ||
    ($Estatus == 34) || ($Estatus == 35) ||
    ($Estatus == 44) || ($Estatus == 45) ||
    ($Estatus == 26)
) {
    $link = conectarf2();
    $sql = "SELECT pagos.Liberado, concat(dayofmonth(pagos.FechaPago),'-',month(pagos.FechaPago),'-',year(pagos.FechaPago)) as FechaPago FROM controlrec.pagos,controlrec.partidaspagos WHERE controlrec.partidaspagos.IdPago = controlrec.pagos.IdPago AND controlrec.partidaspagos.IdSolCheque =" . $idsolcheque . ";";
    $result = mysql_query($sql, $link);
    mysql_close($link);

    while ($row = mysql_fetch_array($result)) {
        $FechaPago = $row["FechaPago"];
        $liberado = $row["Liberado"];
    }
    if ($liberado == 1) {

        for ($b = 0; $b < 60; $b++) {
            for ($a = 0; $a < 25; $a++) {
                $pdf->RotatedText($a, $b, 'PAGADO POR TESORERIA EL ' . $FechaPago, 45);
                $a = $a + 3;
            }
        }
    }


}

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(204, 204, 204);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(19.5, .5, $empresa, 1, 1, 'C', 1);

$pdf->Cell(19.5, .2, ' ', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 7);

$pdf->Cell(1.5, .3, 'FECHA', 1, 0, 'C', 1);
$pdf->CellFitScale(2, .3, 'FORMA DE PAGO', 1, 0, 'C', 1);
$pdf->CellFitScale(1.5, .3, 'VENCIMIENTO', 1, 0, 'C', 1);
if (($pdf->idtipopago == 73 || $pdf->idtipopago == 6 || $pdf->idtipopago == 5) && $relacion != "") {
    $pdf->Cell(1.5, .3, utf8_decode('RELACIÓN'), 1, 0, 'C', 1);
    $pdf->Cell($pdf->separacion - 1.5, .3, ' ', 0, 0, 'C');
} else {
    $pdf->Cell($pdf->separacion, .3, ' ', 0, 0, 'C');
}
if ($pdf->importe != '0.00') {
    $pdf->Cell(2.5, .3, 'IMPORTE', 1, 0, 'C', 1);
}

if ($pdf->iva != '0.00') {
    $pdf->Cell(2, .3, 'IVA', 1, 0, 'C', 1);
}

if ($pdf->retenciones != '0.00') {
    $pdf->Cell(2, .3, 'RETENCIONES', 1, 0, 'C', 1);
}

if ($pdf->otros != '0.00') {
    $pdf->Cell(2, .3, 'OTROS IMP.', 1, 0, 'C', 1);
}


if ($pdf->total != '0.00') {
    $pdf->Cell(2.5, .3, 'TOTAL', 1, 0, 'C', 1);
}

$pdf->Cell(1.8, .3, 'MONEDA', 1, 1, 'C', 1);

$pdf->SetFont('Arial', '', 7);
$pdf->Cell(1.5, .3, $fecha, 1, 0, 'C');
$pdf->Cell(2, .3, $forma_pago, 1, 0, 'C');
$pdf->Cell(1.5, .3, $vencimiento, 1, 0, 'C');

if (($pdf->idtipopago == 73 || $pdf->idtipopago == 6 || $pdf->idtipopago == 5) && $relacion != "") {
    $pdf->Cell(1.5, .3, $relacion, 1, 0, 'C');
    $pdf->Cell($pdf->separacion - 1.5, .3, ' ', 0, 0, 'C');
} else {
    $pdf->Cell($pdf->separacion, .3, ' ', 0, 0, 'C');
}


if ($pdf->importe != '0.00') {
    $pdf->Cell(2.5, .3, $pdf->importe, 1, 0, 'C');
}

if ($pdf->iva != '0.00') {
    $pdf->Cell(2, .3, $pdf->iva, 1, 0, 'C');
}

if ($pdf->retenciones != '0.00') {
    $pdf->Cell(2, .3, $pdf->retenciones, 1, 0, 'C');
}

if ($pdf->otros != '0.00') {
    $pdf->Cell(2, .3, $pdf->otros, 1, 0, 'C');
}


if ($pdf->total != '0.00') {
    $pdf->Cell(2.5, .3, $pdf->total, 1, 0, 'C');
}

$pdf->CellFitScale(1.8, .3, $moneda, 1, 1, 'C');


$pdf->SetFont('Arial', '', 9);

$pdf->Cell(19.5, .2, ' ', 0, 1, 'C');
$pdf->Cell(19.5, .4, 'A FAVOR DE', 1, 1, 'C', 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->MultiCell(19.5, .4, $proveedor, 1, 'C', 0);

$pdf->SetFont('Arial', '', 7);

$pdf->Cell(19.5, .3, ' ', 0, 1, 'C');
$pdf->Cell(19.5, .3, 'CONCEPTO DEL PAGO', 1, 1, 'C', 1);

$pdf->SetFont('Arial', 'B', 8);

$pdf->MultiCell(19.5, .4, $concepto, 1, 'J');

---->
$pdf->SetFont('Arial', 'B', 8);

$pdf->Cell(19.5, .4, ' ', 0, 1, 'C');
$pdf->Cell(6.5, .4, 'TIPO DE PAGO', 1, 0, 'C', 1);
$pdf->Cell(5, .4, 'INTRUCCIONES DE ENTREGA', 1, 0, 'C', 1);
$pdf->Cell(8, .4, 'CUENTA BANCARIA', 1, 1, 'C', 1);

$pdf->SetFont('Arial', '', 8);

$pdf->Cell(6.5, .4, $tipopago, 1, 0, 'C');
$pdf->Cell(5, .4, $entrega, 1, 0, 'C');
$pdf->Cell(8, .4, $CuentaProv, 1, 1, 'C');


$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(19.5, .4, ' ', 0, 1, 'L');
$pdf->Cell(19.5, .4, utf8_decode('APLICACIÓN DE SEGMENTOS DE NEGOCIO (Debe ser Llenada por el Solicitante)'), 0, 1, 'L');


if ($pdf->equal) {
    $linkcc = conectarf2();
    $sqlcc = "SELECT if(CC.Facturable='Y','F','-')as Facturable,if(SNC.NumeroSegmento is null,' - ',SNC.NumeroSegmento) as NS, C.Descripcion as IdCC, T.Descripcion as IdTipoGasto,count(CC.IdCCSolCheques) as TotalCC, sum(CC.Importe) as Importe
FROM ccsolcheques as CC, centroscosto as C left join segmentos_negocio_contabilidad as SNC on(C.NoSN=SNC.NumeroSegmento) ,
 tiposgasto as T where CC.idSolcheque=$idsolcheque and CC.IdCC=C.IdCC and CC.IdTipoGasto=T.IdTipoGasto group by C.Descripcion,T.Descripcion,CC.Facturable;";


    $resultcc = mysql_query($sqlcc, $linkcc);
    mysql_close($linkcc);

    $conter = 1;
    while ($rowcc = mysql_fetch_array($resultcc)) {
        if ($rowcc["TotalCC"] == 1) {
            $tg[$conter] = $rowcc["IdTipoGasto"];
        } else {
            $tg[$conter] = $rowcc["IdTipoGasto"] . " (" . $rowcc["TotalCC"] . ")";
        }


        $cc[$conter] = $rowcc["IdCC"];
        $fac[$conter] = $rowcc["Facturable"];
        $imp[$conter] = $rowcc["Importe"];
        $ns[$conter] = $rowcc["NS"];
        $conter = $conter + 1;
    }

    $pdf->SetFont('Arial', 'B', 8);


    $pdf->Cell(8.7, .3, 'SEGMENTO DE NEGOCIO', 1, 0, 'C', 1);
    $pdf->Cell(7.8, .3, 'TIPO DE GASTO', 1, 0, 'C', 1);
    $pdf->Cell(3, .3, 'TOTAL', 1, 1, 'C', 1);

    $pdf->SetFont('Arial', '', 8);
    for ($a = 1; $a < $conter; $a++) {
        $pdf->CellFitScale(0.7, .3, $ns[$a], 1, 0, 'C');
        $pdf->CellFitScale(0.7, .3, $fac[$a], 1, 0, 'C');
        $pdf->CellFitScale(7.3, .3, $cc[$a], 1, 0, 'L');
        $pdf->CellFitScale(7.8, .3, $tg[$a], 1, 0, 'L');
        $pdf->CellFitScale(3, .3, number_format($imp[$a], 2, '.', ','), 1, 1, 'R');
    }

    $resto = 50 - $conter;

    for ($b = 1; $b < $resto; $b++) {
        $pdf->Cell(0.7, .3, ' ', 1, 0, 'C');
        $pdf->Cell(0.7, .3, ' ', 1, 0, 'C');
        $pdf->Cell(7.3, .3, ' ', 1, 0, 'L');
        $pdf->Cell(7.8, .3, ' ', 1, 0, 'L');
        $pdf->Cell(3, .3, ' ', 1, 1, 'R');
    }

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(16.5, .3, "SUMAS:", 0, 0, 'R');
    $pdf->Cell(3, .3, $pdf->total, 1, 1, 'R', 1);
} else {
    $linkcc = conectarf2();


    $sqlcc = "
	SELECT
	if(ccdoctos.Facturable='Y','F','-')as Facturable,
	   if(centroscosto.NoSN is null,' - ',centroscosto.NoSN) as NS,
	   count(centroscosto.Descripcion) as TotalCC,
       centroscosto.Descripcion AS `SN`,
       tiposgasto.Descripcion AS `TG`,
       documentos.Importe AS `ImporteDocumento`,
       documentos.Retenciones AS `RetencionesDocumento`,
	documentos.OtrosImpuestos AS `OtrosImpuestosDocumento`,
       documentos.TasaIVA,
       documentos.IVA AS `IVADocumento`,
       documentos.Total AS `TotalDocumento`,
       FORMAT(SUM(ccdoctos.Importe),3) AS `ImporteSN`,
       FORMAT(SUM(ccdoctos.IVA),3) as `IVASN`,
       FORMAT(SUM(ccdoctos.Retenciones),3) as `RetencionesSN`,
       FORMAT(SUM(ccdoctos.OtrosImpuestos),3) as `OtrosImpuestos`,
       FORMAT(SUM(ccdoctos.Total),3) as `TotalSN`
  FROM    (   (   (   (   controlrec.ccdoctos ccdoctos
                       INNER JOIN
                          controlrec.centroscosto centroscosto
                       ON (ccdoctos.IdCC = centroscosto.IdCC))
                   INNER JOIN
                      controlrec.documentos documentos
                   ON (ccdoctos.IdDocto = documentos.IdDocto))
               INNER JOIN
                  controlrec.solchequesdoctos solchequesdoctos
               ON (solchequesdoctos.IdDocto = documentos.IdDocto))
           INNER JOIN
              controlrec.solcheques solcheques
           ON (solchequesdoctos.IdSolCheque = solcheques.IdSolCheques))
       INNER JOIN
          controlrec.tiposgasto tiposgasto
       ON (ccdoctos.IdTipoGasto = tiposgasto.IdTipoGasto)
	   left join segmentos_negocio_contabilidad as SNC on(centroscosto.NoSN=SNC.NumeroSegmento)
 WHERE (solcheques.IdSolCheques = " . $idsolcheque . ") GROUP BY  centroscosto.Descripcion,tiposgasto.Descripcion,ccdoctos.Facturable ;
			";


    $resultcc = mysql_query($sqlcc, $linkcc) or die(mysql_error($linkcc));
    mysql_close($linkcc);

    $conter = 1;

    $pdf->SetFont('Arial', 'B', 7);

    $w = 4.75;
    if ($pdf->iva == 0.00) $w += 1;
    if ($pdf->retenciones == 0.00) $w += 1;
    if ($pdf->importe == 0.00) $w += 1;
    if ($pdf->otros == 0.00) $w += 1;


    $pdf->Cell($w + 0.7, .3, 'SEGMENTO DE NEGOCIO', 1, 0, 'C', 1);
    $pdf->Cell($w - 0.7, .3, 'TIPO DE GASTO', 1, 0, 'C', 1);
    if ($pdf->importe != 0.00)
        $pdf->Cell(2, .3, 'IMPORTE', 1, 0, 'C', 1);
    if ($pdf->iva != 0.00)
        $pdf->Cell(2, .3, 'IVA', 1, 0, 'C', 1);
    if ($pdf->retenciones != 0.00)
        $pdf->Cell(2, .3, 'RETENCIONES', 1, 0, 'C', 1);
    if ($pdf->otros != 0.00)
        $pdf->Cell(2, .3, 'OTROS IMP.', 1, 0, 'C', 1);

    $pdf->Cell(2, .3, 'TOTAL', 1, 1, 'C', 1);..----->
    $pdf->SetFont('Arial', '', 7);
    while ($rowcc = mysql_fetch_array($resultcc)) {

        $pdf->CellFitScale(0.7, .3, $rowcc["NS"], 1, 0, 'C');
        $pdf->CellFitScale(0.7, .3, $rowcc["Facturable"], 1, 0, 'C');
        if ($rowcc["TotalCC"] == 1) {
            $pdf->CellFitScale($w - 0.7, .3, $rowcc["SN"], 1, 0, 'L');
        } else {
            $pdf->CellFitScale($w - 0.7, .3, $rowcc["SN"] . " (" . $rowcc["TotalCC"] . ")", 1, 0, 'L');
        }

        $pdf->CellFitScale($w - 0.7, .3, $rowcc["TG"], 1, 0, 'L');
        if ($pdf->importe != 0.00)
            $pdf->Cell(2, .3, $rowcc["ImporteSN"], 1, 0, 'R');
        if ($pdf->iva != 0.00)
            $pdf->Cell(2, .3, $rowcc["IVASN"], 1, 0, 'R');
        if ($pdf->retenciones != 0.00)
            $pdf->Cell(2, .3, $rowcc["RetencionesSN"], 1, 0, 'R');
        if ($pdf->otros != 0.00)
            $pdf->Cell(2, .3, $rowcc["OtrosImpuestos"], 1, 0, 'R');

        $pdf->Cell(2, .3, $rowcc["TotalSN"], 1, 1, 'R');
        $conter = $conter + 1;

    }
    if ($uids_txt == "" &&

        ($pdf->Estatus == 1 || $pdf->Estatus == 2 || $pdf->Estatus == 3 || $pdf->Estatus == 4 || $pdf->Estatus == 5 || $pdf->Estatus == 6 || $pdf->Estatus == 7 || $pdf->Estatus == 8 || $pdf->Estatus == 9 || $pdf->Estatus == 102 || $pdf->Estatus == 103 || $pdf->Estatus == 104 || $pdf->Estatus == 10 || $pdf->Estatus == 11 || $pdf->Estatus == 12 || $pdf->Estatus == 13 || $pdf->Estatus == 14 || $pdf->Estatus == 15 || $pdf->Estatus == 16 || $pdf->Estatus == 17 || $pdf->Estatus == 18 || $pdf->Estatus == 111 || $pdf->Estatus == 112 || $pdf->Estatus == 113 && $pdf->idtipopago != 73)

    ) {
        $resto = 50 - $conter - (count($uuids) - 1);
    } else {
        $resto = 50 - $conter - 1;
    }


    for ($b = 1; $b < $resto; $b++) {
        $pdf->Cell(0.7, .3, ' ', 1, 0, 'C');
        $pdf->Cell(0.7, .3, ' ', 1, 0, 'C');
        $pdf->Cell($w - 0.7, .3, ' ', 1, 0, 'L');
        $pdf->Cell($w - 0.7, .3, ' ', 1, 0, 'L');
        if ($pdf->importe != 0.00)
            $pdf->Cell(2, .3, ' ', 1, 0, 'R');
        if ($pdf->iva != 0.00)
            $pdf->Cell(2, .3, ' ', 1, 0, 'R');
        if ($pdf->retenciones != 0.00)
            $pdf->Cell(2, .3, ' ', 1, 0, 'R');
        if ($pdf->otros != 0.00)
            $pdf->Cell(2, .3, ' ', 1, 0, 'R');

        $pdf->Cell(2, .3, ' ', 1, 1, 'R');
    }
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($w, .3, '', 0, 0, 'L');
    $pdf->Cell($w, .3, "SUMAS:", 0, 0, 'R');
    if ($pdf->importe != 0.00)
        $pdf->Cell(2, .3, $pdf->importe, 1, 0, 'R', 1);
    if ($pdf->iva != 0.00)
        $pdf->Cell(2, .3, $pdf->iva, 1, 0, 'R', 1);
    if ($pdf->retenciones != 0.00)
        $pdf->Cell(2, .3, $pdf->retenciones, 1, 0, 'R', 1);
    if ($pdf->otros != 0.00)
        $pdf->Cell(2, .3, $pdf->otros, 1, 0, 'R', 1);

    $pdf->Cell(2, .3, $pdf->total, 1, 1, 'R', 1);

}


$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(19.5, .3, 'Documento de Soporte Recibido', 0, 1, 'L');
$pdf->SetFont('Arial', '', 4);
$pdf->Cell(2.2, .3, utf8_decode('REQUISICIÓN DEL USUARIO'), 'LRT', 0, 'C', 1);
$pdf->Cell(2.2, .3, 'SOLICITUD DE COMPRA', 'LRT', 0, 'C', 1);
$pdf->Cell(2.2, .3, utf8_decode('OFICIO DE AUTORIZACIÓN '), 'LRT', 0, 'C', 1);
$pdf->Cell(2.2, .3, 'COMPARATIVA DE ', 'LRT', 0, 'C', 1);
$pdf->Cell(2.2, .3, 'ORDEN DE COMPRA', 'LRT', 0, 'C', 1);
$pdf->Cell(2.2, .3, utf8_decode('ENTRADA DE ALMACÉN /'), 'LRT', 0, 'C', 1);
$pdf->Cell(2.1, .3, 'ORIGINAL', 'LRT', 0, 'C', 1);
$pdf->Cell(2.1, .3, 'COPIA', 'LRT', 0, 'C', 1);
$pdf->Cell(2.1, .3, 'OTRO', 'LRT', 1, 'C', 1);

$pdf->Cell(2.2, .3, '', 'LRB', 0, 'C', 1);
$pdf->Cell(2.2, .3, '', 'LRB', 0, 'C', 1);
$pdf->Cell(2.2, .3, 'DE COMPRA DE AF', 'LRB', 0, 'C', 1);
$pdf->Cell(2.2, .3, 'COTIZACIONES', 'LRB', 0, 'C', 1);
$pdf->Cell(2.2, .3, '', 'LRB', 0, 'C', 1);
$pdf->Cell(2.2, .3, utf8_decode('RECEPCIÓN DEL SERVICIO'), 'LRB', 0, 'C', 1);
$pdf->Cell(2.1, .3, 'FACTURA/COMPROBANTE', 'LRB', 0, 'C', 1);
$pdf->Cell(2.1, .3, 'FACTURA/COMPROBANTE', 'LRB', 0, 'C', 1);
$pdf->Cell(2.1, .3, '', 'LRB', 1, 'C', 1);


$pdf->Cell(1.1, 0.3, 'SI', 1, 0, 'C');
$pdf->Cell(1.1, 0.3, 'NO', 1, 0, 'C');

$pdf->Cell(1.1, 0.3, 'SI', 1, 0, 'C');
$pdf->Cell(1.1, 0.3, 'NO', 1, 0, 'C');

$pdf->Cell(1.1, 0.3, 'SI', 1, 0, 'C');
$pdf->Cell(1.1, 0.3, 'NO', 1, 0, 'C');

$pdf->Cell(1.1, 0.3, 'SI', 1, 0, 'C');
$pdf->Cell(1.1, 0.3, 'NO', 1, 0, 'C');

$pdf->Cell(1.1, 0.3, 'SI', 1, 0, 'C');
$pdf->Cell(1.1, 0.3, 'NO', 1, 0, 'C');

$pdf->Cell(1.1, 0.3, 'SI', 1, 0, 'C');
$pdf->Cell(1.1, 0.3, 'NO', 1, 0, 'C');

$pdf->Cell(1, 0.3, 'SI', 1, 0, 'C');
$pdf->Cell(1.1, 0.3, 'NO', 1, 0, 'C');

$pdf->Cell(1.1, 0.3, 'SI', 1, 0, 'C');
$pdf->Cell(1, 0.3, 'NO', 1, 0, 'C');

$pdf->Cell(1, 0.3, 'SI', 1, 0, 'C');
$pdf->Cell(1.1, 0.3, 'NO', 1, 1, 'C');

//
$pdf->SetFont('Arial', '', 7);
$pdf->SetWidths(array(1.1, 1.1, 1.1, 1.1, 1.1, 1.1, 1.1, 1.1, 1.1, 1.1, 1.1, 1.1, 1, 1.1, 1.1, 1, 1, 1.1));

if ($uids_txt == "") {
    $pdf->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
    $pdf->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0',));
} else {
    $pdf->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '0,0,0', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));
    $pdf->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '255,255,255', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0',));
}


$pdf->SetDrawColor(117, 117, 117);
$pdf->SetHeights(array(0.4));
$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
$pdf->Row(array("", "", "", "", "", "", "", "", "", "", "", "", $uids_txt, "", "", "", "", ""));
$pdf->SetFont('Arial', '', 5);
//

--->
$pdf->Cell(19.5, .1, ' ', 0, 1, 'C');

if ($uids_txt == "" &&

    ($pdf->Estatus == 1 || $pdf->Estatus == 2 || $pdf->Estatus == 3 || $pdf->Estatus == 4 || $pdf->Estatus == 5 || $pdf->Estatus == 6 || $pdf->Estatus == 7 || $pdf->Estatus == 8 || $pdf->Estatus == 9 || $pdf->Estatus == 102 || $pdf->Estatus == 103 || $pdf->Estatus == 104 || $pdf->Estatus == 10 || $pdf->Estatus == 11 || $pdf->Estatus == 12 || $pdf->Estatus == 13 || $pdf->Estatus == 14 || $pdf->Estatus == 15 || $pdf->Estatus == 16 || $pdf->Estatus == 17 || $pdf->Estatus == 18 || $pdf->Estatus == 111 || $pdf->Estatus == 112 || $pdf->Estatus == 113 && $pdf->idtipopago != 73)

) {

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(255, 0, 0);
    $pdf->SetDrawColor(255, 0, 0);

    $pdf->Cell(15.5, .5, utf8_decode('SE REQUIERE AUTORIZACIÓN ADICIONAL PARA DAR TRÁMITE AL PAGO SIN CFDI:'), 0, 0, 'L');

    $y = $pdf->getY();
    $x = $pdf->getX();

    $pdf->setY($y);
    $pdf->setX($x);

    $pdf->SetFont('Arial', '', 5);
    $pdf->CellFitScale(4, .3, "AUTORIZA", 1, 2, 'C', 1);
    $pdf->Cell(4, .7, '', 1, 2, 'C');
    $pdf->SetFont('Arial', '', 5);
    $pdf->CellFitScale(4, .3, utf8_decode("C.P. PRIMITIVO AYALA SÁNCHEZ"), 1, 2, 'C', 1);
}

$pdf->SetTextColor(0, 0, 0);


$pdf->Output();

}
