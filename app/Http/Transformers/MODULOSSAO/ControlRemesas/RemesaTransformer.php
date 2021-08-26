<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 12:13 PM
 */

namespace App\Http\Transformers\MODULOSSAO\ControlRemesas;

use App\Models\MODULOSSAO\ControlRemesas\Remesa;
use League\Fractal\TransformerAbstract;

class RemesaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'documentos',
        'documentosDisponibles',
        'remesaLiberada'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Remesa $model){
        return [
            'id' => $model->getKey(),
            'año' => $model->Anio,
            'semana' => $model->NumeroSemana,
            'tipo' => $model->tipo,
            'folio' => $model->Folio,
            'proyecto' => $model->IDProyecto,
            'proyecto_descripcion' => $model->nombre_proyecto
        ];
    }

    /**
     * @param Remesa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeDocumentos(Remesa $model)
    {
        if($documento = $model->documento){
            return $this->collection($documento, new DocumentoTransformer);
        }
        return null;
    }

    /**
     * @param Remesa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeDocumentosDisponibles(Remesa $model)
    {
        if($documento = $model->documento()->disponiblesParaDistribuir($model->getKey())->get()){
            return $this->collection($documento, new DocumentoTransformer);
        }
        return null;
    }

    /**
     * @param Remesa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeRemesaLiberada(Remesa $model)
    {
        if($remesa = $model->remesaLiberada){
            return $this->item($remesa, new RemesaLiberadaTransformer);
        }
        return null;
    }
}
