<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 09:41 PM
 */

namespace App\Observers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia;

class SolicitudMovimientoFondoGarantiaObserver
{
    /**
     * @param SolicitudMovimientoFondoGarantia $movimientoFondoGarantia
     * @throws \Exception
     */
    public function creating(SolicitudMovimientoFondoGarantia $movimientoFondoGarantia)
    {
        $movimientoFondoGarantia->created_at = date('Y-m-d h:i:s');
        if (!$movimientoFondoGarantia->validaNoSolicitudesPendientes()) {
            throw New \Exception('Hay una solicitud de movimiento a fondo de garantía pendiente de autorizar, la solicitud actual no puede registrarse');
        }
        if (!$movimientoFondoGarantia->validaMontoSolicitud()) {
            throw New \Exception('El monto de la solicitud sobrepasa el monto disponible del fondo de garantía: $ '.number_format($movimientoFondoGarantia->fondo_garantia->saldo,2).'.');
        }
    }

    public function created(SolicitudMovimientoFondoGarantia $movimientoFondoGarantia)
    {
        $movimientoFondoGarantia->generaMovimientoSolicitud(1, $movimientoFondoGarantia->usuario_registra);
    }
}