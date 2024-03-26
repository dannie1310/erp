<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Events\FinalizaProcesamientoAsociacion;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOSCambio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SolicitudAsociacionCuentaProveedor extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitudes_asociacion_cuentas_proveedor';
    public $timestamps = false;
    protected $fillable =[
        "usuario_inicio",
        "fecha_hora_inicio",
        "fecha_hora_fin",
        "cantidad_asociaciones",
        "base_datos",
        "id_empresa_contpaq",
        "nombre_empresa"
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_inicio', 'idusuario');
    }

    public function partidas()
    {
        return $this->hasMany(SolicitudAsociacionCuentaProveedorPartida::class,"id_solicitud_asociacion", "id");
    }

    public static function getSolicitudActiva($id_empresa_local)
    {
        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($id_empresa_local);
        if($empresaLocal)
        {
            $id_empresa_contpaq = $empresaLocal->IdEmpresaContpaq;
            return SolicitudAsociacionCuentaProveedor::whereNull("fecha_hora_fin")->where("id_empresa_contpaq","=", $id_empresa_contpaq)->first();
        }else{
            abort(500,"Empresa no encontrada: ".$id_empresa_local);
        }
    }

    public function getFechaHoraInicioFormatAttribute()
    {
        $date = date_create($this->fecha_hora_inicio);
        return date_format($date, "d/m/Y H:i:s");
    }

    public function getFechaHoraFinFormatAttribute()
    {
        $date = date_create($this->fecha_hora_fin);
        return date_format($date, "d/m/Y H:i:s");
    }

    public function getCantidadAsociacionesDetectadasFormatAttribute()
    {
        return number_format($this->cantidad_asociaciones_detectadas,0,"",",");
    }

    public function getCantidadAsociacionesNuevasFormatAttribute()
    {
        return number_format($this->cantidad_asociaciones_nuevas,0,"",",");
    }

    public function getCantidadAsociacionesEliminadasFormatAttribute()
    {
        return number_format($this->cantidad_asociaciones_eliminadas,0,"",",");
    }

    public function finaliza()
    {
        $this->fecha_hora_fin = date('Y-m-d H:i:s');
        $this->cantidad_asociaciones_nuevas = $this->partidas()->sum("cantidad_asociaciones_nuevas");
        $this->cantidad_asociaciones_eliminadas = $this->partidas()->sum("cantidad_asociaciones_eliminadas");
        $this->cantidad_asociaciones_detectadas = $this->partidas()->sum("cantidad_asociaciones_detectadas");
        $this->save();
        /*event(new FinalizaProcesamientoAsociacion(
            $this
        ));*/
    }

}
