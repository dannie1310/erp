<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CtgMoneda extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'ctg_monedas';
    protected $primaryKey = 'id';
}
