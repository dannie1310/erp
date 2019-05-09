<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 04:11 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\Rubro;
use League\Fractal\TransformerAbstract;

class RubroTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo'
    ];

    public function transform(Rubro $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }

    public function includeTipo (Rubro $model)
    {
        if ($tipo = $model->tipo_rubro) {
            return $this->item($tipo, new TipoRubroTransformer);
        }
        return null;
    }
}