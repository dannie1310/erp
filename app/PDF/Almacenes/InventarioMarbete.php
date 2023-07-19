<?php


namespace App\PDF\Almacenes;


use App\Models\CADECO\Inventarios\InventarioFisico;
use Ghidev\Fpdf\Rotation;
use Illuminate\Support\Facades\App;

class InventarioMarbete extends Rotation
{
    protected $inventario;
    protected $e = 1.45;


    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(InventarioFisico $inventario)
    {
        parent::__construct('L', 'cm', 'Letter');

        $this->WidthTotal = $this->GetPageWidth() - 2;
        $this->inventario = $inventario;

    }

    function Header()
    {
        if (!App::environment('production')) {
            $this->SetFont('Arial','B',80);
            $this->SetTextColor(155,155,155);
            $this->RotatedText(5,20,utf8_decode("MUESTRA"),45);
            $this->RotatedText(6,26,utf8_decode("SIN VALOR"),45);
            $this->SetTextColor('0,0,0');
        }
        $this->setY(0.73);
        $this->e = 1.47;
    }

    public function partidas(){

        $this->SetTextColor('0', '0', '0');
        $this->SetFillColor(255, 255, 255);

        foreach ($this->inventario->marbetes as $key => $material) {
            $marbete = $material;
            $folio_format = str_pad($marbete->folio, '6', 0, 0);
            $marb = $this->inventario->getNumeroFolioFormatAttribute() .' - '. chunk_split($folio_format, 3);
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(4.5);
            $this->Cell(3, 0.55, utf8_decode($marb), '', 0, 'R', 1);
            $this->Cell(5);
            $this->Cell(4.3, 0.55, utf8_decode($marb), '', 0, 'R', 1);
            $this->Cell(5.2);
            $this->Cell(3.7, 0.55, utf8_decode($marb), '', 0, 'R', 1);

            $this->SetY($this->GetY() + 0.6);
            $this->SetFont('Arial', 'B', 6);
            $this->Cell(2.2);
            $this->Cell(5.15, 0.45, utf8_decode($this->inventario->obra->nombre), '', 0, 'C', 1);
            $this->Cell(2.65);
            $this->Cell(6.8, 0.45, utf8_decode($marbete->almacen->descripcion), '', 0, 'C', 1);
            $this->Cell(2.65);
            $this->Cell(6.25, 0.45, utf8_decode($marbete->almacen->descripcion), '', 0, 'C', 1);

            $this->SetY($this->GetY() + 0.45);
            $this->SetFont('Arial', 'B', 5.5);
            $this->Cell(2.2);
            $this->Cell(5.15, 0.65, utf8_decode($marbete->almacen->descripcion), '', 0, 'C', 1);
            $this->Cell(2.65);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(2.8, 0.65, utf8_decode($marbete->material->numero_parte), '', 0, 'C', 1);
            $this->Cell(1.4);
            $this->Cell(2.6, 0.65, utf8_decode($marbete->material->unidad), '', 0, 'C', 1);
            $this->Cell(2.65);
            $this->Cell(2.8, 0.65, utf8_decode($marbete->material->numero_parte), '', 0, 'C', 1);
            $this->Cell(1.4);
            $this->Cell(2, 0.65, utf8_decode($marbete->material->unidad), '', 0, 'C', 1);

            $this->SetY($this->GetY() + 0.7);
            $this->SetFont('Arial', 'B', 5.5);
            $this->Cell(2.2);
            $this->Cell(5.15, 0.65, utf8_decode(substr($marbete->material->familia->descripcion, 0, 42)), '', 0, 'C', 1);
            $this->SetFont('Arial', '', 6);
            $this->Cell(1.30);
            $this->MultiAlignCell(8.25, 0.25, utf8_decode('                '.$marbete->material->descripcion), '', 0, 'L');
            $this->SetFont('Arial', '', 6.5);
            $this->Cell(1.15);
            $this->MultiAlignCell(7.85, 0.3, utf8_decode('                '.$marbete->material->descripcion),  '', 0, 'L');

            $this->SetY($this->GetY() + 0.65);
            $this->SetFont('Arial', '', 6.5);
            $this->Cell(2.2);
            $this->Cell(2.15, 0.65, utf8_decode($marbete->material->numero_parte), '', 0, 'C', 1);
            $this->Cell(1.4);
            $this->Cell(1.65, 0.65, utf8_decode($marbete->material->id_material), '', 0, 'C', 1);

            $this->SetY($this->GetY() + 0.6);
            $this->SetFont('Arial', '', 6.5);
            $this->Cell(2.2);
            $this->Cell(2.15, 0.7, utf8_decode(''), '', 0, 'C', 1);
            $this->Cell(1.4);
            $this->Cell(1.65, 0.7, utf8_decode($marbete->material->unidad), '', 0, 'C', 1);

            $this->SetY($this->GetY() + 0.75);
            $this->SetFont('Arial', '', 6);
            $this->Cell(0.9);
            $this->MultiAlignCell(6.55, 0.28, utf8_decode('                '.$marbete->material->descripcion ), '', 0, 'L');

            $this->SetY($this->GetY() + 1.7);
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(0.8);
            $this->Code39($this->GetX() + 1.6,$this->GetY() +0.1, strval($marbete->id) );
            $this->Cell(1.1);
            $this->Code39($this->GetX() + 9.2,$this->GetY() +0.1, strval($marbete->id.'C2') );
            $this->Cell(1.15);
            $this->Code39($this->GetX() + 17 ,$this->GetY() +0.1, strval($marbete->id.'C1') );
            $this->setFillColor('255','255','255');
//            dd('pardo', $this->GetY(), $this->GetY() + $this->e, $this->e, $marbete->material->id_material);
            $this->SetY($this->GetY() + $this->e);
            $this->e -=0.03;

        }
    }

    public function create() {
        $this->SetMargins(1.1, 0.5, 1.1);
        $this->SetAutoPageBreak(true, 0);
        $this->AddPage();
        $this->partidas();
        try {
            $this->Output('D', "Marbetes Inventario #".$this->inventario->getNumeroFolioFormatAttribute().".pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

    private function MultiAlignCell($w,$h,$text,$border=0,$ln=0,$align='L',$fill=false)
    {
        // Store reset values for (x,y) positions
        $x = $this->GetX() + $w;
        $y = $this->GetY();

        // Make a call to FPDF's MultiCell
        $this->MultiCell($w,$h,$text,$border,$align,$fill);

        // Reset the line position to the right, like in Cell
        if( $ln==0 )
        {
            $this->SetXY($x,$y);
        }
    }

    function pixelsToCM($val) {
        return ($val * self::MM_IN_INCH / self::DPI) / 10;
    }


    function resizeToFit($imgFilename) {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);
        return [
            round($this->pixelsToCM($scale * $width)),
            round($this->pixelsToCM($scale * $height))
        ];
    }

    public function Code39($x, $y, $code, $ext = true, $cks = false, $w = 0.022, $h = .7, $wide = true) {
        //Display code
        $this->SetFont('Arial', '', 10);
//        $this->Text($x, $y+$h+0.50, $code);
        if($ext)
        {
            //Extended encoding
            $code = $this->encode_code39_ext($code);
        }
        else
        {
            //Convert to upper case
            $code = strtoupper($code);
            //Check validity
            if(!preg_match('|^[0-9A-Z. $/+%-]*$|', $code))
                $this->Error('Invalid barcode value: '.$code);
        }
        //Compute checksum
        if ($cks)
            $code .= $this->checksum_code39($code);
        //Add start and stop characters
        $code = '*'.$code.'*';
        //Conversion tables
        $narrow_encoding = array (
            '0' => '101001101101', '1' => '110100101011', '2' => '101100101011',
            '3' => '110110010101', '4' => '101001101011', '5' => '110100110101',
            '6' => '101100110101', '7' => '101001011011', '8' => '110100101101',
            '9' => '101100101101', 'A' => '110101001011', 'B' => '101101001011',
            'C' => '110110100101', 'D' => '101011001011', 'E' => '110101100101',
            'F' => '101101100101', 'G' => '101010011011', 'H' => '110101001101',
            'I' => '101101001101', 'J' => '101011001101', 'K' => '110101010011',
            'L' => '101101010011', 'M' => '110110101001', 'N' => '101011010011',
            'O' => '110101101001', 'P' => '101101101001', 'Q' => '101010110011',
            'R' => '110101011001', 'S' => '101101011001', 'T' => '101011011001',
            'U' => '110010101011', 'V' => '100110101011', 'W' => '110011010101',
            'X' => '100101101011', 'Y' => '110010110101', 'Z' => '100110110101',
            '-' => '100101011011', '.' => '110010101101', ' ' => '100110101101',
            '*' => '100101101101', '$' => '100100100101', '/' => '100100101001',
            '+' => '100101001001', '%' => '101001001001' );
        $wide_encoding = array (
            '0' => '101000111011101', '1' => '111010001010111', '2' => '101110001010111',
            '3' => '111011100010101', '4' => '101000111010111', '5' => '111010001110101',
            '6' => '101110001110101', '7' => '101000101110111', '8' => '111010001011101',
            '9' => '101110001011101', 'A' => '111010100010111', 'B' => '101110100010111',
            'C' => '111011101000101', 'D' => '101011100010111', 'E' => '111010111000101',
            'F' => '101110111000101', 'G' => '101010001110111', 'H' => '111010100011101',
            'I' => '101110100011101', 'J' => '101011100011101', 'K' => '111010101000111',
            'L' => '101110101000111', 'M' => '111011101010001', 'N' => '101011101000111',
            'O' => '111010111010001', 'P' => '101110111010001', 'Q' => '101010111000111',
            'R' => '111010101110001', 'S' => '101110101110001', 'T' => '101011101110001',
            'U' => '111000101010111', 'V' => '100011101010111', 'W' => '111000111010101',
            'X' => '100010111010111', 'Y' => '111000101110101', 'Z' => '100011101110101',
            '-' => '100010101110111', '.' => '111000101011101', ' ' => '100011101011101',
            '*' => '100010111011101', '$' => '100010001000101', '/' => '100010001010001',
            '+' => '100010100010001', '%' => '101000100010001');
        $encoding = $wide ? $wide_encoding : $narrow_encoding;
        //Inter-character spacing
        $gap = ($w > 0.29) ? '00' : '0';
        //Convert to bars
        $encode = '';
        for ($i = 0; $i< strlen($code); $i++)
            $encode .= $encoding[$code{$i}].$gap;
        //Draw bars
        $this->draw_code39($encode, $x, $y, $w, $h);
    }
    public function checksum_code39($code) {
        //Compute the modulo 43 checksum
        $chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
            'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
            'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%');
        $sum = 0;
        for ($i=0 ; $i<strlen($code); $i++) {
            $a = array_keys($chars, $code{$i});
            $sum += $a[0];
        }
        $r = $sum % 43;
        return $chars[$r];
    }
    public function encode_code39_ext($code) {
        //Encode characters in extended mode
        $encode = array(
            chr(0) => '%U', chr(1) => '$A', chr(2) => '$B', chr(3) => '$C',
            chr(4) => '$D', chr(5) => '$E', chr(6) => '$F', chr(7) => '$G',
            chr(8) => '$H', chr(9) => '$I', chr(10) => '$J', chr(11) => '£K',
            chr(12) => '$L', chr(13) => '$M', chr(14) => '$N', chr(15) => '$O',
            chr(16) => '$P', chr(17) => '$Q', chr(18) => '$R', chr(19) => '$S',
            chr(20) => '$T', chr(21) => '$U', chr(22) => '$V', chr(23) => '$W',
            chr(24) => '$X', chr(25) => '$Y', chr(26) => '$Z', chr(27) => '%A',
            chr(28) => '%B', chr(29) => '%C', chr(30) => '%D', chr(31) => '%E',
            chr(32) => ' ', chr(33) => '/A', chr(34) => '/B', chr(35) => '/C',
            chr(36) => '/D', chr(37) => '/E', chr(38) => '/F', chr(39) => '/G',
            chr(40) => '/H', chr(41) => '/I', chr(42) => '/J', chr(43) => '/K',
            chr(44) => '/L', chr(45) => '-', chr(46) => '.', chr(47) => '/O',
            chr(48) => '0', chr(49) => '1', chr(50) => '2', chr(51) => '3',
            chr(52) => '4', chr(53) => '5', chr(54) => '6', chr(55) => '7',
            chr(56) => '8', chr(57) => '9', chr(58) => '/Z', chr(59) => '%F',
            chr(60) => '%G', chr(61) => '%H', chr(62) => '%I', chr(63) => '%J',
            chr(64) => '%V', chr(65) => 'A', chr(66) => 'B', chr(67) => 'C',
            chr(68) => 'D', chr(69) => 'E', chr(70) => 'F', chr(71) => 'G',
            chr(72) => 'H', chr(73) => 'I', chr(74) => 'J', chr(75) => 'K',
            chr(76) => 'L', chr(77) => 'M', chr(78) => 'N', chr(79) => 'O',
            chr(80) => 'P', chr(81) => 'Q', chr(82) => 'R', chr(83) => 'S',
            chr(84) => 'T', chr(85) => 'U', chr(86) => 'V', chr(87) => 'W',
            chr(88) => 'X', chr(89) => 'Y', chr(90) => 'Z', chr(91) => '%K',
            chr(92) => '%L', chr(93) => '%M', chr(94) => '%N', chr(95) => '%O',
            chr(96) => '%W', chr(97) => '+A', chr(98) => '+B', chr(99) => '+C',
            chr(100) => '+D', chr(101) => '+E', chr(102) => '+F', chr(103) => '+G',
            chr(104) => '+H', chr(105) => '+I', chr(106) => '+J', chr(107) => '+K',
            chr(108) => '+L', chr(109) => '+M', chr(110) => '+N', chr(111) => '+O',
            chr(112) => '+P', chr(113) => '+Q', chr(114) => '+R', chr(115) => '+S',
            chr(116) => '+T', chr(117) => '+U', chr(118) => '+V', chr(119) => '+W',
            chr(120) => '+X', chr(121) => '+Y', chr(122) => '+Z', chr(123) => '%P',
            chr(124) => '%Q', chr(125) => '%R', chr(126) => '%S', chr(127) => '%T');
        $code_ext = '';
        for ($i = 0 ; $i<strlen($code); $i++) {
            if (ord($code{$i}) > 127)
                $this->Error('Invalid character: '.$code{$i});
            $code_ext .= $encode[$code{$i}];
        }
        return $code_ext;
    }
    public function draw_code39($code, $x, $y, $w, $h){
        //Draw bars
        for($i=0; $i<strlen($code); $i++)
        {
            if($code{$i} == '1') {
                $this->setFillColor('0','0','0');
                $this->Rect($x+$i*$w, $y, $w, $h, 'F');
            }
            else {
                //else cond added by Roger V. form Alcatel-Lucent to cope with small size printed barcodes
                $x += 0.01;
            }
        }
    }

}
