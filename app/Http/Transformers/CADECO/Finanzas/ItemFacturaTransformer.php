<?php

namespace App\Http\Transformers\CADECO\Finanzas;

use App\Models\CADECO\ItemFactura;
use League\Fractal\TransformerAbstract;

class ItemFacturaTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
        ''
    ];

    /**
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(ItemFactura $model)
    {
        return [
            'id' => $model->getKey(),
            'importe' => $model->importe,
            'importe_format' => $model->importe_format,
            'saldo' => number_format($model->saldo, 2,'.', ''),
            'saldo_base' => number_format($model->saldo, 2,'.', ''),
            'saldo_format' => $model->saldo_format,
            'descripcion_antecedente' => $model->descripcion_antecedente
        ];
    }
}