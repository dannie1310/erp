<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class FirmaFirmante extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'firmas_firmantes';
    protected $primaryKey = 'idfirmas_firmantes';
    public $timestamps = false;
}
