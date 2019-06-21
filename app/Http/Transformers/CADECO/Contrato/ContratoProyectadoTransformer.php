<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 08:33 PM
 */

namespace App\Http\Transformers\CADECO\Contrato;


use App\Models\CADECO\ContratoProyectado;
use League\Fractal\TransformerAbstract;

class ContratoProyectadoTransformer extends TransformerAbstract
{
    public function transform(ContratoProyectado $model)
    {
        return [
            'id' => $model->getKey(),
            'numeroFolio' => $model->numero_folio,
            'referencia' => (string)$model->referencia
        ];
    }
}