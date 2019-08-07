<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 09:07 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\BancoTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\CtgPlazaTransformer;
use App\Models\CADECO\FinanzasCBE\Solicitud;
use League\Fractal\TransformerAbstract;

class SolicitudCuentaBancariaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo',
        'empresa',
        'moneda',
        'banco',
        'plaza',
        'usuario'

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Solicitud $model)
    {
        return [
            'id' => $model->getKey(),
            'cuenta' => $model->cuenta_clabe,
            'sucursal' => $model->sucursal,
            'tipo_cuenta' => $model->tipo,
            'fecha' => $model->fecha,
            'observaciones' => $model->observaciones
        ];
    }

    /**
     * @param Solicitud $model
     * Include Tipo de Solicitud
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipo(Solicitud $model)
    {
        if($tipo = $model->tipoSolicitud)
        {
            return $this->item($tipo, new CtgTipoSolicitudTransformer);
        }
        return null;
    }

    /**
     * @param Solicitud $model
     * Include Empresa
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Solicitud $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param Solicitud $model
     * Include Moneda
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(Solicitud $model)
    {
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    /**
     * @param Solicitud $model
     * Include Banco
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeBanco(Solicitud $model)
    {
        if($banco = $model->banco)
        {
            return $this->item($banco, new BancoTransformer);
        }
        return null;
    }

    /**
     * @param Solicitud $model
     * Include Plaza
     * @return \League\Fractal\Resource\Item|null
     */
    public function includePlaza(Solicitud $model)
    {
        if($plaza = $model->plaza)
        {
            return $this->item($plaza, new CtgPlazaTransformer);
        }
        return null;
    }

    /**
     * @param Solicitud $model
     * Include Usuario
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(Solicitud $model)
    {
        if($usuario = $model->registro)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}