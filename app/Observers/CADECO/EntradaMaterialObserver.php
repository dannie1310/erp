<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:28 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\EntradaMaterial;

class EntradaMaterialObserver
{
    /**
     * @param EntradaMaterial $entradaMaterial
     */
    public function creating(EntradaMaterial $entradaMaterial)
    {
        $entradaMaterial->tipo_transaccion = 33;
        $entradaMaterial->estado = 0;
        $entradaMaterial->opciones = 1;
        $entradaMaterial->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $entradaMaterial->FechaHoraRegistro = date('Y-m-d h:i:s');
        $entradaMaterial->id_obra = Context::getIdObra();
        $entradaMaterial->fecha = date('Y-m-d h:i:s');
        $entradaMaterial->id_usuario = auth()->id();
    }

    public function created(EntradaMaterial $entradaMaterial)
    {

    }

    public function deleting(EntradaMaterial $entradaMaterial)
    {
        $items = $entradaMaterial->partidas()->get()->toArray();
        $entradaMaterial->eliminar_partidas($items);
        $entradaMaterial->liberarOrdenCompra();
    }
}