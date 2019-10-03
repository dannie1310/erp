<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 01/10/2019
 * Time: 07:44 PM
 */

namespace App\Observers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\LayoutPago;

class LayoutPagoObserver
{
    /**
     * @param LayoutPago $layoutPago
     */
    public function creating(LayoutPago $layoutPago)
    {
        $layoutPago->id_usuario_carga = auth()->id();
        $layoutPago->fecha_hora_carga = date('Y-m-d h:i:s');
        $layoutPago->estado = 1;
    }
}