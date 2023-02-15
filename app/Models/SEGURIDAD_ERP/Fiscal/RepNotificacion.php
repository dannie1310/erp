<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
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
    ];

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

}
