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

}
