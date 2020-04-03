<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/04/2020
 * Time: 05:19 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Reportes;

use App\Models\SEGURIDAD_ERP\Reportes\Reporte as Model;
use League\Fractal\TransformerAbstract;

class ReporteTransformer extends TransformerAbstract
{
    public function transform(Model $model) {
        return [
            'id' => (int) $model->id,
            'nombre' => (string) $model->nombre,
            'descripcion' => (string) $model->descripcion,
            'url' => (string) $model->url,
        ];
    }

}