<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 10:08 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\DescuentoFondoGarantia;
use App\Models\CADECO\Subcontrato;

class DescuentoFondoGarantiaObserver
{
    /**
     * @param DescuentoFondoGarantia $descuentoFondoGarantia
     * @throws \Exception
     */
    public function creating(DescuentoFondoGarantia $descuentoFondoGarantia)
    {
        if (!$descuentoFondoGarantia->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $subcontrato = Subcontrato::find($descuentoFondoGarantia->id_antecedente);
        $descuentoFondoGarantia->tipo_transaccion = 53;
        $descuentoFondoGarantia->opciones = 1;
        $descuentoFondoGarantia->estado = 1;
        $descuentoFondoGarantia->id_empresa = $subcontrato->id_empresa;
        $descuentoFondoGarantia->id_moneda = $subcontrato->id_moneda;
        $descuentoFondoGarantia->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $descuentoFondoGarantia->FechaHoraRegistro = date('Y-m-d h:i:s');
        $descuentoFondoGarantia->id_obra = Context::getIdObra();
        $descuentoFondoGarantia->id_usuario = auth()->id();
    }
}