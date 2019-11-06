<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 08:08 p. m.
 */


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\SEGURIDAD_ERP\Compras\CtgAreaCompradoraTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Compras\CtgAreaSolicitanteTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Compras\CtgTipoTransformer;
use App\Models\CADECO\Compras\SolicitudComplemento;
use Carbon\Carbon;
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
        'tipo',


    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(SolicitudComplemento $model)
    {
        return [
            'id' => $model->getKey(),
            'id_area_compradora' => $model->id_area_compradora,
            'id_tipo' => $model->id_tipo,
            'id_area_solicitante' => $model->id_area_solicitante,
            'folio' => $model->folio_compuesto,
            'estado' => $model->estado,
            'concepto' => $model->concepto,
            'fecha_requisicion_origen' => Carbon::parse($model->fecha_requisicion_origen)->format('d-m-Y'),
            'requisicion_origen' => $model->requisicion_origen,
            'registro' => $model->registro,
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
            return $this->item($area, new CtgAreaCompradoraTransformer);
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
            return $this->item($area, new CtgAreaSolicitanteTransformer);
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
