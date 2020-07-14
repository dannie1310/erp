<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 01/07/2020
 * Time: 06:23 PM
 */

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use Illuminate\Database\Eloquent\Model;

class CFDAutocorreccion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfd_sat_autocorrecciones';
    public $timestamps = false;

}