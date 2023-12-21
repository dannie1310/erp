<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class FirmaSolicitud extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'firmas_solicitudes';
    public $timestamps = false;
    protected $fillable = [
        'idsolcheque',
        'idfirmas_encabezados',
        'idfirmas_firmantes'
    ];
}
