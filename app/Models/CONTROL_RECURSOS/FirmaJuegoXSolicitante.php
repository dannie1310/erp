<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class FirmaJuegoXSolicitante extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'firmas_juegos_x_solicitante';
    protected $primaryKey = 'idfirmas_firmantes';
    public $timestamps = false;
}
