<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 09:03 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class CFDSATTraslados extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfd_sat_traslados';
    public $timestamps = false;
    protected $fillable =[
        "tipo_factor",
        "tasa_o_cuota",
        "importe",
        "impuesto",
        "base"
    ];

    public function cfd_sat()
    {
        return $this->belongsTo(CFDSAT::class, 'id_cfd_sat', 'id');
    }

    public function getImporteFormatAttribute()
    {
        return "$".number_format($this->importe,2);
    }

    public function getBaseFormatAttribute()
    {
        return "$".number_format($this->base,2);
    }

    public function getImpuestoTxtAttribute()
    {
        if($this->impuesto == "002")
        {
            return "IVA";
        }
        return $this->impuesto;
    }

}
