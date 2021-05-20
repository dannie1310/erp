<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use League\Fractal\TransformerAbstract;
use App\Models\ACARREOS\ImpresoraHistorico;

class ImpresoraHistoricoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];


    public function transform(ImpresoraHistorico $model) {
        return [
            'id' => (int) $model->getKey(),
            'mac' => (string) $model->mac,
            'marca' => (string) $model->marca,
            'modelo' => (string) $model->modelo,
            'registro' => (string) $model->nombre_registro,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
            'desactivo' => (string) $model->nombre_desactivo,
            'fecha_desactivo_format' => $model->fecha_desactivo_format,
            'motivo' => $model->Motivo
        ];
    }
}
