<?php


namespace App\PDF\CTPQ;


use App\Models\CTPQ\Poliza;
use Ghidev\Fpdf\Rotation;

class PolizaFormato extends Rotation
{
    private $poliza;
    private $encabezado_pdf = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct($poliza)
    {
        parent::__construct('P', 'cm', 'Letter');
     //   $this->obra = Obra::find(Context::getIdObra());
       // $this->poliza = $poliza;

        $this->SetAutoPageBreak(true, 5);
        $this->WidthTotal = $this->GetPageWidth() - 2;
        $this->txtTitleTam = 18;
        $this->txtSubtitleTam = 13;
        $this->txtSeccionTam = 9;
        $this->txtContenidoTam = 11;
        $this->txtFooterTam = 6;
    }

    function Header()
    {
        $this->setXY(0.9, 1.2);
        $this->SetTextColor('0', '0', '255');
        $this->SetFont('Helvetica', 'BI', 12);
        $this->Cell(0, 0, 'CONTPAQ i', 0, 0, 'L');
        $this->setXY(5.76, 1.2);
        $this->SetTextColor('0', '0', '0');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 0, 'LA PENINSULAR (098 RESIDENCIAL EL TEMPLO)', 0, 0, 'L');
        $this->setXY(18.50, 1.2);
        $this->Cell(0, 0, 'Hoja:   '.$this->PageNo(), 0, 0, 'L');

        $this->setXY(5.83, 1.6);
        $this->SetFont('Arial', 'B', 11.5);
        $this->Cell(0, 0, utf8_decode('Impreso de pólizas del 01/Nov/2019 al 30/Nov/2019'), 0, 0, 'L');
        $this->setXY(16.6, 1.6);
        $this->Cell(0, 0, utf8_decode('Fecha: 08/May/2020'), 0, 0, 'L');

        $this->setXY(8.3, 2);
        $this->Cell(0, 0, utf8_decode('Moneda: Peso Mexicano'), 0, 0, 'L');

        $this->setXY(0.9, 2.3);
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 0, utf8_decode('Dirección:'), 0, 0, 'L');
        $this->setXY(17, 2.3);
        $this->Cell(0, 0, utf8_decode('Código postal:'), 0, 0, 'L');

        $this->setXY(0.9, 2.6);
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 0, utf8_decode('Reg. Fed.: PC0811231EI4'), 0, 0, 'L');
        $this->setXY(5.83, 2.6);
        $this->Cell(0, 0, utf8_decode('Reg. Cámara:'), 0, 0, 'L');
        $this->setXY(12.9, 2.6);
        $this->Cell(0, 0, utf8_decode('Cta.Estatal:'), 0, 0, 'L');

        $this->partidasTitle();

        $currentPage = $this->PageNo();
        if ($currentPage > 1) {
            $this->Ln();
        }
    }

    public function partidasTitle()
    {
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);

        $this->setXY(1, 2.82);

        $this->Cell(3.1,0.6, 'Cuenta', 'BT', 0, 'L', 180);
        $this->Cell(5.2,0.6, 'Nombre', 'BT', 0, 'L', 180);
        $this->Cell(4,0.6, 'Referencia', 'BT', 0, 'L', 180);
        $this->Cell(2.5,0.6, 'Parcial', 'BT', 0, 'C', 180);
        $this->Cell(2.5, 0.6, 'Cargos', 'BT', 0, 'C', 180);
        $this->Cell(2.29, 0.6, 'Abonos', 'BT', 0, 'C', 180);

        $this->Ln();
    }

    public function partidas()
    {
        $this->SetFont('Arial', '', 10);

        $this->Cell(19.65,0.5, utf8_decode('Póliza de Egresos número 98007 correspondiente al 07/Nov/2019'), '', 0, 'C', 0);
        $this->Ln(0.4);
        $this->Cell(19.65,0.5, utf8_decode('TRANSF 5835471 F-716 MULTISERVICIOS MAYAKIN SA DE CV'), '', 0, 'C', 0);


        $this->Ln(0.68);
        $this->SetX(1);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);
// 1
        $this->Cell(3.1,0.5, '1195-000-000-000', '', 0, 'L', 180);
        $this->Cell(5.2,0.5, 'I.V.A. ACREDITABLE', '', 0, 'L', 180);
        $this->Cell(4,0.5, '', '', 0, 'L', 180);
        $this->Cell(2.5,0.5, '', '', 0, 'L', 180);
        $this->Cell(2.5, 0.5, '4,000.00', '', 0, 'L', 180);
        $this->Cell(2.29, 0.5, '', '', 0, 'L', 180);

        $this->Ln(0.45);
        $this->Cell(3.1,0.3, '', '', 0, 'L', 180);
        $this->Cell(5.2,0.3, '    TRANSF 5835471 RENTA C..', '', 1, 'L', 180);

        $this->SetFont('Arial', '', 10);
        $this->Cell(3.1,0.5, '1195-000-005-000', '', 0, 'L', 180);
        $this->Cell(5.2,0.5, ' IVA ACREDITABLE 16% PAG..', '', 0, 'L', 180);
        $this->Cell(4,0.5, 'F-716 MMA', '', 0, 'L', 180);
        $this->Cell(2.5,0.5, '4,000.00', '', 0, 'L', 180);
        $this->Cell(2.5, 0.5, '', '', 0, 'L', 180);
        $this->Cell(2.29, 0.5, '', '', 0, 'L', 180);

        $this->Ln(0.4);
        $this->Cell(3.1,0.3, '', '', 0, 'L', 180);
        $this->Cell(5.2,0.3, '    TRANSF 5835471 RENTA C..', '', 1, 'L', 180);

        //2
        $this->Ln(0.32);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(3.1,0.5, '2130-000-000-000', '', 0, 'L', 180);
        $this->Cell(5.2,0.5, 'ACREEDORES DIVERSOS', '', 0, 'L', 180);
        $this->Cell(4,0.5, '', '', 0, 'L', 180);
        $this->Cell(2.5,0.5, '', '', 0, 'L', 180);
        $this->Cell(2.5, 0.5, '29,000.00', '', 0, 'L', 180);
        $this->Cell(2.29, 0.5, '', '', 0, 'L', 180);

        $this->Ln(0.45);
        $this->Cell(3.1,0.3, '', '', 0, 'L', 180);
        $this->Cell(5.2,0.3, '    TRANSF 5835471 RENTA C..', '', 1, 'L', 180);

        $this->SetFont('Arial', '', 10);
        $this->Cell(3.1,0.5, '2130-002-098-509', '', 0, 'L', 180);
        $this->Cell(5.2,0.5, ' MULTISERVICIOS MAYAKIN ..', '', 0, 'L', 180);
        $this->Cell(4,0.5, 'F-716 MMA', '', 0, 'L', 180);
        $this->Cell(2.5,0.5, '29,000.00', '', 0, 'L', 180);
        $this->Cell(2.5, 0.5, '', '', 0, 'L', 180);
        $this->Cell(2.29, 0.5, '', '', 0, 'L', 180);

        $this->Ln(0.45);
        $this->Cell(3.1,0.3, '', '', 0, 'L', 180);
        $this->Cell(5.2,0.3, '    TRANSF 5835471 RENTA C..', '', 1, 'L', 180);

        //3
        $this->Ln(0.32);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);

        $this->Cell(3.1,0.5, '1196-000-000-000', '', 0, 'L', 180);
        $this->Cell(5.2,0.5, 'I.V.A. ACREDITABLE NO PAG..', '', 0, 'L', 180);
        $this->Cell(4,0.5, '', '', 0, 'L', 180);
        $this->Cell(2.5,0.5, '', '', 0, 'L', 180);
        $this->Cell(2.5, 0.5, '', '', 0, 'L', 180);
        $this->Cell(2.29, 0.5, '4,000.00', '', 0, 'L', 180);

        $this->Ln(0.45);
        $this->Cell(3.1,0.3, '', '', 0, 'L', 180);
        $this->Cell(5.2,0.3, '    TRANSF 5835471 RENTA C..', '', 1, 'L', 180);

        $this->SetFont('Arial', '', 10);
        $this->Cell(3.1,0.5, '1196-005-000-000', '', 0, 'L', 180);
        $this->Cell(5.2,0.5, ' IVA ACREDITABLE 16% NO ..', '', 0, 'L', 180);
        $this->Cell(4,0.5, 'F-716 MMA', '', 0, 'L', 180);
        $this->Cell(2.5,0.5, '4,000.00', '', 0, 'L', 180);
        $this->Cell(2.5, 0.5, '', '', 0, 'L', 180);
        $this->Cell(2.29, 0.5, '', '', 0, 'L', 180);

        $this->Ln(0.4);
        $this->Cell(3.1,0.3, '', '', 0, 'L', 180);
        $this->Cell(5.2,0.3, '    TRANSF 5835471 RENTA C..', '', 1, 'L', 180);

        //4
        $this->Ln(0.32);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);

        $this->Cell(3.1,0.5, '1105-000-000-000', '', 0, 'L', 180);
        $this->Cell(5.2,0.5, 'BANCOS', '', 0, 'L', 180);
        $this->Cell(4,0.5, '', '', 0, 'L', 180);
        $this->Cell(2.5,0.5, '', '', 0, 'L', 180);
        $this->Cell(2.5, 0.5, '', '', 0, 'L', 180);
        $this->Cell(2.29, 0.5, '29,000.00', '', 0, 'L', 180);

        $this->Ln(0.45);
        $this->Cell(3.1,0.3, '', '', 0, 'L', 180);
        $this->Cell(5.2,0.3, '    TRANSF 5835471 RENTA C..', '', 1, 'L', 180);

        $this->SetFont('Arial', '', 10);
        $this->Cell(3.1,0.5, '1105-002-098-001', '', 0, 'L', 180);
        $this->Cell(5.2,0.5, ' CUENTA SANTANDER 6550-..', '', 0, 'L', 180);
        $this->Cell(4,0.5, 'F-716 MMA', '', 0, 'L', 180);
        $this->Cell(2.5,0.5, '29,000.00', '', 0, 'L', 180);
        $this->Cell(2.5, 0.5, '', '', 0, 'L', 180);
        $this->Cell(2.29, 0.5, '', '', 0, 'L', 180);

        $this->Ln(0.4);
        $this->Cell(3.1,0.3, '', '', 0, 'L', 180);
        $this->Cell(5.2,0.3, '    TRANSF 5835471 RENTA C..', '', 1, 'L', 180);

    }

    public function Footer()
    {
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);

        $this->setXY(1, 24.5);
        $this->Cell(12.98,0.6, 'TRANSF 5835471 F-716 MULTISERVICIOS MAYAKIN SA DE CV', 'T', 0, 'L', 180);
        $this->setXY(14.15, 24.5);
        $this->Cell(3,0.6, '33,000.00', 'T', 0, 'R', 180);
        $this->setXY(17.3, 24.5);
        $this->Cell(3,0.6, '33,000.00', 'T', 0, 'R', 180);

        $this->setXY(1, 25.4);
        $this->Cell(4.2,0.6, utf8_decode('Elaboró'), 'T', 0, 'C', 180);
        $this->setXY(5.35, 25.4);
        $this->Cell(4.3 ,0.6, utf8_decode('Revisó'), 'T', 0, 'C', 180);
        $this->setXY(9.8, 25.4);
        $this->Cell(4.2,0.6, utf8_decode('Autorizó'), 'T', 0, 'C', 180);
        $this->setXY(14.15, 25.4);
        $this->Cell(3,0.5, 'Origen', 'T', 0, 'L', 180);
        $this->setXY(17.3, 25.4);
        $this->Cell(3,0.6, utf8_decode('Póliza'), 'T', 0, 'C', 180);

        $this->SetFont('Arial', '', 10);
        $this->setXY(14.15, 25.85);
        $this->Cell(3,0.3, 'CONTPAQ i', '', 0, 'L', 180);

        $this->setXY(17.3, 26.2);
        $this->Cell(3,0.5, 'Egresos # 98007', '', 0, 'R', 180);
        $this->setXY(17.3, 26.6);
        $this->Cell(3,0.5, '07/Nov/2019', '', 0, 'R', 180);
    }

    function create() {
        $this->SetMargins(1, 0.9, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,6.80);
        $this->partidas();

        try {
            //$this->Output('I', "Formato - poliza_".$this->contrato->numero_folio.".pdf", 1);
            $this->Output('I', "Formato - poliza.pdf", 1);
        } catch (\Exception $ex) {
            dd("error",$ex);
        }
        exit;
    }

}
