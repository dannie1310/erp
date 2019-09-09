<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:54 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\SalidaAlmacen;

class SalidaAlmacenObserver
{
    /**
     * @param SalidaAlmacen $salida
     * @throws \Exception
     */
    public function creating(SalidaAlmacen $salida) //El creating que se encuentra en transaccion
    {
        if (!$salida->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $salida->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $salida->FechaHoraRegistro = date('Y-m-d h:i:s');
        $salida->id_obra = Context::getIdObra();
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