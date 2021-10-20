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

    ];


    public function transform(AvanceObra $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'fecha' => $model->fecha,
            'fecha_format' => $model->fecha_format,
            'observaciones' => (string) $model->observaciones,
            'estado' => (int) $model->estado,
            'estado_format' => $model->estado_format,
            'folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'usuario_registro' => ($model->id_usuario) ? $model->id_usuario : '--------------',
            'concepto_descripcion' => $model->concepto_descripcion
        ];
    }
}
