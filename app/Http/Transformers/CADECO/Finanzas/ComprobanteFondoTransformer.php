<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\FondoTransformer;
use App\Models\CADECO\ComprobanteFondo;
use League\Fractal\TransformerAbstract;

class ComprobanteFondoTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * @var array
     */
    protected $defaultIncludes = [
        'fondo'
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
            'estado_format'=>$model->estado_string,
            'estado' => (int)$model->estado,
            'total_format' => $model->total_format
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
}
