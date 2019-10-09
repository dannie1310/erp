<?php


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\OrdenPago;
use League\Fractal\TransformerAbstract;

class OrdenPagoTransformer extends TransformerAbstract
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
    public function transform(OrdenPago $model){

        return [
            'id_transaccion' => $model->id_transaccion,
            'id_antecedente' => $model->id_antecedente,
            'id_referente'=> $model->id_referente,
            'fecha' => $model->fecha,
            'id_obra' => $model->id_obra,
            'monto' => $model->monto,
            'referencia' => $model->referencia,
            'tipo_transaccion' => $model->tipo_transaccion,
            "id_empresa" => $model->id_empres,
            "id_moneda" => $model->id_moneda,
            "id_usuario" => $model->id_usuario
        ];

    }

}
