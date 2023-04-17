<?php

namespace App\Models\REPSEG;

use Illuminate\Database\Eloquent\Model;

class FinDimClaveSatIngreso extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_dim_clave_sat_ingreso';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'idclave_sat',
        'idtipo_ingreso',
        'timestamp'
    ];

    /**
     * Relaciones
     */

    /**
     * Scopes
     */

    /**
     * Attributos
     */

    /**
     * Métodos
     */
}
