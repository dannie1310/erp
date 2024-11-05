<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:57 AM
 */

namespace App\Http\Transformers\CTPQ_NOM;


use App\Models\CTPQ\NomGenerales\Nom10000;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
{
    public function transform(Nom10000 $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->NombreEmpresa,
            'ruta_db' => (string) $model->RutaEmpresa
        ];
    }
}
