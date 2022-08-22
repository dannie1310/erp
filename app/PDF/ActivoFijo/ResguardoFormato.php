<?php


namespace App\PDF\ActivoFijo;


use App\Models\ACTIVO_FIJO\Resguardo;
use Ghidev\Fpdf\Rotation;

class ResguardoFormato extends Rotation
{
    protected $resguardo;
    protected $partidas;
    protected $encola = '';
    protected $y_c = 0;
    protected $x_c = 0;
    protected $x_p = 0;

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 279;
    const A4_WIDTH = 216;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    private $dias_semana = array(  7 => "DOMINGO"
								 , 1 => "LUNES"
								 , 2 => "MARTES"
								 , 3 => "MIÉRCOLES"
								 , 4 => "JUEVES"
								 , 5 => "VIERNES"
								 , 6 => "SÁBADO"
								 , );

	private $meses = array(  1 => "ENERO"
						   , 2 => "FEBRERO"
						   , 3 => "MARZO"
						   , 4 => "ABRIL"
						   , 5 => "MAYO"
						   , 6 => "JUNIO"
						   , 7 => "JULIO"
						   , 8 => "AGOSTO"
						   , 9 => "SEPTIEMBRE"
						   , 10 => "OCTUBRE"
						   , 11 => "NOVIEMBRE"
						   , 12 => "DICIEMBRE");

    public function __construct(Resguardo $resguardo)
    {
        parent::__construct('P', 'cm', 'Letter');
        $this->resguardo = $resguardo;
        $this->partidas = $this->resguardo->partidasEmpresa;
        $this->WidthTotal = $this->GetPageWidth();
    }

    function logo(){
        $file = public_path('img/ghi-logo.png');
        $data = unpack("H*", file_get_contents($file));
        $data = bin2hex($data[1]);
        $data = pack('H*', hex2bin($data));
        $file = public_path('/img/logo_temp.png');
        if (file_put_contents($file, $data) !== false) {
            list($width, $height) = $this->resizeToFit($file);
            $this->Image($file, -0.5, 1, $width, $height);
            unlink($file);
        }
    }
    function Header(){
        $this->logo();

        // TITULO DE TIPO DE RESGUARDO
        $this->SetFont('Arial', 'B', 90);
		$this->SetTextColor(200, 200, 200);
        $this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial', 'BU', 11);		// Font: Arial Bold Underline Size:11
		$this->Cell(5, 5, '', 0, 0, 'C');
		$this->Cell(8.3,0.9, utf8_decode('RESGUARDO DE '), 0, 2, 'C');
		$this->Cell(8.3,0.1, utf8_decode($this->resguardo->resguardoElemento->Titulo), 0, 0, 'C');

        //AREA
		$this->Ln(0.5);
        $this->Cell(5, 5, '', 0, 0, 'C');
		$this->SetFont('Arial', 'B', 6);
		$this->SetTextColor(150, 150, 150);
		$this->Cell(8.3, 0.4, utf8_decode($this->resguardo->resguardoElemento->Area), 0, 0, 'C');

        $fecha = $this->resguardo->FechaCreo;
        $expl = explode("-", $fecha);
        $dayofweek = date('w', strtotime($fecha));
		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial', 'BI', 7);
		$this->SetY($this->GetY()+0.9);
		$this->Cell(19.5, 0, utf8_decode($this->dias_semana[(int)$dayofweek]." ".(int)$expl[2]." DE ".$this->meses[(int)$expl[1]]." DEL AÑO ".$expl[0]), 0, 1, 'R',0);
		$this->SetFont('Arial', 'B', 9);

        if($this->encola == 'caracteristicas'){
            $this->SetRadius(array(0, 0));
            $this->SetFont('Arial', '', 5);
            $this->SetAligns(array('L', 'L'));
            $this->SetWidths(array(4.0, 5.5));
            $this->SetFills(array('205,205,205', '255,255,255'));
            $this->SetStyles(array('DF', 'DF'));
            $this->SetTextColors(array('0,0,0', '0,0,0'));
            $this->SetHeights(array(.3, .3));

            $this->SetXY($this->x_p, $this->GetY() + 0.7);
            $this->y_c = $this->GetY();
        }

        if($this->encola == 'detalles'){
            $this->SetY($this->GetY() + 0.7);
        }
        if($this->encola == 'TipoEquipo'){
            $this->SetXY($this->x_p, $this->GetY() + 0.7);
            $this->y_c = $this->GetY();
        }
        if($this->encola == 'CodigoEquipo'){
            // dd($this->y_c , $this->GetY());
            $this->SetY($this->GetY() + 0.75);
            $this->y_c = $this->GetY()-0.5;
        }
    }

    function asignacion(){
        $this->SetY($this->GetY()+0.7);
		$this->SetRadius(array(0.3));
		$this->SetWidths(array(19.5));
		$this->SetFills(array('221,221,221'));
		$this->SetAligns(array('C'));
		$this->SetHeights(array(0.5));
		$this->SetRounds(array('12'));
		$this->SetFont('Arial','b',12);
		$this->Row(array("ASIGNADO A"));

		$this->SetFont('Arial','b',15);
		$this->SetWidths(array(19.5));
		$this->SetHeights(array(0.7));
		$this->SetFills(array('255,255,255'));
		$this->SetRounds(array('34'));
		$this->Row(array(utf8_decode($this->resguardo->usuario->nombreCompleto)));

        $this->Ln(1);
		$this->SetFont('Arial', 'b', 9);
		$this->SetAligns(array('L', 'L'));
		$this->SetWidths(array(6.2, 13.3));
		$this->SetHeights(array(.5, .5));
		$this->SetFills(array('205, 205, 205', '255, 255, 255'));
		$this->SetStyles(array('DF', 'DF'));

		$this->SetRounds(array('1', '2'));
		$this->SetRadius(array(.3, .3));
		$this->Row(array(utf8_decode("EMPRESA A LA QUE PERTENECE:"), utf8_decode($this->resguardo->empresa->empresa)));

		$this->SetRadius(array(0, 0));
		$this->Row(array(utf8_decode("DIRECCIÓN DE LA EMPRESA:"), utf8_decode($this->resguardo->DireccionEmpresa)));
		$this->Row(array(utf8_decode("DEPARTAMENTO"), utf8_decode($this->resguardo->departamento->departamento)));
		$this->Row(array(utf8_decode("PROYECTO/OFICINA:"), utf8_decode($this->resguardo->ubicacion->ubicacion)));

		$this->SetRadius(array(.3, .3));
		$this->SetRounds(array('4', '3'));
		$this->Row(array(utf8_decode("DIRECCIÓN DEL PROYECTO/OFICINA:"), utf8_decode($this->resguardo->ubicacion->ubicacion_direccion)));
    }

    function detalles(){
        $this->encola = 'detalles';
        $this->SetY($this->GetY() + 0.5);
		$this->SetRounds(array('12'));
		$this->SetFont('Arial', 'b', 9);
		$this->SetAligns(array('L'));
		$this->SetWidths(array(19.5));
		$this->SetFills(array('0, 0, 0'));
		$this->SetStyles(array('DF'));
		$this->SetTextColors(array('255, 255, 255'));
		$this->SetRadius(array(.1));
		$this->Row(array(utf8_decode("DETALLE DE LO ASIGNADO")));

		$this->SetHeights(array(.3, .3, .3, .3, .3, .3, .3, .3));
		$this->SetWidths(array(.5, 2.0, 3.5, 2.5, 2.5, 3.0, 1.5, 4.0));
		$this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
		$this->SetTextColors(array('0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0'));
		$this->SetFills(array('205,205,205', '205,205,205', '205,205,205', '205,205,205', '205,205,205', '205,205,205', '205,205,205', '205,205,205'));
		$this->SetFont('Arial', 'b', 5);
		$this->SetRadius(array(0, 0, 0, 0, 0, 0, 0, 0));
		$this->Row(array("#", "IDENTIFICADOR", "EQUIPO", "MARCA", "MODELO", "NO. SERIE", "ESTADO", "OBSERVACIONES"));
		$this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));

        foreach($this->partidas as $k => $partida){
            if($partida->es_ultima_partida)
            {
                if($this->resguardo->GrupoEquipo == 5){
                    $this->SetFills(array('220,220,220', '220,220,220', '220,220,220', '220,220,220', '220,220,220', '220,220,220', '220,220,220', '220,220,220'));
                }else{
                    $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '220,220,220'));
                }
            }else{
                $this->SetFills(array('255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255'));

            }
            $this->Row(array($k+1, $partida->CodigoEquipo, utf8_decode($partida->TipoEquipo), utf8_decode($partida->Marca), $partida->Modelo, $partida->SerieEquipo, utf8_decode($partida->estadoPartida->descripcion), utf8_decode($partida->Observaciones)));
        }
    }

    function caracteristicas(){
        $this->SetRounds(array('1234'));
        $this->SetRadius(array(.1));
		$this->SetFont('Arial', 'b', 9);
		$this->SetAligns(array('L'));
		$this->SetWidths(array(19.5));
		$this->SetFills(array('0,0,0'));
		$this->SetStyles(array('DF'));
		$this->SetTextColors(array('255,255,255'));
		$this->SetHeights(array(.5));
		$this->SetY($this->GetY() + 0.5);
		$this->Row(array(utf8_decode("CARACTERÍSTICAS DEL EQUIPO ASIGNADO")));
		$this->SetY($this->GetY() + 0.2);
        $this->SetRadius(array(.1));
        $this->SetRounds(array('12'));
        $this->SetFont('Arial','b',8);
        $this->SetAligns(array('C'));
        $this->SetWidths(array(9.5, 0.5, 9.5));
        $this->SetHeights(array(.5));
        $this->SetFills(array('205,205,205'));
        $this->SetTextColors(array('0,0,0'));

        $cantP = count($this->partidas);
        $part = $this->partidas;
        foreach($part as $p){
            $p->push($p->partidaCaracteristicas);
        }
        $partidas = $part->toArray();
        $caract1 = 0;

        for($i = 0; $i < $cantP; $i++){
            $j = 0;

            $this->SetRadius(array(.1));
            $this->SetRounds(array('12'));
            $this->SetFont('Arial','b',8);
            $this->SetAligns(array('C'));
            $this->SetWidths(array(9.5, 0.5, 9.5));
            $this->SetHeights(array(.5));
            $this->SetFills(array('205,205,205'));
            $this->SetTextColors(array('0,0,0'));

            $caract1 = count($partidas[$i]['partida_caracteristicas']);
            if(array_key_exists($i+1, $partidas) && count($partidas[$i+1]['partida_caracteristicas']) > $caract1){
                $caract1 = count($partidas[$i+1]['partida_caracteristicas']);
            }
            $this->x_c = $this->GetX();
            $this->y_c = $this->GetY();
            $this->x_p = $this->GetX();
            $this->encola = 'TipoEquipo';
            $this->Row(array(utf8_decode($partidas[$i]['TipoEquipo'])));

            if(array_key_exists($i+1, $partidas)){
                $this->x_p = $this->x_c+10;
                $this->SetXY(($this->x_c + 10),$this->y_c);
                $this->SetFont('Arial','b',8);
                $this->Row(array(utf8_decode($partidas[$i+1]['TipoEquipo'])));

            }
            $this->encola = 'CodigoEquipo';

            $this->SetFont('code39', '', 7);
			$this->Cell(9.5, 2, '*'.$partidas[$i]['CodigoEquipo'].'*', 0, 0, 'C');
            $this->RoundedRect($this->x_c, $this->y_c+0.5, 9.5, 1.5, 1, '', 'D');
            if(array_key_exists($i+1, $partidas)){
                $this->SetFont('code39', '', 7);
                $this->x_p = $this->x_c+10;
                $this->SetXY(($this->x_c + 10),$this->y_c+0.5);
                $this->Cell(9.5, 2, '*'.$partidas[$i+1]['CodigoEquipo'].'*', 0, 0, 'C');
                $this->RoundedRect($this->x_c + 10, $this->y_c+0.5, 9.5, 1.5, 1, '', 'D');
            }


            $this->SetXY(($this->x_c),$this->y_c+2);
            while($j<$caract1){
                $this->encola = 'caracteristicas';
                $this->y_c = $this->GetY();
                $this->x_c = $this->GetX();
                $this->x_p = $this->GetX();

                $this->SetRadius(array(0, 0));
                $this->SetFont('Arial', '', 5);
                $this->SetAligns(array('L', 'L'));
                $this->SetWidths(array(4.0, 5.5));
                $this->SetFills(array('205,205,205', '255,255,255'));
                $this->SetStyles(array('DF', 'DF'));
                $this->SetTextColors(array('0,0,0', '0,0,0'));
                $this->SetHeights(array(.3, .3));
                $linea = [];
                if(array_key_exists($j, $partidas[$i]['partida_caracteristicas'])){
                    $linea = array(utf8_decode($partidas[$i]['partida_caracteristicas'][$j]['NombreCaracteristicaPartida']), utf8_decode($partidas[$i]['partida_caracteristicas'][$j]['DescripcionCaracteristicaPartida']));
                }
                $this->Row($linea);
                if(array_key_exists($i+1, $partidas)){
                    if(array_key_exists($j, $partidas[$i+1]['partida_caracteristicas'])){
                        $this->x_p = $this->x_c+10;
                        $this->SetXY(($this->x_c+10),$this->y_c);
                        $this->Row(array(utf8_decode($partidas[$i+1]['partida_caracteristicas'][$j]['NombreCaracteristicaPartida']), utf8_decode($partidas[$i+1]['partida_caracteristicas'][$j]['DescripcionCaracteristicaPartida'])));
                    }
                }
                $j++;

            }
            $i++;
            $this->SetY($this->GetY() + 0.5);
        }

        if($this->GetY() > 22.0)
			$this->AddPage();

		$this->SetY(-6.0);


		$this->SetStyles(array("DF"));
		$this->SetFont('Arial', 'B', 9);
		$this->SetWidths(array(19.5));
		$this->SetHeights(array(.4));
		$this->SetFills(array('0,0,0'));
		$this->SetTextColors(array('255,255,255'));
		$this->SetRounds(array('12'));
		$this->SetAligns(array('J'));
		$this->SetRadius(array(.1));
		// $this->SetX(10.00125);
		$this->Row(array("RESPONSIVA"));

		$this->SetFont('Arial', '', 6);
		$this->SetWidths(array(19.5));
		$this->SetHeights(array(.3));
		$this->SetFills(array('255,255,255'));
		$this->SetTextColors(array('0,0,0'));
		$this->SetRounds(array('34'));
		$this->SetAligns(array('J'));
		$this->SetRadius(array(.1));
		$this->Row(array(utf8_decode($this->resguardo->resguardoElemento->Compromiso)));
    }

    function Footer(){
        $this->SetY(-2.5);
        $this->SetFont('Arial','B',7);
		$this->SetDrawColor(0,0,0);
		$this->SetTextColor(0,0,0);
		$this->Cell(10.0,.3,'La firma de este resguardo cancela de forma inmediata cualquier resguardo anterior emitido por este tipo de activo.',0,0,'L');
		$this->setY($this->getY()+.4);
        if(!in_array($this->resguardo->IdProyecto,array("22","23")))
			$this->Firmas();
		else
		{
			$this->setY($this->getY()-.2);

			$this->Cell(2.44,.3,'',0,0,'C');
			$this->Cell(4.875,.3,"Entrega",1,0,'C',1);
			$this->Cell(4.875,.3,"VoBo",1,0,'C',1);
			$this->Cell(4.875,.3,"Recibe",1,1,'C',1);

			$this->Cell(2.44,.8,'',0,0,'C');
			$this->Cell(4.875,.8,"",1,0,'C');
			$this->Cell(4.875,.8,"",1,0,'C');
			$this->Cell(4.875,.8,"",1,1,'C');

			$this->Cell(2.44,.3,'',0,0,'C');
			$this->Cell(4.875,.3,"Nombre y Firma",1,0,'C',1);
			$this->Cell(4.875,.3,"Nombre y Firma",1,0,'C',1);
			$this->Cell(4.875,.3,"Nombre y Firma",1,1,'C',1);
		}

        $this->SetTextColor(180,180,180);
		$this->SetY(-.7);
		$this->Cell(10.0,.4,'V091110',0,0,'L');
		$this->SetTextColor(0,0,0);
    	$this->Cell(9.5,.4,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
    }

    function firmas(){
        $firmas = $this->resguardo->firmasValidas;

        if(count($firmas) <= 3){
            $this->Cell(2.44,.3,'',0,0,'C');
            $this->SetFont('Arial','B',7);
			$this->SetTextColor(255,255,255);
			$this->SetFillColor(0,0,0);

            foreach($firmas as $key => $firma){
                $salto = 0;
                if($key == count($firmas)-1) $salto = 1;
                $this->Cell(4.875,.3,utf8_decode($firma->resguardoFirma->TituloFirma),1,$salto,'C',1);
            }

            $this->Cell(2.44,.3,'',0,0,'C');
            foreach($firmas as $key => $firma){
                $salto = 0;
                if($key == count($firmas)-1) $salto = 1;
                $this->Cell(4.875,.8,'',1,$salto,'C');
            }

            $this->Cell(2.44,.3,'',0,0,'C');
			$this->SetFont('Arial','B',5);
			$this->SetTextColor(0,0,0);
			$this->SetFillColor(221,221,221);
            foreach($firmas as $key => $firma){
                $salto = 0;
                if($key == count($firmas)-1) $salto = 1;
                $this->Cell(4.875,.3,utf8_decode($firma->Valor),1,$salto,'C',1);
            }
        }else{
            $cant = count($firmas);
            $this->SetFont('Arial','B',7);
			$this->SetTextColor(255,255,255);
			$this->SetFillColor(0,0,0);
			$ancho=19.5/$cant;

            foreach($firmas as $key => $firma){
                $salto = 0;
                if($key == $cant-1) $salto = 1;
                $this->Cell($ancho,.3,utf8_decode($firma->resguardoFirma->TituloFirma),1,$salto,'C',1);
            }
            foreach($firmas as $key => $firma){
                $salto = 0;
                if($key == $cant-1) $salto = 1;
                $this->Cell($ancho,.8,'',1,$salto,'C');
            }
            $this->SetFont('Arial','B',5);
			$this->SetTextColor(0,0,0);
			$this->SetFillColor(221,221,221);
            foreach($firmas as $key => $firma){
                $salto = 0;
                if($key == $cant-1) $salto = 1;
                $this->Cell($ancho,.3,utf8_decode($firma->Valor),1,$salto,'C',1);
            }

        }
    }

    function create()
    {
        $this->AddFont('code39', '', 'code39.php');
        $this->SetMargins(1, 1, 0.7);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 2.5);
        $this->asignacion();
        $this->detalles();
        $this->caracteristicas();

        try {
            $this->Output('I', 'Formato - Resguardo '.$this->resguardo->idResguardo.'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }

    function resizeToFit($imgFilename)
    {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);
        return [
            round($this->pixelsToCM($scale * $width)),
            round($this->pixelsToCM($scale * $height))
        ];
    }

    function pixelsToCM($val)
    {
        return ($val * self::MM_IN_INCH / self::DPI) / 10;
    }

}
