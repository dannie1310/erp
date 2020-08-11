<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class CtgSeccion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.ctg_secciones';
    public $timestamps = false;
}
