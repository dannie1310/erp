<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 15/07/2020
 * Time: 03:22 PM
 */

namespace App\Http\Transformers\CADECO\Contrato;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Subcontratos\AsignacionContratista;
use App\Http\Transformers\CADECO\Contrato\ContratoProyectadoTransformer;

class AsignacionContratistaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'contrato'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(AsignacionContratista $model)
    {
        return [
            'id' => $model->getKey(),
        ];
    }

    /**
     * @param AsignacionContratista $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeContrato(AsignacionContratista $model)
    {
        if($contrato = $model->contratoProyectado)
        {
            return $this->item($contrato, new ContratoProyectadoTransformer);
        }
        return null;
    }

}
