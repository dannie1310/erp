<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;

use Illuminate\Database\Eloquent\Model;

class CtgTipoFecha extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.ctg_tipos_fechas';
    protected $primaryKey = 'id';

    public $timestamps = false;
}