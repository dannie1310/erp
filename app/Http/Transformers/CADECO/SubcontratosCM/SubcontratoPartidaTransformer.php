<?php


namespace App\Http\Transformers\CADECO\SubcontratosCM;


use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Models\CADECO\ItemSubcontrato;
use League\Fractal\TransformerAbstract;

class SubcontratoPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'partida_estimacion',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];


    public function transform($item)
    {
        return $item;
    }

}
