<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 08:28 PM
 */

namespace App\Observers\CADECO\FinanzasCBE;


use App\Models\CADECO\FinanzasCBE\SolicitudMovimiento;

class SolicitudMovimientoObserver
{
    /**
     * @param SolicitudMovimiento $solicitudMovimiento
     */
    public function creating(SolicitudMovimiento $solicitudMovimiento)
    {
        $solicitudMovimiento->ip = gethostbyname(getHostName());
        $solicitudMovimiento->mac_address = substr(shell_exec('getmac'), 475,17);
        $solicitudMovimiento->fecha_hora = date('Y-m-d H:i:s');
        $solicitudMovimiento->usuario_registra = auth()->id();
    }
}