<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 07:04 PM
 */

namespace App\Http\Transformers\CADECO\Catalogos;


use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Catalogos\UnificacionProveedores;

class UnificacionProveedoreTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
    ];


    public function transform(UnificacionProveedores $model) {
        return [
            'id' => (int) $model->getKey(),
        ];
    }

}
