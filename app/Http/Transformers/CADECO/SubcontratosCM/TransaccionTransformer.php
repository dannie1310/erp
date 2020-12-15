<?php


namespace App\Http\Transformers\CADECO\SubcontratosCM;

use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;
use App\Models\CADECO\SubcontratosCM\Transaccion;
use League\Fractal\TransformerAbstract;

class TransaccionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subcontrato',
        'items'
    ];

    /**
     * @param Transaccion $model
     * @return array
     */
    public function transform(Transaccion $model)
    {
        return [
            'id' => $model->getKey(),
            'id_subcontrato' => $model->id_subcontrato,
            'fecha_format' => $model->fecha_format,
            'fecha' => $model->fecha,
            'fecha_registro_format' => $model->fecha_registro_format,
            'fecha_registro' => $model->fecha_registro,
            'impuesto' => $model->impuesto,
            'impuesto_format' => $model->impuesto_format,
            'monto' => $model->monto,
            'monto_format' => $model->monto_format,
            'usuario_registro' => $model->usuario_registro,
            'fecha_aplicacion' => $model->fecha_aplicacion,
            'fecha_aplicacion_format' => $model->fecha_aplicacion_format,
            'usuario_aplico' => $model->usuario_aplico,
        ];
    }

    /**
     * @param Transaccion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSubcontrato(Transaccion $model) {
        if ($subcontrato = $model->subcontrato) {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }

    /**
     * @param Transaccion $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeItems(Transaccion $model)
    {
        if ($items = $model->items) {
            return $this->collection($items, new ItemTransformer);
        }
        return null;
    }
}
