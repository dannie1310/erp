<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 12:13 PM
 */

namespace App\Http\Transformers\MODULOSSAO\ControlRemesas;


use App\Http\Transformers\MODULOSSAO\Proyectos\ProyectosTransformer;
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
        'documento',
        'proyecto'
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
            'folio' => $model->Folio
        ];
    }

    /**
     * @param Remesa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeDocumento(Remesa $model)
    {
        if($documento = $model->documento){
            return $this->collection($documento, new DocumentoTransformer);
        }
        return null;
    }

    /**
     * @param Remesa $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeProyecto(Remesa $model)
    {
        if($proyecto = $model->proyecto){
            return $this->item($proyecto, new ProyectosTransformer);
        }
        return null;
    }
}