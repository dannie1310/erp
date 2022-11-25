<?php


namespace App\Http\Transformers\SEGUIMIENTO;


use App\Models\REPSEG\FinFacIngresoFactura;
use League\Fractal\TransformerAbstract;

class FacturaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'conceptos',
        'partidas',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(FinFacIngresoFactura $model) {
        return [
            'id' => (int) $model->getKey(),
            'nombre_proyecto' => $model->nombre_proyecto,
            'nombre_cliente' => $model->nombre_cliente,
            'nombre_empresa' => $model->nombre_empresa,
            'nombre_moneda' => $model->nombre_moneda,
            'numero' => $model->numero,
            'fecha_format' => $model->fecha_format,
            'fecha_registro_format' => $model->fecha_registro_format,
            'fecha_cobro_format' => $model->fecha_cobro_format,
            'fecha_fi_format' => $model->fecha_fi_format,
            'fecha_ff_format' => $model->fecha_ff_format,
            'importe_format' => $model->importe_format,
            'descripcion' => $model->descripcion,
            'tipo_cambio' => $model->tipo_cambio,
            'estado' => $model->estado,
            'estado_descripcion' => $model->estado_descripcion,
            'estado_color' => $model->estado_color,
            'subtotal_format' => $model->subtotal_format,
            'iva_format' => $model->iva_format,
            'total_format' => $model->total_format
        ];
    }

    /**
     * @param FinFacIngresoFactura $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeConceptos(FinFacIngresoFactura $model)
    {
        if($conceptos = $model->conceptos)
        {
            return $this->collection($conceptos, new FacturaConceptoTransformer);
        }
        return null;
    }

    /**
     * @param FinFacIngresoFactura $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(FinFacIngresoFactura $model)
    {
        if($partidas = $model->partidasSinTotales)
        {
            return $this->collection($partidas, new FacturaDetalleTransformer);
        }
        return null;
    }
}
