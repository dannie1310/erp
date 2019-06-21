<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:22 PM
 */

namespace App\Http\Transformers\CADECO\Contrato;


use App\Http\Transformers\CADECO\SubcontratosEstimaciones\SubcontratoEstimacionTrasnformer;
use App\Models\CADECO\Estimacion;
use League\Fractal\TransformerAbstract;

class EstimacionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subcontratoEstimacion',
        'subcontrato'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(Estimacion $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_folio' => $model->numero_folio,
            'observaciones' => $model->observaciones,
            'impuesto' => $model->impuesto,
            'monto' => $model->monto,
            'estado' => $model->estado
        ];
    }

    public function includeSubcontratoEstimacion(Estimacion $model)
    {
        if ($subcontratoEstimacion = $model->subcontratoEstimacion) {
            return $this->item($subcontratoEstimacion, new SubcontratoEstimacionTrasnformer);
        }
        return null;
    }

    public function includeSubcontrato(Estimacion $model)
    {
        if($subcontrato = $model->subcontrato) {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }
}