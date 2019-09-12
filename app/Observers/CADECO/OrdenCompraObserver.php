<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:46 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\OrdenCompra;

class OrdenCompraObserver
{
    /**
     * @param OrdenCompra $ordenCompra
     * @throws \Exception
     */
    public function creating(OrdenCompra $ordenCompra) //El creating que se encuentra en transaccion
    {
        if (!$ordenCompra->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $ordenCompra->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $ordenCompra->FechaHoraRegistro = date('Y-m-d h:i:s');
        $ordenCompra->id_obra = Context::getIdObra();
    }
}