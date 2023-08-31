<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class CFDISATNominaReceptor extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.cfdi_sat_nominas_receptores';
    protected $fillable =[
        "rfc",
        "nombre",
        "nss",
        "curp",
        "id_usuario_intranet",
        "usuario_intranet",
        "acceso_scr",
        "acceso_sao_erp",
        "acceso_modulos_sao",
        "acceso_remesa",
        "acceso_scr",
        "solicita_gxc_rel"
    ];
    public $timestamps = false;

    public function scopePendienteDatos($query)
    {
        return $query->whereNull('nss')
            ->orwhereNull("curp")
            ;
    }

    public function scopePendienteDatosIntranet($query)
    {
        return $query->whereNull('id_usuario_intranet')
            ->orwhereNull("usuario_intranet")
            ;
    }

    public function scopeTieneDatosIntranet($query)
    {
        return $query->whereNotNull('id_usuario_intranet')
            ->whereNotNull("usuario_intranet")
            ;
    }
}
