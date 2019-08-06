<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use Illuminate\Database\Eloquent\Model;

class CtgBanco extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.ctg_bancos';
    public $timestamps = false;
}
