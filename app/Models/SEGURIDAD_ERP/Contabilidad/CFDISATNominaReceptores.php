<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class CFDISATNominaReceptores extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.cfdi_sat_nominas_receptores';
    protected $fillable =[
       "nombre"
    ];
    public $timestamps = false;
}
