<?php

namespace App\Models\CADECO\FinanzasCBE;

use Illuminate\Database\Eloquent\Model;

class CtgTipoCuenta extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'FinanzasCBE.ctg_tipo_cuenta';
    protected $primaryKey = 'id';

    public $timestamps = false;
}