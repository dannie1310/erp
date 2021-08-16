<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;

use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use League\Fractal\TransformerAbstract;

class SolicitudCompraCotizarProveedorTransformer extends TransformerAbstract
{
    public function transform($array)
    {
        return $array;
    }

}
