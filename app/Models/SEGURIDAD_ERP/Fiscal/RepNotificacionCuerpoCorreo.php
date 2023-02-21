<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;

class RepNotificacionCuerpoCorreo extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.rep_notificaciones_cuerpo_correo';
    public $timestamps = false;

    public $fillable = [
    ];



}
