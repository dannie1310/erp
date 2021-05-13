<?php


namespace App\Http\Transformers\MODULOSSAO\ControlRemesas;


use App\Models\MODULOSSAO\ControlRemesas\RemesaFolio;
use League\Fractal\TransformerAbstract;

class RemesaFolioTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    public function transform(RemesaFolio $model) {
        return [
            'anio' => $model->Anio,
            'numero_semana' => $model->NumeroSemana,
            'cantidad_limite' => $model->CantidadExtraordinariasPermitidas,
            'monto_limite' => $model->MontoLimiteExtraordinarias,
            'id_proyecto' => $model->IDProyecto,
            'proyecto' => $model->nombre_proyecto
        ];
    }
}
