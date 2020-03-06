<?php


namespace App\Http\Transformers\CADECO\Finanzas;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Finanzas\ComplementoFactura;

class ComplementoFacturaTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(ComplementoFactura $model)
    {
        return [
            'id' => $model->getKey(),
            'iva' => $model->iva,
            'iva_format' => $model->iva_format,
            'ieps' => $model->ieps,
            'ieps_format' => $model->ieps_format,
            'imp_hosp' => $model->imp_hosp,
            'imp_hosp_format' => $model->imp_hosp_format,
            'ret_iva_4' => $model->ret_iva_4,
            'ret_iva_4_format' => $model->ret_iva4_format,
            'ret_iva_6' => $model->ret_iva_6,
            'ret_iva_6_format' => $model->ret_iva6_format,
            'ret_iva_10' => $model->ret_iva_10,
            'ret_iva_10_format' => $model->ret_iva10_format,
            'ret_isr_10' => $model->ret_isr_10,
            'ret_isr_10_format' => $model->ret_isr10_format,
        ];
    }
}