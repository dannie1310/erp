<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;

use App\Models\ACARREOS\Impresora;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\ACARREOS\Catalogos\ImpresoraHistoricoTransformer;

class ImpresoraTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'historicos'
    ];


    public function transform(Impresora $model) {
        return [
            'id' => (int) $model->getKey(),
            'mac' => $model->mac,
            'marca' => (string) $model->marca,
            'modelo' => (string) $model->modelo,
            'usuario_registro' => (string) $model->nombre_usuario,
            'estado' => (int) $model->estatus,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
        ];
    }

    /**
     * @param Impresora $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeHistoricos(Impresora $model)
    {
        if($historicos = $model->historicos)
        {
            return $this->collection($historicos, new ImpresoraHistoricoTransformer);
        }
        return null;
    }
}
