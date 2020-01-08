<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 03/01/2020
 * Time: 01:42 PM
 */

namespace App\Http\Transformers\CADECO;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\ProveedorContratista;

class ProveedorContratistaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    public function transform(ProveedorContratista $model)
    {
        return [
            'id' => $model->getKey(),
            'tipo_empresa'=> $model->tipo_empresa,
            'razon_social' => $model->razon_social,
            'tipo' => $model->tipo_proveedor_contratista,
            'rfc' => $model->rfc,
        ];
    }
}