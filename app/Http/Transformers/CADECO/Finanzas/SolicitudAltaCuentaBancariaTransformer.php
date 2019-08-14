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
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgPlazaTransformer;
use App\Models\CADECO\FinanzasCBE\SolicitudAlta;
use League\Fractal\TransformerAbstract;

class SolicitudAltaCuentaBancariaTransformer extends TransformerAbstract
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
        'usuario',
        'movimientos',
        'movimiento_solicitud'

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo',
        'empresa',
        'moneda',
        'banco',
        'plaza',
        'usuario',
        'movimientos',
        'movimiento_solicitud'
    ];

    public function transform(SolicitudAlta $model)
    {
        return [
            'id' => $model->getKey(),
            'cuenta' => $model->cuenta_clabe,
            'sucursal' => $model->sucursal,
            'tipos' => $model->tipos_cuentas,
            'tipo_cuenta' => $model->tipo_cuenta,
            'fecha' => $model->fecha,
            'observaciones' => $model->observaciones,
            'fecha_format' => $model->fecha_format,
            'estatus' => $model->movimientoSolicitud->estado_resultante_desc,
            'estado' => $model->estado,
            'folio' => $model->numero_folio,
            'numero_folio_format_orden' => $model->numero_folio_format_orden,
            'sucursal_format' => $model->sucursal_format
        ];
    }

    /**
     * @param SolicitudAlta $model
     * Include Tipo de SolicitudAlta
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipo(SolicitudAlta $model)
    {
        if($tipo = $model->tipoSolicitud)
        {
            return $this->item($tipo, new CtgTipoSolicitudTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudAlta $model
     * Include Empresa
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(SolicitudAlta $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudAlta $model
     * Include Moneda
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(SolicitudAlta $model)
    {
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudAlta $model
     * Include Banco
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeBanco(SolicitudAlta $model)
    {
        if($banco = $model->banco)
        {
            return $this->item($banco, new BancoTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudAlta $model
     * Include Plaza
     * @return \League\Fractal\Resource\Item|null
     */
    public function includePlaza(SolicitudAlta $model)
    {
        if($plaza = $model->plaza)
        {
            return $this->item($plaza, new CtgPlazaTransformer);
        }
        return null;
    }

    public function includeMovimientoSolicitud(SolicitudAlta $model)
    {
        if($movimiento = $model->movimientoSolicitud)
        {
            return $this->item($movimiento, new CtgTipoMovimientoSolicitudTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudAlta $model
     * Include Usuario
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(SolicitudAlta $model)
    {
        if($usuario = $model->registro)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudAlta $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMovimientos(SolicitudAlta $model)
    {
        if($movimiento_solicitud = $model->movimientos)
        {
            return $this->collection($movimiento_solicitud, new SolicitudMovimientoTransformer);
        }
        return null;
    }
}