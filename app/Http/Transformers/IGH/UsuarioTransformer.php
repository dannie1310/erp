<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/02/2019
 * Time: 02:13 PM
 */

namespace App\Http\Transformers\IGH;

use App\Models\IGH\Usuario;
use League\Fractal\TransformerAbstract;

class UsuarioTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'departamento',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Usuario $model) {

        return [
            'id' => (int) $model->getKey(),
            'nombre' => $model->getNombreCompletoAttribute(),
            'usuario' => $model->usuario,
            'idUbicacion' => $model->idubicacion
        ];
    }

    /**
     * @param Usuario $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDepartamento(Usuario $model)
    {
        if($depa = $model->departamento)
        {
            return $this->item($depa, new DepartamentoTransformer);
        }
        return null;
    }
}
