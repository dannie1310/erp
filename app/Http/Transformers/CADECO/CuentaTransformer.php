<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 08:02 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Contabilidad\CuentaBancoTransformer;
use App\Http\Transformers\CADECO\Finanzas\CtgTipoCuentaObraTransformer;
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
        'empresa',
        'cuentasBanco',
        'moneda',
        'tiposCuentasObra'
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
            'empresa' => $model->id_empresa,
            'numero' => $model->numero,
            'saldo' => $model->saldo_inicial_format,
            'saldo_real' => $model->saldo_real_format,
            'fecha' => $model->fecha_format,
            'chequera' => (int)$model->chequera,
            'tipo_cuentas_obra' => $model->id_tipo_cuentas_obra,
            'abreviatura' => $model->abreviatura,
            'saldo_format_cadeco' => $model->saldo_format_cadeco
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

    /**
     * Include CuentasBanco
     * @param Cuenta $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeCuentasBanco(Cuenta $model){
        if ($cuentas = $model->cuentasBanco) {
            return $this->collection($cuentas, new CuentaBancoTransformer);
        }
        return null;
    }

    /**
     * Include Monedas
     * @param Cuenta $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(cuenta $model){
        if($moneda = $model->moneda){
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    public function includeTiposCuentasObra(cuenta $model){
        if($tipos_cuentas_obra = $model->tiposCuentasObra){
            return $this->item($tipos_cuentas_obra, new CtgTipoCuentaObraTransformer);
        }
    }

}
