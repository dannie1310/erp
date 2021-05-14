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
        'ctg_nolocalizados',
    ];

    public function transform(NoLocalizado $model)
    {
        return [
            'id' => $model->getKey(),
            'rfc' => $model->rfc,
            'razon_social' => $model->razon_social,
            'estado' => $model->estado
        ];
    }

    /**
     * @param NoDeducido $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCtgNoLocalizados(NoLocalizado $model)
    {
        // dd($model->ctg_no_localizados_registro);
        if($partida = $model->ctg_no_localizados_registro)
        {
            return $this->item($partida, new CtgNoLocalizadoTransformer);
        }
        return null;
    }

  
}
