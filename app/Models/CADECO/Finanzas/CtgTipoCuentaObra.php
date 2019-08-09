<?php


namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class CtgTipoCuentaObra extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.ctg_tipos_cuentas_obra';
    public $timestamps = false;
}
