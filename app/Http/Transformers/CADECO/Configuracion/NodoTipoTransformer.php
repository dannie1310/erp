<?php


namespace App\Http\Transformers\CADECO\Configuracion;


use App\Models\CADECO\Configuracion\CtgTipoNodo;
use App\Models\CADECO\Configuracion\NodoTipo;
use League\Fractal\TransformerAbstract;

class NodoTipoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'ctg_tipo_nodo'
    ];

    /**
     * List of resources to automatically include 
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(NodoTipo $model)
    {
        return [
            'tipo_nodo' => $model->id_tipo_nodo,
            'concepto' => $model->id_concepto,
            'descripcion_padre' => $model->descripcion_padre
        ];
    }

    /**
     * @param NodoTipo $model
     * @return \League\Fractal\Resource\NodoTipo|null
     */
    public function includeCtgTipoNodo(NodoTipo $model) {
        if ($tipoNodo = $model->tipoNodo) {
            return $this->item($tipoNodo, new CtgTipoNodoTransformer);
        }
        return null;
    }


}
