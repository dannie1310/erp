<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/01/2019
 * Time: 12:33 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Models\CADECO\Contabilidad\CuentaEmpresa;
use League\Fractal\TransformerAbstract;

class CuentaEmpresaTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'empresa',
        'tipo'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'empresa',
        'tipo'
    ];

    public function transform(CuentaEmpresa $model) {
        return [
            'id' => (int) $model->getKey(),
            'cuenta' => (string) $model->cuenta
        ];
    }

    /**
     * Include Empresa
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeEmpresa(CuentaEmpresa $model)
    {
        $empresa = $model->empresa;

        return $this->item($empresa, new EmpresaTransformer);
    }

    /**
     * Include TipoCuentaEmpresa
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipo(CuentaEmpresa $model){
        $tipo = $model->tipoCuentaEmpresa;
        return $this->item($tipo, new TipoCuentaEmpresaTransformer);
    }
}