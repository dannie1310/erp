<?php


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\SolicitudCompra;
use League\Fractal\TransformerAbstract;

class SolicitudTransformer extends TransformerAbstract
{
        protected $defaultIncludes = [

            ];

    public function transform(SolicitudCompra $model)
    {
               return [
                        'id' => (int)$model->getKey(),
                        'numero_folio_format'=>(string)$model->numero_folio_format_orden
                        ];
    }

}