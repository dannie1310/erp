<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 05:02 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\catCFDI\ClaveProductoServicio;
use Illuminate\Database\Eloquent\Model;

class CFDSATConceptos extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfd_sat_conceptos';
    public $timestamps = false;
    protected $fillable =[
        "cantidad",
        "descripcion",
        "importe",
        "no_identificacion",
        "unidad",
        "valor_unitario",
        "clave_prod_serv",
        "clave_unidad",
        "descuento"
    ];

    public function cfd_sat()
    {
        return $this->belongsTo(CFDSAT::class, 'id_cfd_sat', 'id');
    }

    public function cfdi()
    {
        return $this->belongsTo(CFDSAT::class, 'id_cfd_sat', 'id');
    }

    public function traslados()
    {
        return $this->hasMany(CFDSATTraslados::class, 'id_cfd_sat_concepto', 'id');
    }

    public function retenciones()
    {
        return $this->hasMany(CFDSATRetenciones::class, 'id_cfd_sat_concepto', 'id');
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
