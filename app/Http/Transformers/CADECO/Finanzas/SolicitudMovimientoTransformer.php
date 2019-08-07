<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 09:09 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\FinanzasCBE\SolicitudMovimiento;
use League\Fractal\TransformerAbstract;

class SolicitudMovimientoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'solicitud',
        'tipo',
        'movimientoAntecedente',
        'usuario'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(SolicitudMovimiento $model)
    {
        return [
            'id' => $model->getKey(),
            'mac_address' => $model->mac_address,
            'ip' => $model->ip,
            'observaciones' => $model->observaciones,
            'fecha' => $model->fecha
        ];
    }

    /**
     * @param SolicitudMovimiento $model
     * Include Solicitud
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSolicitud(SolicitudMovimiento $model)
    {
        if($solicitud = $model->solicitud)
        {
            return $this->item($solicitud, new SolicitudTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudMovimiento $model
     * Include Tipo de Movimiento
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipo(SolicitudMovimiento $model)
    {
        if($tipo = $model->tipoMovimientoSolicitud)
        {
            return $this->item($tipo, new CtgTipoMovimientoSolicitudTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudMovimiento $model
     * Include Movimiento Antecedente
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMovimientoAntecedente(SolicitudMovimiento $model)
    {
        if($antecedente = $model->movimientoAntecedente)
        {
            return $this->item($antecedente, new SolicitudMovimientoTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudMovimiento $model
     * Include Usuario
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(SolicitudMovimiento $model)
    {
        if($usuario = $model->registro)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}