<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/12/2019
 * Time: 01:41 PM
 */

namespace App\Observers\CADECO\Almacenes;


use App\Models\CADECO\Almacenes\EntregaContratista;

class EntregaContratistaObserver
{
    public function creating(EntregaContratista $model)
    {
        $model->numero_folio = $model->calcularFolio($model->salida->id_empresa);
    }

}