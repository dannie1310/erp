<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 08:08 p. m.
 */


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\SEGURIDAD_ERP\Compras\CtgTipoTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Compras\TipoAreaCompradoraTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Compras\TipoAreaSolicitanteTransformer;
use App\Models\CADECO\Compras\SolicitudComplemento;
use League\Fractal\TransformerAbstract;

class SolicitudComplementoTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'area_compradora',
        'area_solicitante',
        'tipo'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'area_compradora',
        'area_solicitante',
        'tipo'
    ];

    public function transform(SolicitudComplemento $model)
    {
      return [
            'id' => $model->getKey(),
            'folio' => $model->folio_compuesto,
            'estado' => $model->estado,
            'concepto' => $model->concepto,
            'id_area_compradora' => $model->id_area_compradora,
            'id_tipo' => $model->id_tipo,
            'id_area_solicitante' => $model->id_area_solicitante,
            'fecha_requisicion_origen_format' => $model->fecha_format,
            'fecha_requisicion_origen' => $model->fecha_requisicion_origen,
            'requisicion_origen' => $model->requisicion_origen,
            'fecha_registro' =>$model->timestamp_registro,
        ];
    }

    /**
     * @param SolicitudComplemento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAreaCompradora(SolicitudComplemento $model)
    {

        if($area = $model->area_compradora)
        {
            return $this->item($area, new TipoAreaCompradoraTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudComplemento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAreaSolicitante(SolicitudComplemento $model)
    {
        if($area = $model->area_solicitante)
        {
            return $this->item($area, new TipoAreaSolicitanteTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudComplemento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipo(SolicitudComplemento $model)
    {
        if($tipo = $model->tipo)
        {
            return $this->item($tipo, new CtgTipoTransformer);
        }
        return null;
    }
}
