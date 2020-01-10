<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use Illuminate\Database\Eloquent\Model;

class CtgEfosLog extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.ctg_efos_log';
    protected $primaryKey = 'id';

}
