<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\Camion;
use League\Fractal\TransformerAbstract;

class CamionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];


    public function transform(Camion $model) {
        return [
            'id' => (int) $model->getKey(),
            'propietario' => $model->Propietario,
            'operador' => $model->nombre_operador,
            'placas' => $model->Placas,
            'placas_caja' => (string) $model->PlacasCaja,
            'economico' => $model->Economico,
            'marca' => $model->descripcion_marca,
            'modelo' => $model->modelo,
            'poliza_seguro' => $model->PolizaSeguro,
            'vigencia_poliza' => $model->VigenciaPolizaSeguro,
            'aseguradora' => $model->Aseguradora,
            'ancho' => $model->Ancho,
            'largo' => $model->Largo,
            'alto' => $model->Alto,
            'altura_extension' => $model->AlturaExtension,
            'estapacio_gato' => $model->EspacioDeGato,
            'disminucion' => $model->Disminucion,
            'cubicacion_real' => $model->CubicacionReal,
            'cubicacion_pago' => $model->CubicacionParaPago,
            'fecha_registro' => $model->fecha_registro,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'estado_color' => $model->color_estado,
            'nombre_registro' => (string) $model->nombre_registro,
            'nombre_desactivo' => (string) $model->nombre_desactivo,
            'motivo' => $model->motivo
        ];
    }
}
