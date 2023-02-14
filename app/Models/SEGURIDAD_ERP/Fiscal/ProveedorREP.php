<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;

class ProveedorREP extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.vw_proveedores_rep';
    public $timestamps = false;

    public $fillable = [
    ];

    public function cfdi(){
        return $this->hasMany(CFDSAT::class,"id_proveedor_sat","id");
    }

    public function ultima_ubicacion()
    {
        return $this->hasOne(ProveedorUltimaUbicacion::class, "id_proveedor_sat","id");
    }

    public function notificaciones()
    {
        return $this->hasMany(RepNotificacion::class, "id_proveedor_sat", "id");
    }

    public function contactos()
    {
        return $this->hasMany(ContactoProveedorREP::class, "id_proveedor_sat", "id");
    }

    public function getTotalRepFormatAttribute()
    {
        return number_format($this->total_rep);
    }

    public function getTotalCfdiFormatAttribute()
    {
        return number_format($this->total_cfdi);
    }

    public function getPendienteRepFormatAttribute()
    {
        return number_format($this->pendiente_rep);
    }

    public function getCantidadCfdiFormatAttribute()
    {
        return number_format($this->cantidad_cfdi);
    }

    public function getFechaUltimoCfdiConUbicacionFormatAttribute()
    {
        if($this->fecha_ultimo_cfdi_con_ubicacion) {
            $date = date_create($this->fecha_ultimo_cfdi_con_ubicacion);
            return date_format($date, "d/m/Y");
        }
        return null;
    }

    public function getFechaUltimaNotificacionFormatAttribute()
    {
        if(count($this->notificaciones)>0) {
            $ultima_notificacion = $this->notificaciones()->orderBy("id","desc")->first();
            $date = date_create($ultima_notificacion->fecha_hora_registro);
            return date_format($date, "d/m/Y");
        }
        return null;
    }

    public function getContacto($data){
        $contacto = $this->contactos()->where("correo", "=", $data["correo"])->first();
        if(!$contacto){
            $contacto = $this->contactos()->create([
                "correo" => $data["correo"],
                "nombre" => $data["contacto"]
            ]);
        }else{
            $contacto->nombre = $data["contacto"];
            $contacto->save();
        }
        return $contacto;
    }

}
