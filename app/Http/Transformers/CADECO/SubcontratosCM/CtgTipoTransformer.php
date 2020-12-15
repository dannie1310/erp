<?php


namespace App\Http\Transformers\CADECO\SubcontratosCM;

use App\Models\CADECO\SubcontratosCM\CtgTipo;
use App\Models\CADECO\SubcontratosCM\Item;
use League\Fractal\TransformerAbstract;

class CtgTipoTransformer extends TransformerAbstract
{

    public function transform(CtgTipo $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
        ];
    }
}
