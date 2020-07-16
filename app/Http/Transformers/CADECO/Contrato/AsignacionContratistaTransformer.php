<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 15/07/2020
 * Time: 03:22 PM
 */

namespace App\Http\Transformers\CADECO\Contratos;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Subcontratos\AsignacionContratista;

class AsignacionContratistaTransformer extends TransformerAbstract
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
    protected $defaultIncludes = [];

    public function transform(AsignacionContratista $model)
    {
        return [
            'id' => $model->getKey(),
        ];
    }

}
