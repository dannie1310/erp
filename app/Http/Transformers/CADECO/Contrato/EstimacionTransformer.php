<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:22 PM
 */

namespace App\Http\Transformers\CADECO\Contrato;


use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\CADECO\ItemTransformer;
use App\Http\Transformers\CADECO\SubcontratosEstimaciones\SubcontratoEstimacionTrasnformer;
use App\Models\CADECO\Estimacion;
use League\Fractal\TransformerAbstract;
use Carbon\Carbon;

class EstimacionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subcontratoEstimacion',
        'subcontrato',
        'empresa',
        'moneda',
        'items',

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
            'numero_folio_format' => $model->numero_folio_format,
            'observaciones' => $model->observaciones,
            'impuesto' => $model->impuesto,
            'impuesto_format' => $model->impuesto_format,
            'monto' => $model->monto,
            'monto_format' => $model->monto_format,
            'estado' => $model->estado,
            'fecha' => $model->fecha_format,
            'fecha_inicial'=> $model->cumplimiento_format,
            'fecha_final' => $model->vencimiento_format,
            'subtotal' => $model->subtotal,
            'subtotal_format' => $model->subtotal_format
        ];
    }

    /**
     * @param Estimacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSubcontratoEstimacion(Estimacion $model)
    {
        if ($subcontratoEstimacion = $model->subcontratoEstimacion)
        {
            return $this->item($subcontratoEstimacion, new SubcontratoEstimacionTrasnformer);
        }
        return null;
    }

    /**
     * @param Estimacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSubcontrato(Estimacion $model)
    {
        if($subcontrato = $model->subcontrato)
        {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }

    /**
     * @param Estimacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Estimacion $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param Estimacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(Estimacion $model)
    {
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    /**
     * @param Estimacion $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeItems(Estimacion $model)
    {
        if($item= $model->item)
        {
            return $this->collection($item, new ItemTransformer);
        }
        return null;
    }
}
