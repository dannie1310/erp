<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\Fiscal\NoLocalizado;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoTransformer;

class NoLocalizadoTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        
    ];

    protected $availableIncludes = [
        'ctg_no_localizados_registro',
    ];

    public function transform(NoLocalizado $model)
    {
        return [
            'id' => $model->getKey(),
            'rfc' => $model->fecha_hora_registro_format,
            'razon_social' => $model->total_partidas,
            'estado' => $model->cantidad_partidas
        ];
    }

    /**
     * @param NoDeducido $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCtgNoLocalizadosRegistro(NoLocalizado $model)
    {
        if($partida = $model->ctg_no_localizados_registro)
        {
            return $this->item($partida, new CtgNoLocalizadoTransformer);
        }
        return null;
    }

  
}
