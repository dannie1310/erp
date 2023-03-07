<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;

use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Scopes\EstatusMayorACeroScope;
use App\Scopes\EstatusMayorCeroScope;
use Exception;
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
        , "cuerpo_correo"
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EstatusMayorACeroScope);
    }

    /**
     * Relaciones
     */
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

    public function registro()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }


    /**
     * Scope
     */
    public function scopePorProveedor($query, $id)
    {
        return $query->where('id_proveedor_sat', $id);
    }


    /**
     * Atributos
     */
    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date, "d/m/Y");
    }

    public function getCantidadCfdiFormatAttribute()
    {
        return number_format($this->cantidad_cfdi,0,'.',',');
    }

    public function getMontoFormatAttribute()
    {
        return number_format($this->monto_mxn_cfdi,2,'.',',');
    }
 
    public function getURegistroAttribute()
    {
        try{
            return $this->registro->nombre_completo;
        }catch(Exception $e){
            return null;
        }
    }

    public function getCfdiAtendidosFormatAttribute()
    {
        return $this->cfdi_atendidos != null ? $this->cfdi_atendidos : 0;
    }

    public function getCfdiNuevosFormatAttribute()
    {
        return $this->cdfi_nuevos != null ? $this->cdfi_nuevos : 0;
    }

    public function getCfdiCanceladosFormatAttribute()
    {
        return $this->cfdi_cancelados != null ? $this->cfdi_cancelados : 0;
    }

    /**
      * Métodos
      */

}
