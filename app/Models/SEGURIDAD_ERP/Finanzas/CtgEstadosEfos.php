<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use Illuminate\Database\Eloquent\Model;

class CtgEstadosEfos extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.ctg_estados_efos';
    protected $primaryKey = 'id';

    public $timestamps = false;

}
