<?php


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Http\Transformers\CADECO\Compras\SolicitudComplementoTransformer;
use App\Http\Transformers\CADECO\Compras\CotizacionCompraTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\SolicitudCompra;
use League\Fractal\TransformerAbstract;

class SolicitudCompraTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'complemento',
        'partidas',
        'usuario',
        'cotizaciones',
        'relaciones'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'usuario'
    ];

    public function transform(SolicitudCompra $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_folio' => $model->numero_folio,
            'fecha' => $model->fecha,
            'estado' => (int) $model->estado,
            'estado_solicitud' => $model->complemento ? $model->complemento->estadoSolicitud->descripcion:'',
            'fecha_format'=>$model->fecha_format,
            'fecha_registro'=>$model->fecha_hora_registro_format,
            'observaciones' => $model->observaciones,
            'concepto' => $model->complemento ? $model->complemento->concepto : '',
            'numero_folio_compuesto' =>$model->complemento ? $model->complemento->folio_compuesto:'',
            'area_compradora' =>$model->area_compradora,
            'area_solicitante' =>$model->area_solicitante,
            'numero_folio_format'=>(string) $model->numero_folio_format,
            'cotizaciones' => $model->cotizaciones ? $model->cotizaciones->count() : null,
            'autorizacion_requerida' => $model->obra->configuracionCompras ? $model->obra->configuracionCompras->con_autorizacion:"0",
            'direccion_entrega' => $model->obra->direccion_proyecto,
            'ubicacion_entrega_plataforma_digital' => $model->obra->direccion_plataforma_digital
        ];
    }


    /**
     * @param SolicitudCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeComplemento(SolicitudCompra $model)
    {
        if($complemento =$model->complemento)
        {
            return $this->item($complemento, new SolicitudComplementoTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudCompra$model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(SolicitudCompra $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new SolicitudCompraPartidaTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudCompra $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCotizaciones(SolicitudCompra $model)
    {
        if($cotizaciones = $model->cotizaciones)
        {
            return $this->collection($cotizaciones, new CotizacionCompraTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(SolicitudCompra $model)
    {
        if ($usuario = $model->usuario) {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

    public function includeRelaciones(SolicitudCompra $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }
}
