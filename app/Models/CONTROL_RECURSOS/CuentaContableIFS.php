<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CuentaContableIFS extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'cuentas_contables_ifs';
    protected $primaryKey = 'id';
}
