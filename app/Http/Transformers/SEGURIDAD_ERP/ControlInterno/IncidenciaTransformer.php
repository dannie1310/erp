<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\ControlInterno;

use App\Http\Transformers\IGH\UsuarioTransformer;
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
        'tipo',
        'usuario'
    ];

    public function transform(Incidencia $model) {
        return [
            'id' => (int) $model->getKey(),
            'obra' => (string) $model->obra,
            'base_datos' => (string) $model->base_datos,
            'tipo_incidencia' => $model->id_tipo_incidencia,
            'usuario' => $model->id_usuario
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

    /**
     * @param Incidencia $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(Incidencia $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}
