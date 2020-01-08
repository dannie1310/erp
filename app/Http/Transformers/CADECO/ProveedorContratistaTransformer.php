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
use App\Http\Transformers\CADECO\SucursalTransformer;
use App\Http\Transformers\CADECO\SuministradosTransformer;

class ProveedorContratistaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'suministrados',
        'sucursales'
    ];

    public function transform(ProveedorContratista $model)
    {
        return [
            'id' => $model->getKey(),
            'tipo_empresa'=> $model->tipo_empresa,
            'razon_social' => $model->razon_social,
            'tipo' => $model->tipo,
            'rfc' => $model->rfc,
            'proveedor_virtual' => $model->no_proveedor_virtual,
            'dias_credito' => $model->dias_credito,
            'porcentaje' => $model->porcentaje
        ];
    }

    /**
     * @param ProveedorContratista $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeSuministrados(ProveedorContratista $model) {
        if ($suministrados = $model->suministrados) {
            return $this->collection($suministrados, new SuministradosTransformer);
        }
        return null;
    }

    /**
     * @param ProveedorContratista $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeSucursales(ProveedorContratista $model){
        if($sucursales = $model->sucursales){
            return $this->collection($sucursales, new SucursalTransformer);
        }
        return null;
    }
}