<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Scopes\EstatusMayorACeroScope;
use App\Scopes\EstatusMayorCeroScope;
use Illuminate\Database\Eloquent\Model;

class RepNotificacion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.rep_notificaciones';
    public $timestamps = false;

    public $fillable = [
        "id_proveedor_sat"
        , "id_usuario_hermes"
        , "id_contacto_proveedor"
        , "comunicado_pdf"
        , "cuerpo_correo",
        "cfdi_nuevos",
        "cfdi_atendidos",
        "cfdi_cancelados",
        "cantidad_cfdi",
        "monto_mxn_cfdi"
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EstatusMayorACeroScope);
    }

    public function destinatarios()
    {
        return $this->hasMany(RepNotificacionDestinatario::class, "id_notificacion", "id");
    }

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, "id_proveedor_sat","id");
    }

    public function proveedor_rep()
    {
        return $this->belongsTo(ProveedorREP::class, "id_proveedor_sat","id");
    }

    public function notificacionCFDI()
    {
        return $this->hasMany(RepNotificacionCFDI::class,"id_notificacion", "id");
    }

}
