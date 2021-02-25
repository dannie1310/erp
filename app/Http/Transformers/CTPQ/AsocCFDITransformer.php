<?php


namespace App\Http\Transformers\CTPQ;


use App\Models\CTPQ\AsocCFDI;
use League\Fractal\TransformerAbstract;

class AsocCFDITransformer extends TransformerAbstract
{
    public function transform(AsocCFDI $model) {
        return [
            'id' => (int) $model->getKey(),
            'uuid' => $model->UUID,
        ];
    }
}
