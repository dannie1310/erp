<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:46 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\Transaccion;

class ContratoProyectadoObserver extends TransaccionObserver
{
    /**
     * @param ContratoProyectado $contratoProyectado
     */
    public function creating(Transaccion $contratoProyectado)
    {
       parent::creating($contratoProyectado);
       $contratoProyectado->tipo_transaccion = 49;
       $contratoProyectado->opciones = 1026;
    }
}