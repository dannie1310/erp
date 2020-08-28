<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 15/07/2020
 * Time: 03:22 PM
 */

namespace App\Http\Transformers\CADECO\Contrato;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Subcontratos\AsignacionSubcontrato;
use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;

class AsignacionSubcontratoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subcontrato'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(AsignacionSubcontrato $model)
    {
        return [
            'id' => $model->getKey(),
            'id_transaccion' => $model->id_transaccion
        ];
    }

    /**
     * @param AsignacionSubcontrato $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSubcontrato(AsignacionSubcontrato $model)
    {
        if($subcontrato = $model->subcontrato)
        {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }

}
