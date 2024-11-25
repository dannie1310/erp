<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class SolicitudComprobada extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'solicitudescomprobadas';
    public $timestamps = false;
    protected $fillable = [
        'IdSolcheque',
        'IdComprobacion',
        'ImporteSolicitud',
        'ImporteComprobadoAnterior',
        'ImportePorComprobar'
    ];
}
