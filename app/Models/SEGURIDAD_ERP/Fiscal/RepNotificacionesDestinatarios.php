<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;

class RepNotificacionesDestinatarios extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.rep_notificaciones_destinatarios';
    public $timestamps = false;

    public $fillable = [
        "id_notificacion"
        , "id_usuario_hermes"
        , "id_contacto_proveedor"
    ];

}
