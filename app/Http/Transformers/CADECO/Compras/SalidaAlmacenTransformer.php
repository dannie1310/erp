<?php


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Models\CADECO\SalidaAlmacen;
use League\Fractal\TransformerAbstract;
use PhpParser\Node\Scalar\String_;

class SalidaAlmacenTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'almacen',
        'partidas'
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
            'operacion' => $model->operacion,
            'opciones' => $model->opciones,
            'folio_format' => $model->numero_folio_format_orden
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

}