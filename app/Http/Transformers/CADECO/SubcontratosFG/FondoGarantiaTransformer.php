<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2019
 * Time: 08:54 PM
 */

namespace App\Http\Transformers\CADECO\SubcontratosFG;


use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use League\Fractal\TransformerAbstract;

class FondoGarantiaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subcontrato',
        'movimientos'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'subcontrato'
    ];

    /**
     * @param FondoGarantia $model
     * @return array
     */
    public function transform(FondoGarantia $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'fecha' => (string)$model->fecha,
            'saldo_format' => (string)$model->saldo_format,
            'saldo' => (float)$model->saldo,
            'suma_cargos_format' => (string) '$ ' . number_format($model->suma_cargos,2,".",","),
            'suma_abonos_format' => (string) '$ ' . number_format(abs($model->suma_abonos),2,".",","),
            'porcentaje_cargos' => (float) $model->porcentaje_cargos,
            'porcentaje_abonos' => (float) $model->porcentaje_abonos,
            'estilo_porcentaje_cargos' =>(string)"width: ".$model->porcentaje_cargos . "%",
            'estilo_porcentaje_abonos' =>(string)"width: ".$model->porcentaje_abonos . "%",
            'created_at'=>(string)$model->created_at,
        ];
    }

    /**
     * Include Subcontrato
     *
     * @param FondoGarantia $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeSubcontrato(FondoGarantia $model) {
        if ($subcontrato = $model->subcontrato) {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }

    /**
     * Include Movimientos
     *
     * @param FondoGarantia $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeMovimientos(FondoGarantia $model) {
        if ($movimientos = $model->movimientos) {
            return $this->collection($movimientos, new MovimientoFondoGarantiaTransformer);
        }
        return null;
    }
}
