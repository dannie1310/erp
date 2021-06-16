<?php


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Destino;
use App\Models\CADECO\PresupuestoContratistaPartida;
use League\Fractal\TransformerAbstract;

class DestinoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'concepto'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = ['concepto'];

    public function transform(Destino $model)
    {
        return [
            'id_concepto' => $model->getKey(),
        ];
    }

    public function includeConcepto(Destino $model)
    {
        if($concepto = $model->concepto)
        {
            return $this->item($concepto, new ConceptoTransformer);
        }
        return null;
    }
}
