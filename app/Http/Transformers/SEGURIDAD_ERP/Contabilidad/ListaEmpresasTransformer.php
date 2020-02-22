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
        
    ];

    public function transform(Empresa $model) {
        return [
            'id' => (int) $model->Id,
            'nombre' => $model->Nombre,
            'alias' => $model->AliasBDD,
            'visible' => $model->Visible?(int)$model->Visible:0,
            'editable' => $model->Editable?(int)$model->Editable:0,
        ];
    }
}