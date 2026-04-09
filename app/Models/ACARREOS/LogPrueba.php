<?php

namespace App\Models\ACARREOS;

use Illuminate\Database\Eloquent\Model;

class LogPrueba extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'logs_pruebas';
    public $primaryKey = 'id';
    protected $fillable = [
        'consulta',
        'registro'
    ];
    public $timestamps = false;
    /**
     * Relaciones Eloquent
     */

    /**
     * Scopes
     */


    /**
     * Attributes
     */



    /**
     * Métodos
     */
}
