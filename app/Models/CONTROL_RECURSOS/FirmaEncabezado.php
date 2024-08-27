<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class FirmaEncabezado extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'firmas_encabezados';
    protected $primaryKey = 'idfirmas_encabezados';
    public $timestamps = false;
}
