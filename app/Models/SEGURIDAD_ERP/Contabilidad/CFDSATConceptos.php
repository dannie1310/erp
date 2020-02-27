<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 05:02 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class CFDSATConceptos extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfd_sat_conceptos';
    public $timestamps = false;

    public function cfd_sat()
    {
        return $this->belongsTo(CFDSAT::class, 'id_cfd_sat', 'id');
    }
}