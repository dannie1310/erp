<?php


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Destino;
use League\Fractal\TransformerAbstract;

class DestinoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(Destino $model)
    {
        return [
            'id_concepto' => $model->getKey(),
            'destino_path' => $model->ruta_destino,
            'path' => $model->ruta,
            'descripcion' => $model->concepto->descripcion
        ];
    }
}
