<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\Empresa;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'historicos'
    ];


    public function transform(Empresa $model) {
        return [
            'id' => (int) $model->getKey(),
            'razon_social' => (string) $model->razonSocial,
            'rfc' => $model->RFC,
            'fecha_registro' => $model->fecha_registro,
            'fecha_desactivo' => $model->fecha_desactivacion_format,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado,
            'nombre_registro' => (string) $model->nombre_registro,
            'nombre_desactivo' => (string) $model->nombre_desactivo,
            'motivo' => $model->motivo
        ];
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeHistoricos(Empresa $model)
    {
        if($historicos = $model->historicos)
        {
            return $this->collection($historicos, new EmpresaHistoricoTransformer);
        }
        return null;
    }
}
