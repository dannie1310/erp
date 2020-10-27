<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 15/07/2020
 * Time: 03:22 PM
 */

namespace App\Http\Transformers\CADECO\Contrato;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Subcontratos\AsignacionContratista;
use App\Http\Transformers\CADECO\Contrato\ContratoProyectadoTransformer;
use App\Http\Transformers\CADECO\Contrato\AsignacionSubcontratoTransformer;

class AsignacionContratistaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'contrato',
        'asignacionEstimacion',
        'partidas'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(AsignacionContratista $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_folio' => $model->numero_folio_format,
            'fecha_format' => $model->fecha_registro_format,
            'usuario' => $model->Usuario_registro_nombre,
            'estado' => $model->estado
        ];
    }

    /**
     * @param AsignacionContratista $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeContrato(AsignacionContratista $model)
    {
        if($contrato = $model->contratoProyectado)
        {
            return $this->item($contrato, new ContratoProyectadoTransformer);
        }
        return null;
    }

    /**
     * @param AsignacionContratista $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAsignacionEstimacion(AsignacionContratista $model)
    {
        if($asignacion_subcontrato = $model->asignacionSubcontrato)
        {
            return $this->item($asignacion_subcontrato, new AsignacionSubcontratoTransformer);
        }
        return null;
    }

    /**
     * @param AsignacionContratista $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(AsignacionContratista $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new AsignacionContratistaPartidaTransformer);
        }
        return null;
    }
}
