<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:57 AM
 */

namespace App\Http\Transformers\CTPQ;


use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\IncidenteIndividualConsolidadaTransformer;
use App\Models\CTPQ\Poliza;
use League\Fractal\TransformerAbstract;

class PolizaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'movimientos_poliza',
        'incidentes_activos',
        'tipo'
    ];

    public function transform(Poliza $model) {
        return [
            'id' => (int) $model->getKey(),
            'concepto' => (string) $model->Concepto,
            'folio' => (string) $model->Folio,
            'ejercicio' => (string) $model->Ejercicio,
            'periodo' => (string) $model->Periodo,
            'fecha' => (string) $model->fecha_format,
            'fecha_completa' => $model->Fecha,
            'cargos' => (string) $model->cargos_format,
            'abonos' => (float) $model->Abonos,
            'tipo' => (string) $model->tipo_poliza->Nombre,
            'monto' => (string) $model->Cargos,
            'monto_format' => (string) $model->cargos_format
        ];
    }

    /**
     * @param Poliza $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMovimientosPoliza(Poliza $model){
        if ($movimientos = $model->movimientos()->orderBy("TipoMovto")->orderBy("Importe","desc")->get()) {
            return $this->collection($movimientos, new PolizaMovimientoTransformer);
        }
        return null;
    }

    /**
     * @param Poliza $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeIncidentesActivos(Poliza $model){
        if ($incidentes = $model->incidentes->activos()->get()) {
            return $this->collection($incidentes, new IncidenteIndividualConsolidadaTransformer);
        }
        return null;
    }

    /**
     * @param Poliza $poliza
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipo(Poliza $poliza)
    {
        if($tipo = $poliza->tipo_poliza)
        {
            return $this->item($tipo, new TipoPolizaTransformer);
        }
        return null;
    }
}
