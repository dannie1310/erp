<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\catCFDI\ClaveProductoServicio;
use Illuminate\Database\Eloquent\Model;

class CFDISATNominaConcepto extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfdi_sat_nominas_conceptos';
    public $timestamps = false;
    protected $fillable =[
        "id_cfdi_sat_nomina",
        "clave_prod_serv",
        "cantidad",
        "clave_unidad",
        "descripcion",
        "obj_imp",
        "valor_unitario",
        "importe",
        "descuento"
    ];

    protected $casts = [
        "cantidad" => "float",
        "valor_unitario" => "float",
        "importe" => "float",
        "descuento" => "float"
    ];

    public function cfdiSATNomina()
    {
        return $this->belongsTo(CFDISATNomina::class, 'id_cfdi_sat_nomina', 'id');
    }

    public function claveProductoServicio()
    {
        return $this->belongsTo(ClaveProductoServicio::class, 'clave_prod_serv', 'clave');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad,2);
    }

    public function getValorUnitarioFormatAttribute()
    {
        return "$".number_format($this->valor_unitario,2);
    }

    public function getImporteFormatAttribute()
    {
        return "$".number_format($this->importe,2);
    }

    public function getDescuentoFormatAttribute()
    {
        return '$ ' . number_format(($this->descuento),2);
    }

    public function getDescripcionSatAttribute()
    {
        try{
            return $this->claveProductoServicio->descripcion;
        }catch (\Exception $e){
            return null;
        }
    }
}
