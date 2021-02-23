<?php


namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Finanzas\FacturaTransformer;
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
        'factura'
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
            'numero_folio'=>$model->numero_folio,
            'referencia' => $model->referencia,
            'tipo_transaccion' => $model->tipo_transaccion,
            "id_empresa" => $model->id_empres,
            "id_moneda" => $model->id_moneda,
            "id_usuario" => $model->id_usuario,
            'tipo' => $model->tipo->Descripcion,
            'observaciones'=>(string)$model->observaciones,
        ];

    }

    /**
     * @param OrdenPago $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeFactura(OrdenPago $model)
    {
        if($factura = $model->factura)
        {
            return $this->item($factura, new FacturaTransformer);
        }
        return null;
    }
}
