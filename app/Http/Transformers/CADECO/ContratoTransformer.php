<?php


namespace App\Http\Transformers\CADECO;
use App\Models\CADECO\Contrato;
use League\Fractal\TransformerAbstract;

class ContratoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(Contrato $model)
    {
        return [
            'id_transaccion' => $model->id_transaccion,
            'id_concepto' => $model->id_concepto,
            'nivel' => $model->nivel,
            'descripcion' => $model->descripcion,
            'unidad' => $model->unidad,
            'cantidad_original' => $model->cantidad_original,
            'primer_nivel' => $model->primer_nivel_descripcion,
            'segundo_nivel' => $model->segundo_nivel_descripcion,
            'tercer_nivel' => $model->tercer_nivel_descripcion
        ];
    }

    public function includePrimerNivel(Contrato $model)
    {
        if($primer = $model->primer_nivel)
        {
            return $this->item($primer, new ContratoTransformer);
        }
        return null;
    }
}
