<?php


namespace App\Http\Transformers\CADECO;
use App\Http\Transformers\CADECO\Contrato\SubcontratoPartidaTransformer;
use App\Models\CADECO\Contrato;
use League\Fractal\TransformerAbstract;

class ContratoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'destino',
        'partidas_subcontrato'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Contrato $model)
    {
        return [
            'id_concepto' => $model->getKey(),
            'id_transaccion' => $model->id_transaccion,
            'clave' => $model->clave,
            'nivel' => $model->nivel,
            'descripcion' => $model->descripcion,
            'descripcion_formato' => $model->descripcion_format,
            'unidad' => $model->unidad,
            'cantidad_original' => $model->cantidad_original,
            'cantidad_presupuestada' => $model->cantidad_presupuestada,
            'para_estimar' => $model->para_estimar
        ];
    }

    /**
     * @param Contrato $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDestino(Contrato $model)
    {
        if($destino =  $model->destino)
        {
            return $this->item($destino, new DestinoTransformer);
        }
        return null;
    }

    /**
     * @param Contrato $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includePartidasSubcontrato(Contrato $model)
    {
        if($partida = $model->itemsSubcontrato)
        {
            return $this->item($partida, new SubcontratoPartidaTransformer);
        }
        return null;
    }
}
