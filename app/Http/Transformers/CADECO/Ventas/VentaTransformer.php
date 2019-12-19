<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 08:17 PM
 */

namespace App\Http\Transformers\CADECO\Ventas;


use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\Venta;
use League\Fractal\TransformerAbstract;

class VentaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'partidas',
        'empresa',
        'usuario',
        'estado'
    ];

    public function transform(Venta $model) {
        return [
            'id' => (int) $model->getKey(),
            'fecha_format' => $model->fecha_format,
            'monto' => (string) $model->monto_format,
            'observaciones' => (string) $model->observaciones,
            'observaciones_format' => (string) $model->observaciones_format,
            'folio' => $model->numero_folio,
            'opciones' => $model->opciones,
            'folio_format' => $model->numero_folio_format,
            'subtotal' => $model->subtotal_format,
            'impuesto' => $model->impuesto_format
        ];
    }

    /**
     * @param Venta $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(Venta $model)
    {
        if($partida = $model->partidas)
        {
            return $this->collection($partida, new VentaPartidaTransformer);
        }
        return null;
    }

    /**
     * @param Venta $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Venta $model)
    {
        if ($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param Venta $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(Venta $model)
    {
        if ($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

    /**
     * @param Venta $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEstado(Venta $model)
    {
        if($estado = $model->estadoVenta)
        {
            return $this->item($estado, new CtgEstadoTransformer);
        }
        return null;
    }
}
