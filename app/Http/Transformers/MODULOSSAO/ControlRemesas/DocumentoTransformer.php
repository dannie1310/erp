<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 12:53 PM
 */

namespace App\Http\Transformers\MODULOSSAO\ControlRemesas;


use App\Models\MODULOSSAO\ControlRemesas\Documento;
use League\Fractal\TransformerAbstract;

class DocumentoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'remesa',
        'documento_liberado'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Documento $model){
        return [
            'id' => $model->getKey(),
            'referencia' => $model->Referencia
        ];
    }

    /**
     * @param Documento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeRemesa(Documento $model)
    {
        if($remesa = $model->remesa){
            return $this->item($remesa, new RemesaTransformer);
        }
        return null;
    }

    /**
     * @param Documento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDocumentoLiberado(Documento $model)
    {
        if($documento = $model->documentoLiberado)
        {
            return $this->item($documento, new DocumentoLiberadoTransformer);
        }
        return null;
    }
}