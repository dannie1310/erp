<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'tipopago';
    protected $primaryKey = 'IdTipoPago';
}
