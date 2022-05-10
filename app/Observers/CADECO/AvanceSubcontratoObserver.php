<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\AvanceSubcontrato;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\Transaccion;

class AvanceSubcontratoObserver extends TransaccionObserver
{

    /**
     * @param Transaccion $avance
     * @throws \Exception
     */
    public function creating(Transaccion $avance)
    {
        parent::creating($avance);
        $subcontrato = Subcontrato::find($avance->id_antecedente);
        $avance->id_empresa = $subcontrato->id_empresa;
        $avance->id_moneda = $subcontrato->id_moneda;
        $avance->referencia = $avance->observaciones;
        $avance->numero_folio = $avance->calcularFolio();
        $avance->tipo_transaccion = 105;
        $avance->opciones = 0;
        $avance->estado = 0;
    }

    public function deleting(AvanceSubcontrato $avanceSubcontrato)
    {
        $avanceSubcontrato->eliminarPartidas();
        $avanceSubcontrato->respaldar();
    }
}
