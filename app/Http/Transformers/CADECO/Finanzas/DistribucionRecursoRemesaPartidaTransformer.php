<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 04:45 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\MODULOSSAO\ControlRemesas\DocumentoTransformer;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
use League\Fractal\TransformerAbstract;

class DistribucionRecursoRemesaPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'distribucion_recurso',
        'documento_liberado',
        'estado'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(DistribucionRecursoRemesaPartida $model){
        return [
            'id' => $model->getKey(),
            'fecha' => $model->fecha_registro,
            'folio_banco' => $model->folio_partida_bancaria
        ];
    }

    /**
     * @param DistribucionRecursoRemesaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDistribucionRecurso(DistribucionRecursoRemesaPartida $model)
    {
        if($dr = $model->distribucionRecurso){
            return $this->item($dr, new DistribucionRecursoRemesaTransformer);
        }
        return null;
    }

    /**
     * @param DistribucionRecursoRemesaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDocumentoLiberado(DistribucionRecursoRemesaPartida $model)
    {
        if($documento = $model->documentoLiberado){
            return $this->item($documento, new DocumentoTransformer);
        }
        return null;
    }

    /**
     * @param DistribucionRecursoRemesaPartida $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeEstado(DistribucionRecursoRemesaPartida $model)
    {
        if($estado = $model->estado){
            return $this->item($estado, new CtgEstadoDistribucionPartidaTransformer);
        }
        return null;
    }
}