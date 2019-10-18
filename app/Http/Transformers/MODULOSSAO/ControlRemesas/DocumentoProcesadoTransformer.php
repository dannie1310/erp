<?php


namespace App\Http\Transformers\MODULOSSAO\ControlRemesas;


use App\Models\MODULOSSAO\ControlRemesas\DocumentoProcesado;
use League\Fractal\TransformerAbstract;

class DocumentoProcesadoTransformer extends TransformerAbstract
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
    ];

    public function transform(DocumentoProcesado $model){
        return [
            'id_documento' => $model->IDDocumento,
            'id_proceso' => $model->IDProceso,
            'monto_autorizado_primer_envio' => $model->MontoAutorizadoPrimerEnvio,
            'monto_autorizado_segundo_envio' => $model->MontoAutorizadoSegundoEnvio,
        ];
    }
}
