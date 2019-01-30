<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/01/2019
 * Time: 12:48 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoCuentaEmpresa;
use League\Fractal\TransformerAbstract;

class TipoCuentaEmpresaTransformer extends TransformerAbstract
{
    public function transform(TipoCuentaEmpresa $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}