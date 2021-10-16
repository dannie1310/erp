<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\Contabilidad\CuentaContpaqProvedorSat;

class CuentaProveedorSatTransformer extends TransformerAbstract
{

    public function transform(CuentaContpaqProvedorSat $model) {
        return [
            'id' => (int) $model->id,
            'id_cuenta_contpaq' => $model->id_cuenta_contpaq,
            'id_proveedor_sat' => $model->id_proveedor_sat,
            'id_empresa_contpaq' => $model->id_empresa_contpaq,
            'rfc_proveedor_sat' => $model->proveedor_sat_rfc,
            'razon_social_proveedor_sat' => $model->proveedor_sat_razon_social,
        ];
    }

}
