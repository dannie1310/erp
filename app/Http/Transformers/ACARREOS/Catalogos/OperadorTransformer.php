<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\Operador;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\ACARREOS\Catalogos\OperadorHistoricoTransformer;

class OperadorTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'historicos'
    ];


    public function transform(Operador $model) {
        return [
            'id' => (int) $model->getKey(),
            'clave_format' => '#'.$model->getKey(),
            'nombre' => (string) $model->Nombre,
            'direccion' => (string) $model->Direccion,
            'no_licencia' => (string) $model->NoLicencia,
            'licencia_vigencia_format' => (string) $model->licencia_vigencia_format,
            'vigencia_licencia' => $model->vigencia_licencia_format,
            'usuario_registro' => $model->nombre_usuario,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado,
            'estado' => $model->Estatus,
        ];
    }

     /**
     * @param Operador $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeHistoricos(Operador $model)
    {
        if($historicos = $model->historicos)
        {
            return $this->collection($historicos, new OperadorHistoricoTransformer);
        }
        return null;
    }
}
