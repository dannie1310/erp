<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 30/05/2019
 * Time: 05:31 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\BancoTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgPlazaTransformer;
use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use League\Fractal\TransformerAbstract;

class CuentaBancariaEmpresaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'empresa',
        'banco',
        'moneda',
        'plaza',
        'solicitud_alta',
        'solicitud_baja'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(CuentaBancariaEmpresa $model)
    {
        return [
            'id' => $model->getKey(),
            'cuenta' => $model->cuenta_clabe,
            'sucursal' => $model->sucursal,
            'tipo' => (string) $model->tipo,
            'id_tipo' => $model->tipo_cuenta,
            'estado' => (string) $model->estatus,
            'fecha' => $model->fecha_hora_registro,
            'estado_format' => $model->estadoFormat,
            'sucursal_format' => $model->sucursalFormat
        ];
    }

    /**
     * @param CuentaBancariaEmpresa $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(CuentaBancariaEmpresa $model)
    {
        if ($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param CuentaBancariaEmpresa $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeBanco(CuentaBancariaEmpresa $model)
    {
        if ($empresa = $model->banco) {
            return $this->item($empresa, new BancoTransformer);
        }
        return null;
    }

    /**
     * @param CuentaBancariaEmpresa $model
     * Include Moneda
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(CuentaBancariaEmpresa $model)
    {
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    /**
     * @param CuentaBancariaEmpresa $model
     * Include Plaza
     * @return \League\Fractal\Resource\Item|null
     */
    public function includePlaza(CuentaBancariaEmpresa $model)
    {
        if($plaza = $model->plaza)
        {
            return $this->item($plaza, new CtgPlazaTransformer);
        }
        return null;
    }

    /**
     * @param CuentaBancariaEmpresa $model
     * Include Solicitud Alta
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSolicitudAlta(CuentaBancariaEmpresa $model)
    {
        if($alta = $model->solicitudAlta)
        {
            return $this->item($alta, new SolicitudAltaCuentaBancariaTransformer);
        }
        return null;
    }

    /**
     * @param CuentaBancariaEmpresa $model
     * Include Solicitud Baja
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSolicitudBaja(CuentaBancariaEmpresa $model)
    {
        if($baja = $model->solicitudBaja)
        {
            return $this->item($baja, new SolicitudBajaCuentaBancariaTransformer);
        }
        return null;
    }
}