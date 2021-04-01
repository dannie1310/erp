<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\CamionImagen;
use League\Fractal\TransformerAbstract;

class CamionImagenTransformer extends TransformerAbstract
{
    public function transform(CamionImagen $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => $model->descripcion_imagen,
            'imagen' => "data:image/jpeg;base64,".$model->Imagen
        ];
    }

}
