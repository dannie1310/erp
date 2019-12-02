<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:54 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Almacenes\EntregaContratista;
use App\Models\CADECO\SalidaAlmacen;
use App\Models\CADECO\Transaccion;

class SalidaAlmacenObserver extends TransaccionObserver
{
    /**
     * @param SalidaAlmacen $salida
     *  @throws \Exception
     */
    public function creating(Transaccion $salida)
    {
       parent::creating($salida);
        $salida->tipo_transaccion = 34;
    }

    public function created(Transaccion $salida)
    {
        if($salida->id_empresa != null)
        {
            EntregaContratista::create([
                'id_transaccion' => $salida->id_transaccion,
            ]);
        }
    }

    public function deleting(SalidaAlmacen $salida)
    {
        if($salida->opciones == 65537 ) {
            $salida->eliminar_transferencia();
        }
        if ($salida->opciones == 1){
            $salida->eliminar_salida();
        }
    }
}