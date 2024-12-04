<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class ComprobacionBorrada extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'comprobacionesborradas';
    protected $primaryKey = 'IdComprobacion';

    public $timestamps = false;

    protected $fillable = [
        'usuario_elimina',
        'motivo_eliminacion',
        'fecha_eliminacion'
    ];
}
