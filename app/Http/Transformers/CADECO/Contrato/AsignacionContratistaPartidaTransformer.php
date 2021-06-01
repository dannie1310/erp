<?php


namespace App\Http\Transformers\CADECO\Contrato;


use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Models\CADECO\Subcontratos\AsignacionContratistaPartida;
use League\Fractal\TransformerAbstract;

class AsignacionContratistaPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'concepto'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(AsignacionContratistaPartida $model)
    {
        return [
            'id' => $model->getKey(),
            'cantidad_asignada' => $model->cantidad_asignada,
            'cantidad_autorizada' => $model->cantidad_autorizada,
            'id_transaccion_presupuesto' => $model->id_transaccion,
            'id_contrato' => $model->id_concepto,
            'importe_asignado' => $model->importe_asignado,
            'importe_asignado_format' => $model->importe_asignado_format,
        ];
    }

    /**
     * @param AsignacionContratistaPartida $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeConcepto(AsignacionContratistaPartida $model)
    {
        if ($concepto = $model->contrato)
        {
            return $this->item($concepto, new ContratoTransformer);
        }
        return null;
    }
}
