<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class CFDISATNominaOtroPago extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfdi_sat_nominas_otros_pagos';
    public $timestamps = false;
    protected $fillable =[
        "id_cfdi_sat_nomina",
        "tipo_otro_pago",
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
