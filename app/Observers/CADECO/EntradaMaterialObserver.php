<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:28 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\EntradaMaterial;

class EntradaMaterialObserver
{
    /**
     * @param EntradaMaterial $entradaMaterial
     * @throws \Exception
     */
    public function creating(EntradaMaterial $entradaMaterial) //El creating que se encuentra en transaccion
    {
        $entradaMaterial->validarRegistro();
        dd($entradaMaterial->id_antecedente);
        if (!$entradaMaterial->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $entradaMaterial->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $entradaMaterial->FechaHoraRegistro = date('Y-m-d h:i:s');
        $entradaMaterial->id_obra = Context::getIdObra();
        $entradaMaterial->id_usuario = auth()->id();
    }

    public function deleting(EntradaMaterial $entradaMaterial)
    {
        $items = $entradaMaterial->partidas()->get()->toArray();
        $entradaMaterial->eliminar_partidas($items);
        $entradaMaterial->liberarOrdenCompra();
    }
}