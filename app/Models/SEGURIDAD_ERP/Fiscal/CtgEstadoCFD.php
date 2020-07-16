<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use Illuminate\Database\Eloquent\Model;

class CtgEstadoCFD extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.ctg_estados_cfd';
    protected $primaryKey = 'id';

    public $timestamps = false;
}
