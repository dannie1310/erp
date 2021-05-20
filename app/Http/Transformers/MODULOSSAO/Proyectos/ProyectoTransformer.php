<?php

namespace App\Http\Transformers\MODULOSSAO\Proyectos;

use App\Models\MODULOSSAO\Proyectos\Proyecto;
use League\Fractal\TransformerAbstract;

class ProyectoTransformer extends TransformerAbstract
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
    protected $defaultIncludes = [
        'empresa',
        'tipo'

    ];

    public function transform(Proyecto $model){
        return [
            'id' => $model->getKey(),
            'nombre' => $model->Nombre,
            'obra' => $model->obra_sao,
            'cantidad_limite_extraordinarias' => $model->CantidadExtraordinariasPermitidas,
        ];
    }

    /**
     * @param Proyecto $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Proyecto $model){
        if($item = $model->empresa)
        {
            return $this->item($item, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param Proyecto $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipo(Proyecto $model){
        if($item = $model->tipo)
        {
            return $this->item($item, new TipoProyectoTransformer);
        }
        return null;
    }
}
