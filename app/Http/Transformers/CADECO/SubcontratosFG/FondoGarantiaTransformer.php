<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2019
 * Time: 08:54 PM
 */

namespace App\Http\Transformers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use League\Fractal\TransformerAbstract;

class FondoGarantiaTransformer extends TransformerAbstract
{
    public function transform(FondoGarantia $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'fecha' => (string)$model->fecha,
            'saldo' => (string)$model->saldo_format,
            'contratista' => (string)$model->subcontrato->empresa->razon_social,
            'created_at'=>(string)$model->created_at,
            'numero_folio_subcontrato'=>(string)$model->subcontrato->numero_folio_format,
            'fecha_subcontrato'=>(string)$model->subcontrato->fecha_format,
            'monto_subcontrato'=>(string)$model->subcontrato->monto_format,
        ];
    }
}