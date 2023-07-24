<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/03/2019
 * Time: 06:16 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Contabilidad\DatosContablesTransformer;
use App\Http\Transformers\CADECO\Finanzas\ConfiguracionEstimacionTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\ConfiguracionObraTransformer;
use App\Models\CADECO\Obra;
use League\Fractal\TransformerAbstract;

class ObraTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'datosContables',
        'datosEstimaciones',
        'configuracion'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'datosContables'
    ];

    public function transform(Obra $model)
    {
        return [
            'administrador' => $model->administrador,
            'id_obra' => $model->getKey(),
            'clave' => $model->clave,
            'nombre' => $model->nombre,
            'tipo_obra' => $model->tipo_obra,
            'constructora' => $model->constructora,
            'cliente' => $model->cliente,
            'facturar' => $model->facturar,
            'descripcion' => $model->descripcion,
            'direccion' => $model->direccion,
            'direccion_proyecto' => $model->direccion_proyecto,
            'direccion_plataforma_digital' => $model->direccion_plataforma_digital,
            'ciudad' => $model->ciudad,
            'estado' => $model->estado,
            'codigo_postal' => $model->codigo_postal,
            'fecha_inicial' => $model->fecha_inicial->format('Y-m-d'),
            'fecha_final' => $model->fecha_final->format('Y-m-d'),
            'iva' => $model->iva,
            'id_moneda' => $model->id_moneda,
            'responsable' => $model->responsable,
            'rfc' => $model->rfc,
            'valor_contrato' => $model->valor_contrato,
            'base_datos' => $model->base_datos,
            'base_datos_contpaq' => $model->base_datos_contpaq
        ];
    }

    /**
     * @param Obra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeConfiguracion(Obra $model)
    {
        if ($configuracion = $model->configuracion) {
            return $this->item($configuracion, new ConfiguracionObraTransformer);
        }
        return null;
    }

    /**
     * @param Obra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDatosContables(Obra $model)
    {
        if ($datos = $model->datosContables) {
            return $this->item($datos, new DatosContablesTransformer);
        }
        return null;
    }

    /**
     * @param Obra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDatosEstimaciones(Obra $model)
    {
        if ($estimaciones = $model->datosEstimaciones) {
            return $this->item($estimaciones, new ConfiguracionEstimacionTransformer);
        }
        return null;
    }
}
