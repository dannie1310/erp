<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 04:09 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\TransaccionRubro;
use League\Fractal\TransformerAbstract;

class TransaccionRubroTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'rubro'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'rubro'
    ];

    public function includeRubro(TransaccionRubro $model)
    {
        if ($rubro = $model->rubro) {
            return $this->item($rubro, new RubroTransformer);
        }
        return null;
    }
}