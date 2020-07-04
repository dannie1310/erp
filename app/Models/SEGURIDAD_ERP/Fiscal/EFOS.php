<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEstadosEfos;
use Illuminate\Database\Eloquent\Model;

class EFOS extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.efos';
    protected $primaryKey = 'id';

    public $timestamps = false;

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, 'id_proveedor_sat', 'id');
    }

    public function efoEstado()
    {
        return $this->belongsTo(CtgEstadosEfos::class, 'estado', 'id');
    }

    public function CFDAutocorreccion()
    {
        return $this->hasMany(CFDAutocorreccion::class, 'id_proveedor_sat', 'id_proveedor_sat');
    }
}
