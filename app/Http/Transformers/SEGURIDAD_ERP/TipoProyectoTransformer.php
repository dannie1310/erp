<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/21/19
 * Time: 5:30 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\TipoProyecto;
use League\Fractal\TransformerAbstract;

class TipoProyectoTransformer extends TransformerAbstract
{
    public function transform(TipoProyecto $model) {
        return $model->toArray();
    }
}