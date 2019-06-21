<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 01:27 PM
 */

namespace App\Http\Transformers\MODULOSSAO\ControlRemesas;


use App\Http\Transformers\CADECO\Finanzas\DistribucionRecursoRemesaPartidaTransformer;
use App\Models\MODULOSSAO\ControlRemesas\DocumentoLiberado;
use League\Fractal\TransformerAbstract;

class DocumentoLiberadoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'documento',
        'distribucion_partida'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(DocumentoLiberado $model){
        return [
            'id' => $model->getKey(),
        ];
    }

    /**
     * @param DocumentoLiberado $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDocumento(DocumentoLiberado $model){
        if($documento = $model->documento)
        {
            return $this->item($documento, new DocumentoTransformer);
        }
        return null;
    }

    /**
     * @param DocumentoLiberado $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDistribucionPartida(DocumentoLiberado $model){
        if($partida = $model->partidas)
        {
            return $this->item($partida, new DistribucionRecursoRemesaPartidaTransformer);
        }
        return null;
    }
}