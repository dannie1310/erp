<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\Fiscal\FechaInhabilSat;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\CtgTipoFechaTransformer;

class FechaInhabilSatTransformer extends TransformerAbstract
{

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo_fecha'
    ];

    protected $availableIncludes = [
        'tipo_fecha',
    ];

    public function transform(FechaInhabilSat $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_format' => $model->fecha_format,
            'usuario_registro_format' => $model->usuario_registro_format,
            'fecha_registro_format' => $model->fecha_registro_format,
        ];
    }

    /**
     * @param FechaInhabilSat $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipoFecha(FechaInhabilSat $model)
    {
        if($fecha = $model->tipo_fecha)
        {
            return $this->item($fecha, new CtgTipoFechaTransformer);
        }
        return null;
    }

}
