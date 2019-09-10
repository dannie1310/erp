<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 09:34 PM
 */

namespace App\Observers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\MovimientoSolicitudMovimientoFondoGarantia;

class MovimientoSolicitudMovimientoFondoGarantiaObserver
{
    /**
     * @param MovimientoSolicitudMovimientoFondoGarantia $movimiento
     * @throws \Exception
     */
    public function creating(MovimientoSolicitudMovimientoFondoGarantia $movimiento)
    {
        $movimiento->created_at = date('Y-m-d h:i:s');
        $movimiento->id_movimiento_antecedente = ($movimiento->solicitud_movimiento->movimiento_autorizacion)?$movimiento->solicitud_movimiento->movimiento_autorizacion->id:NULL;
        if(!$movimiento->validaNoExistenciaDeMovimientoPrevio())
        {
            throw New \Exception('Ya existe un movimiento del mismo tipo, el movimiento no puede registrarse');
        }
        if(!$movimiento->validaTipoMovimiento())
        {
            throw New \Exception('El tipo de movimiento: '. $movimiento->tipo->descripcion .' no puede registrarse si es precedido por el tipo de movimiento: '. $movimiento->solicitud_movimiento->
                ultimo_movimiento->tipo->descripcion);
        }
    }
}