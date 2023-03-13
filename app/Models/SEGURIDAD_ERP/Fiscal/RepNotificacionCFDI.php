<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Scopes\EstatusMayorACeroScope;
use App\Scopes\EstatusMayorCeroScope;
use Illuminate\Database\Eloquent\Model;

class RepNotificacionCFDI extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.rep_notificaciones_cfdi';
    public $timestamps = false;

    public $fillable = [
         "id_notificacion"
        , "id_cfdi"
    ];

    public function notificacion()
    {
        return $this->belongsTo(RepNotificacion::class, "id_notificacion", "id");
    }

    public function cfdi()
    {
        return $this->belongsTo(CFDSAT::class, "id_cfdi", "id");
    }



}
