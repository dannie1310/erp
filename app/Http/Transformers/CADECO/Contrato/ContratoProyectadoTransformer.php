<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 08:33 PM
 */

namespace App\Http\Transformers\CADECO\Contrato;


use App\Http\Transformers\SEGURIDAD_ERP\TipoAreaSubcontratanteTransformer;
use App\Models\CADECO\ContratoProyectado;
use League\Fractal\TransformerAbstract;

class ContratoProyectadoTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'areasSubcontratantes'
    ];
    public function transform(ContratoProyectado $model)
    {
        return [
            'id' => $model->getKey(),
            'numeroFolio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'fecha' => $model->fecha_format,
            'referencia' => (string)$model->referencia,
        ];
    }

    public function includeAreasSubcontratantes(ContratoProyectado $model)
    {
        if($area_subcontratante = $model->areasSubcontratantes) {
            return $this->collection($area_subcontratante, new TipoAreaSubcontratanteTransformer);
        }
        return null;
    }
}