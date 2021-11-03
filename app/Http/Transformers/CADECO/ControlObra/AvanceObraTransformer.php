<?php


namespace App\Http\Transformers\CADECO\ControlObra;


use App\Models\CADECO\AvanceObra;
use League\Fractal\TransformerAbstract;

class AvanceObraTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'partidas'
    ];


    public function transform(AvanceObra $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'fecha' => $model->fecha,
            'fecha_format' => $model->fecha_format,
            'observaciones' => (string) $model->observaciones,
            'estado' => (int) $model->estado,
            'color_estado' => $model->color_estado,
            'descripcion_estado' => $model->descripcion_estado,
            'folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'nombre_usuario' => $model->nombre_usuario,
            'concepto_descripcion' => $model->concepto_descripcion,
            'concepto_nivel' => $model->concepto_nivel,
            'cumplimiento_format' => $model->cumplimiento_format,
            'vencimiento_format' => $model->vencimiento_format,
            'subtotal_format' => $model->subtotal_format,
            'impuesto_format' => $model->impuesto_format,
            'total_format' => $model->total_format
        ];
    }

    /**
     * @param AvanceObra $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(AvanceObra $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new ItemAvanceObraTransformer);
        }
        return null;
    }
}
