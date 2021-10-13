<?php


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\Almacenes\EntregaContratistaTransformer;
use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Models\CADECO\SalidaAlmacen;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\Almacenes\SalidaAlmacenTransaccionesRelacionadasTransformer;

class SalidaAlmacenTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'almacen',
        'partidas',
        'entrega_contratista',
        'transacciones_relacionadas'
    ];

    public function transform(SalidaAlmacen $model) {
        return [
            'id' => (int) $model->getKey(),
            'fecha' => $model->fecha,
            'fecha_format' => $model->fecha_format,
            'referencia' => (string) $model->referencia,
            'observaciones' => (string) $model->observaciones,
            'estado' => $model->estado,
            'estado_format' => $model->estado_format,
            'folio' => $model->numero_folio,
            'opciones' => $model->opciones,
            'operacion' => $model->operacion,
            'folio_format' => $model->numero_folio_format,
            'almacen_descripcion' => $model->almacen->descripcion,
            'id_empresa_entrega' => $model->id_empresa,
            'tipo_cargo' => $model->tipo_cargo,
        ];
    }

    public function includeAlmacen(SalidaAlmacen $model)
    {
        if($almacen = $model->almacen)
        {
            return $this->item($almacen, new AlmacenTransformer);
        }
        return null;
    }

    public function includePartidas(SalidaAlmacen $model)
    {
        if($partida = $model->partidas)
        {
            return $this->collection($partida, new SalidaAlmacenPartidaTransformer);
        }
        return null;
    }

    public function includeEntregaContratista(SalidaAlmacen $model)
    {
        if($entrega_contratista = $model->entrega_contratista){
            return $this->item($entrega_contratista, new EntregaContratistaTransformer);
        }
    }

    public function includeTransaccionesRelacionadas(SalidaAlmacen $model)
    {
        if ($partida = $model->transacciones_relacionadas) {
            return $this->item($partida, new SalidaAlmacenTransaccionesRelacionadasTransformer());
        }
        return null;
    }
}
