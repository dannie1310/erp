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
        'documento'
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
            'tipo' => $model->getTipoAttibute(),
            'folio' => $model->Folio,
            'proyecto' => $model->IDProyecto,
            'monto_distribuido' => $model->getdistribucionesAnterioresMonto()
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
}
