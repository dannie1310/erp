<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 08:17 PM
 */

namespace App\Http\Transformers\CADECO\Ventas;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Ventas\VentaCancelacion;
use App\Http\Transformers\IGH\UsuarioTransformer;

class VentaCancelacionTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'usuario'
    ];

    public function transform(VentaCancelacion $model) {
        return [
            'id' => (int) $model->getKey(),
            'fecha_hora_cancelacion_format' => $model->fecha_hora_cancelacion_format,
            'motivo_cancelacion' => (string) $model->motivo,
        ];
    }

    /**
     * @param Venta $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(VentaCancelacion $model)
    {
        if ($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}