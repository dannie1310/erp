<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:43 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\LiberacionFondoGarantia;
use App\Models\CADECO\Subcontrato;

class LiberacionFondoGarantiaObserver
{
    /**
     * @param LiberacionFondoGarantia $fondoGarantia
     * @throws \Exception
     */
    public function creating(LiberacionFondoGarantia $fondoGarantia)
    {
        if (!$fondoGarantia->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $subcontrato = Subcontrato::find($fondoGarantia->id_antecedente);
        $fondoGarantia->tipo_transaccion = 53;
        $fondoGarantia->opciones = 0;
        $fondoGarantia->estado = 1;
        $fondoGarantia->id_empresa = $subcontrato->id_empresa;
        $fondoGarantia->id_moneda = $subcontrato->id_moneda;
        $fondoGarantia->saldo = $fondoGarantia->monto;
        $fondoGarantia->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $fondoGarantia->FechaHoraRegistro = date('Y-m-d h:i:s');
        $fondoGarantia->id_obra = Context::getIdObra();
        $fondoGarantia->id_usuario = auth()->id();
    }

    public function updating(LiberacionFondoGarantia $fondoGarantia)
    {
        if($fondoGarantia->saldo != $fondoGarantia->monto){
            throw new \Exception('La transacción de liberación no puede ser cancelada, su saldo ya ha sido afectado');
        }
    }
}