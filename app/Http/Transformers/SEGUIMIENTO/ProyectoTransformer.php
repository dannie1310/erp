<?php


namespace App\Http\Transformers\SEGUIMIENTO;


use App\Models\REPSEG\GrlProyecto;
use League\Fractal\TransformerAbstract;

class ProyectoTransformer extends TransformerAbstract
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

    public function transform(GrlProyecto $model)
    {
        return [
            'id' => (int)$model->getKey(),
        ];
    }

}
