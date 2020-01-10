<?php


namespace App\Http\Transformers\CADECO\Configuracion;


use App\Models\CADECO\Configuracion\CtgTipoNodo;
use App\Models\CADECO\NodoProyecto;
use League\Fractal\TransformerAbstract;

class NodoProyectoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'nodo_tipo'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    public function transform(NodoProyecto $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'nivel' => $model->nivel,
            'path' => $model->path,
            'tipos_pendiente_asignacion' => $model->pendientes
        ];
    }

    /**
     * @param NodoProyecto $model
     * @return \League\Fractal\Resource\NodoProyecto|null
     */
    public function includeNodoTipo(NodoProyecto $model)
    {
        if($nodo_tipo = $model->nodoTipo){
            return $this->collection($nodo_tipo, new NodoTipoTransformer);
        }
        return null;
    }

}
