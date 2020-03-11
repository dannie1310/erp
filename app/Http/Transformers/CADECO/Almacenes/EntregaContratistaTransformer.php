<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/12/2019
 * Time: 02:56 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Almacenes\EntregaContratista;


class EntregaContratistaTransformer extends TransformerAbstract
{
    public function transform(EntregaContratista $model) {
        return [
            'id' => (int) $model->getKey(),
            'id_empresa' => $model->salida->empresa->id_empresa,
            'tipo' => $model->tipo_string,
            'folio' => $model->numero_folio,
            'folio_format' => $model->numero_folio_format,
            'contratista' => $model->salida->empresa->razon_social,
            'tipo_cargo' => (int) $model->tipo,
        ];
    }
}
