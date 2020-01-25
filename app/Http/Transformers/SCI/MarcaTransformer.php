<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 28/10/2019
 * Time: 08:22 p. m.
 */


namespace App\Http\Transformers\SCI;


use App\Models\SCI\Marca;
use League\Fractal\TransformerAbstract;

class MarcaTransformer extends TransformerAbstract
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
    protected $defaultIncludes = [

    ];

    public function transform(Marca $model)
    {
        return [
          'id' => $model->getKey(),
          'marca' => $model->marca
        ];
    }

}
