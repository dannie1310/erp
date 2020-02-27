<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:27 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class CFDSAT extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfd_sat';
    public $timestamps = false;

    public function conceptos()
    {
        return $this->hasMany(CFDSATConceptos::class, 'id_cfd_sat', 'id');
    }

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, 'id_proveedor_sat', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo(EmpresaSAT::class, 'id_empresa_sat', 'id');
    }

}