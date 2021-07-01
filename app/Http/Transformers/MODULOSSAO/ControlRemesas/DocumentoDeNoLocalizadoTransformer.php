<?php


namespace App\Http\Transformers\MODULOSSAO\ControlRemesas;


use App\Models\MODULOSSAO\ControlRemesas\DocumentoDeNoLocalizado;
use League\Fractal\TransformerAbstract;

class DocumentoDeNoLocalizadoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'documento'
    ];

    public function transform(DocumentoDeNoLocalizado $model){
        return [
            'id' => $model->getKey(),
            'fecha' => $model->fecha_aprobacion_format,
            'fecha_rechazo' => $model->fecha_rechazo_format,
            'registro' => $model->nombre_registro,
            'aprobo' => $model->nombre_aprobo,
            'rechazo' => $model->nombre_rechazo,
            'motivo' => $model->motivo_rechazo,
            'estado' => $model->estado,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado
        ];
    }

    /**
     * @param DocumentoDeNoLocalizado $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDocumento(DocumentoDeNoLocalizado $model)
    {
        if($documento = $model->documento)
        {
            return $this->item($documento, new DocumentoTransformer);
        }
        return null;
    }
}
