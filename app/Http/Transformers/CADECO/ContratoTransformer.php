<?php


namespace App\Http\Transformers\CADECO;
use App\Http\Transformers\CADECO\Contrato\PresupuestoContratistaPartidaTransformer;
use App\Http\Transformers\CADECO\Contrato\SubcontratoPartidaTransformer;
use App\Models\CADECO\Contrato;
use League\Fractal\ParamBag;
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
        'partidas_subcontrato',
        'presupuesto',
        'hijos'
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
            'id' => $model->getKey(),
            'id_concepto' => $model->getKey(),
            'id_transaccion' => $model->id_transaccion,
            'clave' => $model->clave,
            'clave_contrato_select' => $model->clave_contrato_select,
            'nivel' => $model->nivel,
            'descripcion' => $model->descripcion,
            'descripcion_formato' => $model->descripcion_format,
            'tiene_hijos' => $model->tieneHijos,
            'unidad' => $model->unidad,
            'cantidad_original' => $model->cantidad_original,
            'cantidad_original_format' => $model->cantidad_original_format,
            'cantidad_presupuestada' => $model->cantidad_presupuestada,
            'cantidad_presupuestada_format' => $model->cantidad_presupuestada_format,
            'para_estimar' => $model->para_estimar,
            'es_hoja' => $model->es_hoja
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

    public function includePresupuestos(Contrato $model)
    {
        if($partidas = $model->partidasPresupuesto)
        {
            return $this->collect($partidas, new PresupuestoContratistaPartidaTransformer);
        }
        return null;
    }

    public function includePresupuesto(Contrato $model, ParamBag $params = null)
    {
        $id_transaccion_presupuesto = $params["id_transaccion_presupuesto"][0];
        if($partida = $model->partidasPresupuesto->where("id_transaccion", $id_transaccion_presupuesto)->first())
        {
            return $this->item($partida, new PresupuestoContratistaPartidaTransformer);
        }
        return null;
    }

    public function includeHijos(Contrato $model)
    {
        if ($hijos = $model->hijos) {
            return $this->collection($hijos, new ContratoTransformer);
        }
        return null;
    }
}
