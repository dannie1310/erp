<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 06:11 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\AjusteNegativo;
use App\Models\CADECO\Transaccion;

class AjusteNegativoObserver extends TransaccionObserver
{
    /**
     * @param AjusteNegativo $ajusteNegativo
     *  @throws \Exception
     */
    public function creating(Transaccion $ajusteNegativo)
    {
        parent::creating($ajusteNegativo);
        $ajusteNegativo->tipo_transaccion = 35;
        $ajusteNegativo->opciones = 1;
        $ajusteNegativo->estado = 0;
        $ajusteNegativo->fecha = date('Y-m-d H:i:s');
    }
}