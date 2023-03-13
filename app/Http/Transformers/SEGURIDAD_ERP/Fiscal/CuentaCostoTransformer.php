<?php
namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\InformeCostoVsCFDI\CuentaCosto;
use League\Fractal\TransformerAbstract;
class CuentaCostoTransformer extends TransformerAbstract
{
    public function transform(CuentaCosto $model)
    {
        return [
            'id' => $model->getKey(),
            'base_datos_contpaq' => $model->base_datos_contpaq,
            'id_cuenta' => $model->id_cuenta,
            'codigo_cuenta' => $model->codigo_cuenta,
            'nombre_cuenta' => $model->nombre_cuenta,
            'tipo_costo' => $model->tipo_costo,
        ];
    }
}
