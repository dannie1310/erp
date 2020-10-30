<?php


namespace App\PDF\Contratos;

use App\Models\CADECO\Obra;
use App\Models\CADECO\Subcontratos\AsignacionContratista;
use Ghidev\Fpdf\Rotation;

class AsignacionTablaComparativaFormato extends Rotation
{
    protected $obra;
    protected $asignacion;
    private $encabezado_pdf = '';

    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 279;
    const A4_WIDTH = 216;

    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    public function __construct(AsignacionContratista $asignacion)
    {
        parent::__construct('L', 'cm', 'Letter');
        $this->obra = Obra::find(Context::getIdObra());
        $this->asignacion = $asignacion;
        $this->encabezado_pdf = utf8_decode($this->obra->facturar);
        //$this->createQR();
    }

    function create()
    {
        $this->SetMargins(0.7, 1, 0.7);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 4);
        //$this->partidas();

        try {
            $this->Output('I', 'Formato - Tabla Comparativa de Asignaciones '.$this->asignacion->numero_folio_format.'-contrato:'.$this->asignacion->contratoProyectado->numero_folio_format.'.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;

    }
}
