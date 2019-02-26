<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 2/25/19
 * Time: 12:53 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Contabilidad\CuentaConceptoTransformer;
use App\Models\CADECO\Concepto;
use League\Fractal\TransformerAbstract;

class ConceptoTransformer extends TransformerAbstract
{
    public function transform(Concepto $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'unidad' => $model->unidad,
        ];
    }

    public function includeCuentaConcepto(Concepto $model)
    {
        if ($cuenta = $model->cuentaConcepto) {
            return $this->item($cuenta, new CuentaConceptoTransformer);
        }
        return null;
    }
}