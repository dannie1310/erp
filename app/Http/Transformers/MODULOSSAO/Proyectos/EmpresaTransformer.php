<?php
namespace App\Http\Transformers\MODULOSSAO\Proyectos;

use App\Models\MODULOSSAO\Proyectos\Empresa;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
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

    public function transform(Empresa $model){
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->Empresa,
        ];
    }
}
