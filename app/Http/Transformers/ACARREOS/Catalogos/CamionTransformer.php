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
        'imagenes'
    ];


    public function transform(Camion $model) {
        return [
            'id' => (int) $model->getKey(),
            'id_sindicato' => $model->IdSindicato,
            'sindicato' => $model->descripcion_sindicato,
            'id_empresa' => $model->IdEmpresa,
            'empresa' => $model->razon_social_empresa,
            'propietario' => $model->Propietario,
            'id_operador' => $model->IdOperador,
            'operador' => $model->nombre_operador,
            'placa' => $model->Placas,
            'placa_caja' => (string) $model->PlacasCaja,
            'economico' => $model->Economico,
            'id_marca' => $model->IdMarca,
            'marca' => $model->descripcion_marca,
            'modelo' => $model->modelo,
            'poliza_seguro' => $model->PolizaSeguro,
            'vigencia_poliza' => $model->VigenciaPolizaSeguro,
            'aseguradora' => $model->Aseguradora,
            'ancho' => $model->Ancho,
            'largo' => $model->Largo,
            'alto' => $model->Alto,
            'altura_extension' => $model->AlturaExtension,
            'espacio_gato' => $model->EspacioDeGato,
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

    /**
     * @param Camion $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeImagenes(Camion $model)
    {
        if($imagenes = $model->imagenes)
        {
            return $this->collection($imagenes, new CamionImagenTransformer);
        }
        return null;
    }
}
