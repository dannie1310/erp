<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:42 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\TipoTransaccion;
use League\Fractal\TransformerAbstract;

class TipoTransaccionTransformer extends TransformerAbstract
{
    public function transform(TipoTransaccion $model) {
        return [
            'descripcion' => $model->Descripcion,
            'opciones' => $model->Opciones,
            'tipo_transaccion' => $model->Tipo_Transaccion
        ];
    }
}