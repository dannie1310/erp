<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 08:33 PM
 */

namespace App\Http\Transformers\CADECO\Contrato;

use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\TipoAreaSubcontratanteTransformer;
use App\Models\CADECO\ContratoProyectado;
use DateTime;
use League\Fractal\TransformerAbstract;

class ContratoProyectadoTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'areasSubcontratantes',
        'conceptos'
    ];
    public function transform(ContratoProyectado $model)
    {
        return [
            'id' => $model->getKey(),
            'numeroFolio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'fecha' => $model->fecha_format,
            'fecha_date' => $model->fecha,
            'referencia' => (string)$model->referencia,
            'area_subcontratante' => ($model->areaSubcontratante) ? $model->areaSubcontratante->tipoAreaSubcontratante->descripcion : 'Sin Área Subcontratante Asignada',
            'usuario' => ($model->areaSubcontratante) ? $model->areaSubcontratante->nombre_completo : '-------------',
            'cumplimiento' => $model->cumplimiento,
            'vencimiento' => date_format(new DateTime($model->vencimiento), 'Y-m-d')
        ];
    }

    /**
     * @param ContratoProyectado $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeAreasSubcontratantes(ContratoProyectado $model)
    {
        if($area_subcontratante = $model->areasSubcontratantes) {
            return $this->collection($area_subcontratante, new TipoAreaSubcontratanteTransformer);
        }
        return null;
    }

    /**
     * @param ContratoProyectado $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeConceptos(ContratoProyectado $model)
    {
        if($concepto = $model->conceptos)
        {
            return $this->collection($concepto, new ContratoTransformer);
        }
        return null;
    }
}
