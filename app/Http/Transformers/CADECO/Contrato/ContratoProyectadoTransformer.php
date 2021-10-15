<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 08:33 PM
 */

namespace App\Http\Transformers\CADECO\Contrato;

use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Http\Transformers\Auxiliares\TransaccionRelacionTransformer;
use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\TipoAreaSubcontratanteTransformer;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Transaccion;
use DateTime;
use League\Fractal\TransformerAbstract;

class ContratoProyectadoTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'areasSubcontratantes',
        'conceptos',
        'relaciones',
        'contratos',
        'transaccion'
    ];
    protected $defaultIncludes=["transaccion"];
    public function transform(ContratoProyectado $model)
    {
        return [
            'id' => $model->getKey(),
            'numeroFolio' => $model->numero_folio,
            'tipo_transaccion' => $model->tipo_transaccion,
            'fecha_format' => $model->fecha_format,
            'numero_folio_format' => $model->numero_folio_format,
            'fecha' => $model->fecha_format,
            'fecha_date' => $model->fecha,
            'referencia' => (string)$model->referencia,
            'area_subcontratante' => ($model->areaSubcontratante) ? $model->areaSubcontratante->tipoAreaSubcontratante->descripcion : 'Sin Área Subcontratante Asignada',
            'usuario' => ($model->areaSubcontratante) ? $model->areaSubcontratante->nombre_completo : '-------------',
            'cumplimiento' => $model->cumplimiento,
            'vencimiento' => date_format(new DateTime($model->vencimiento), 'Y-m-d'),
            'observaciones'=>$model->observaciones_format,
            'fecha_hora_registro_format' => $model->fecha_hora_registro_format,
            'usuario_registro' => $model->usuario_registro,
            'direccion_entrega' => $model->obra->direccion_proyecto,
            'ubicacion_entrega_plataforma_digital' => $model->obra->direccion_plataforma_digital,
            'tipo_transaccion' => $model->tipo_transaccion,
            'puede_editar_partidas' => $model->puede_editar_partidas
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

    /**
     * @param ContratoProyectado $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeContratos(ContratoProyectado $model)
    {
        if($items = $model->contratos)
        {
            return $this->collection($items, new ContratoTransformer);
        }
        return null;
    }

    public function includeRelaciones(ContratoProyectado $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }

    public function includeTransaccion(Transaccion $model)
    {
        return $this->item($model, new TransaccionRelacionTransformer);
    }
}
