<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/08/2019
 * Time: 10:40 AM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\BancoTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgPlazaTransformer;
use App\Models\CADECO\FinanzasCBE\SolicitudBaja;
use League\Fractal\TransformerAbstract;

class SolicitudBajaCuentaBancariaTransformer extends TransformerAbstract
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
        'mov_estado'
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
        'mov_estado'
    ];

    public function transform(SolicitudBaja $model)
    {
        return [
            'id' => $model->getKey(),
            'cuenta' => $model->cuenta_clabe,
            'sucursal' => $model->sucursal,
            'tipo_cuenta' => $model->tipo,
            'fecha' => $model->fecha,
            'observaciones' => $model->observaciones,
            'estado' => $model->estatus,
            'fecha_format' => $model->fecha_format,
            'estado' => $model->estatus,
            'folio' => $model->numero_folio,
            'numero_folio_format_orden' => $model->numero_folio_format_orden,
            'sucursal_format' => $model->sucursal_format
        ];
    }

    /**
     * @param SolicitudBaja $model
     * Include Tipo de SolicitudBaja
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipo(SolicitudBaja $model)
    {
        if($tipo = $model->tipoSolicitud)
        {
            return $this->item($tipo, new CtgTipoSolicitudTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudBaja $model
     * Include Empresa
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(SolicitudBaja $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudBaja $model
     * Include Moneda
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(SolicitudBaja $model)
    {
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudBaja $model
     * Include Banco
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeBanco(SolicitudBaja $model)
    {
        if($banco = $model->banco)
        {
            return $this->item($banco, new BancoTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudBaja $model
     * Include Plaza
     * @return \League\Fractal\Resource\Item|null
     */
    public function includePlaza(SolicitudBaja $model)
    {
        if($plaza = $model->plaza)
        {
            return $this->item($plaza, new CtgPlazaTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudBaja $model
     * Include Usuario
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(SolicitudBaja $model)
    {
        if($usuario = $model->registro)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudBaja $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMovEstado(SolicitudBaja $model)
    {
        if($movimiento = $model->movimientoSolicitud)
        {
            return $this->item($movimiento, new CtgTipoMovimientoSolicitudTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudBaja $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMovimientos(SolicitudBaja $model)
    {
        if($movimiento_solicitud = $model->movimientos)
        {
            return $this->collection($movimiento_solicitud, new SolicitudMovimientoTransformer);
        }
        return null;
    }
}