<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;
class CFDISATLogADD extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfdi_sat_log_add';
    public $timestamps = false;

    protected $fillable =[
        "id_cfdi_sat",
        "log_add",
        "tipo"
    ];

}
