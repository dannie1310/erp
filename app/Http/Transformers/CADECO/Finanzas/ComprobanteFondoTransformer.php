<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\ConceptoTransformer;
use App\Http\Transformers\CADECO\FondoTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\ComprobanteFondo;
use League\Fractal\TransformerAbstract;

class ComprobanteFondoTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
        'concepto',
        'partidas',
    ];

    /**
     * @var array
     */
    protected $defaultIncludes = [
        'fondo',
    ];

    public function transform(ComprobanteFondo $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_folio_format' => $model->numero_folio_format,
            'referencia'=>(string)$model->referencia,
            'observaciones'=>(string)$model->observaciones,
            'fecha' => (string)$model->fecha,
            'fecha_format' => (string)$model->fecha_format,
            'estado' => (int)$model->estado,
            'total_format' => $model->total_format,
            'fecha_registro' => $model->fecha_hora_registro_format,
            'usuario_registro' => $model->usuario_registro,
            'monto_format' => $model->monto_format,
            'impuesto_format' => $model->impuesto_format
        ];
    }

    /**
     * @param ComprobanteFondo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeFondo(ComprobanteFondo $model)
    {
        if($fondo = $model->fondo)
        {
            return $this->item($fondo, new FondoTransformer);
        }
        return null;
    }

    /**
     * @param ComprobanteFondo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeConcepto(ComprobanteFondo $model)
    {
        if($concepto =  $model->concepto)
        {
            return $this->item($concepto, new ConceptoTransformer);
        }
        return null;
    }

    /**
     * @param ComprobanteFondo $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(ComprobanteFondo $model)
    {
        if($partidas = $model->partidasOrdenadas)
        {
            return $this->collection($partidas, new ComprobanteFondoPartidaTransformer);
        }
        return null;
    }
}
