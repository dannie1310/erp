<?php


namespace App\Http\Transformers\CADECO\SubcontratosCM;

use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Models\CADECO\SubcontratosCM\Partida;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\Contrato\SubcontratoPartidaTransformer as ItemSubcontratoTransformer;

class PartidaTransformer extends TransformerAbstract
{
    /**
     * @var string[]
     */
    protected $availableIncludes = [
        'tipo',
        'item_subcontrato',
        'concepto',
        'item_subcontrato'
    ];

    public function transform(Partida $model)
    {
        return [
            'id' => $model->getKey(),
            'clave' => $model->clave,
            'descripcion' => $model->descripcion,
            'unidad' => $model->unidad,
            'cantidad' => $model->cantidad,
            'precio' => $model->precio,
            'importe' => $model->importe,
            'cantidad_format' => $model->cantidad_format,
            'precio_format' => $model->precio_format,
            'importe_format' => $model->importe_format,
            'concepto_path' => $model->concepto_path,
            'concepto_path_corta' => $model->concepto_path_corta,
        ];
    }

    public function includeTipo(Partida $model) {
        if ($tipo = $model->tipo) {
            return $this->item($tipo, new CtgTipoTransformer);
        }
        return null;
    }

    public function includeItemSubcontrato(Partida $model) {
        if ($itemSubcontrato = $model->itemSubcontrato) {
            return $this->item($itemSubcontrato, new ItemSubcontratoTransformer);
        }
        return null;
    }

    public function includeConcepto(Partida $model) {
        if ($concepto = $model->concepto) {
            return $this->item($concepto, new ContratoTransformer);
        }
        return null;
    }
}
