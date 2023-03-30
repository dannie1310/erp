<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class CFDISATNominaPercepcion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfdi_sat_nominas_percepciones';
    public $timestamps = false;
    protected $fillable =[
        "id_cfdi_sat_nomina",
        "tipo_percepcion",
        "clave",
        "concepto",
        "importe_gravado",
        "importe_exento",
    ];

    protected $casts = [
        "importe_gravado" => "float",
        "importe_exento" => "float",
    ];

    public function cfdiSATNomina()
    {
        return $this->belongsTo(CFDISATNomina::class, 'id_cfdi_sat_nomina', 'id');
    }

    public function getImporteGravadoFormatAttribute()
    {
        return "$".number_format($this->importe_gravado,2);
    }

    public function getImporteExentoFormatAttribute()
    {
        return "$".number_format($this->importe_exento,2);
    }

    /*public function clavePercepcion()
    {
        return $this->belongsTo(ClaveProductoServicio::class, 'clave_prod_serv', 'clave');
    }*/



    /*public function getDescripcionSatAttribute()
    {
        try{
            return $this->clavePercepcion->descripcion;
        }catch (\Exception $e){
            return null;
        }
    }*/
}
