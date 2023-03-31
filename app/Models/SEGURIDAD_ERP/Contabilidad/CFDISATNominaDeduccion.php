<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class CFDISATNominaDeduccion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfdi_sat_nominas_deducciones';
    public $timestamps = false;
    protected $fillable =[
        "id_cfdi_sat_nomina",
        "tipo_deduccion",
        "clave",
        "concepto",
        "importe",
    ];

    protected $casts = [
        "importe" => "float",
    ];

    public function cfdiSATNomina()
    {
        return $this->belongsTo(CFDISATNomina::class, 'id_cfdi_sat_nomina', 'id');
    }

    public function getImporteFormatAttribute()
    {
        return "$".number_format($this->importe,2);
    }


    /*public function claveDeduccion()
    {
        return $this->belongsTo(ClaveDeduccion::class, 'clave_prod_serv', 'clave');
    }*/



    /*public function getDescripcionSatAttribute()
    {
        try{
            return $this->claveDeduccion->descripcion;
        }catch (\Exception $e){
            return null;
        }
    }*/
}
