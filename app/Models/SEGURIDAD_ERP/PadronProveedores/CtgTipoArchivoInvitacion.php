<?php

namespace App\Models\SEGURIDAD_ERP\PadronProveedores;

use App\Utils\Util;
use Illuminate\Database\Eloquent\Model;

class CtgTipoArchivoInvitacion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.ctg_tipos_archivos_invitacion';
    public $timestamps = false;

    public function getDescripcionDescargaAttribute()
    {
        return implode("_",explode(" ",strtolower(Util::eliminaCaracteresEspeciales($this->descripcion))));

    }
}
