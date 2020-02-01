<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\ControlInterno;


use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use League\Fractal\TransformerAbstract;

class IncidenciaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo'
    ];

    public function transform(Incidencia $model) {
        return [
            'id' => (int) $model->getKey(),
            'description' => (string) $model->description,
            'display_name' => (string) $model->display_name,
            'name' => (string) $model->name,
            'obra' => (string) $model->obra,
            'sistema_id' => $model->sistema_id
        ];
    }

    /**
     * @param Incidencia $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipo(Incidencia $model)
    {
        if($tipo = $model->tipo)
        {
            return $this->item($tipo, new TipoIncidenciaTransformer);
        }
        return null;
    }
}
