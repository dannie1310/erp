<?php


namespace App\Http\Transformers\CTPQ;


use Exception;
use App\Models\CTPQ\Cuenta;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\CuentaProveedorSatTransformer;

class CuentaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuenta_contpaq_proveedor_sat',
    ];

    protected $defaultIncludes = [
        'cuenta_contpaq_proveedor_sat',
    ];

    public function transform(Cuenta $model) {
        return [
            'id' => (int) $model->getKey(),
            'cuenta' => $model->Codigo,
            'descripcion' => $model->Nombre,
            'cuenta_format' =>$model->cuenta_completa_format,
            'requiere_proveedor' => $model->requiere_proveedor,
        ];
    }

    public function includeCuentaContpaqProveedorSat(Cuenta $cuenta)
    {
        if($item = $cuenta->cuentaContpaqProvedorSat)
        {
            return $this->item($item, new CuentaProveedorSatTransformer);
        }
        return null;
    }
}
