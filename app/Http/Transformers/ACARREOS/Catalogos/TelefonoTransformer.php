<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Http\Transformers\CADECO\ConceptoTransformer;
use App\Models\ACARREOS\Telefono;
use League\Fractal\TransformerAbstract;

class TelefonoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'historicos'
    ];


    public function transform(Telefono $model) {
        return [
            'id' => (int) $model->getKey(),
            'imei' => $model->imei,
            'linea' => $model->linea,
            'marca' => (string) $model->marca,
            'modelo' => (string) $model->modelo,
            'id_dispositivo' => $model->device_id,
            'checador' => (string) $model->nombre_checador,
            'id_checador' =>  $model->id_checador,
            'usuario_registro' => (string) $model->nombre_registro,
            'estado' => $model->estatus,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
        ];
    }

    /**
     * @param Tiro $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeHistoricos(Telefono $model)
    {
        if($historicos = $model->historicos)
        {
            return $this->collection($historicos, new TelefonoHistoricoTransformer);
        }
        return null;
    }
}
