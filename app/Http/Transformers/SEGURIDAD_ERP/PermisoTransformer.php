<?php

namespace App\Http\Transformers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\Permiso;
use League\Fractal\TransformerAbstract;

class PermisoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'sistema',
    ];

    public function transform(Permiso $model) {
        return [
            'id' => (int) $model->getKey(),
            'description' => (string) $model->description,
            'display_name' => (string) $model->display_name,
            'name' => (string) $model->name,
            'sistema_id' => $model->sistema_id
        ];
    }

    public function includeSistema(Permiso $model)
    {
        if ($sistema = $model->sistema) {
            return $this->item($sistema, new SistemaTransformer);
        }
        return null;
    }
}