<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;

use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;

class ListaEmpresasTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'consolida'
    ];

    public function transform(Empresa $model) {
        return [
            'id' => (int) $model->Id,
            'nombre' => $model->Nombre,
            'alias' => $model->AliasBDD,
            'alias_bdd' => $model->AliasBDD,
            'visible' => $model->Visible ? (int) $model->Visible : 0,
            'editable' => $model->Editable ? (int) $model->Editable : 0,
            'historica' => $model->Historica ? (int) $model->Historica : 0,
            'consolidadora' => $model->Consolidadora ? (int) $model->Consolidadora : 0,
            'consolidada' => $model->consolidada,
            'desarrollo' => $model->Desarrollo ? (int) $model->Desarrollo : 0,
            'poliza_cfdi' => $model->SincronizacionPolizasCFDI ? (int) $model->SincronizacionPolizasCFDI : 0,
            'id_empresa_contpaq' => $model->IdEmpresaContpaq,
            'acceso'=>$model->estado_acceso
        ];
    }

    /**
     * @param Empresa $model
     * @return \Leag\League\Fractal\Resource\Collection|null
     */
    public function includeConsolida(Empresa $model)
    {
        if($consolida = $model->consolida)
        {
            return $this->collection($consolida, new ListaEmpresasTransformer);
        }
    }
}
