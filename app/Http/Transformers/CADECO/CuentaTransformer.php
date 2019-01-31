<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 08:02 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Cuenta;
use League\Fractal\TransformerAbstract;

class CuentaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'empresa'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(Cuenta $model)
    {
        return [
            'id' => $model->getKey(),
            'numero' => $model->numero,
            'abreviatura' => $model->abreviatura
        ];
    }

    /**
     * Include Empresa
     *
     * @param Cuenta $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeEmpresa(Cuenta $model)
    {
        if ($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }
}