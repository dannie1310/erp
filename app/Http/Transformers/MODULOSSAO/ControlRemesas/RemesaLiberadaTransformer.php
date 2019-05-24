<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 12:05 PM
 */

namespace App\Http\Transformers\MODULOSSAO\ControlRemesas;


use App\Models\MODULOSSAO\ControlRemesas\RemesaLiberada;
use League\Fractal\TransformerAbstract;

class RemesaLiberadaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'remesa'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(RemesaLiberada $model){
        return [
            'id' => $model->getKey(),
            'fecha' => $model->FechaHoraLiberacion
        ];
    }

    /**
     * @param RemesaLiberada $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeRemesa(RemesaLiberada $model)
    {
        if($remesa = $model->remesa){
            return $this->item($remesa, new RemesaTransformer);
        }
        return null;
    }
}