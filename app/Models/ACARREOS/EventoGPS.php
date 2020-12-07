<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class EventoGPS extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'eventos_gps';
    public $primaryKey = 'ideventos_gps';
    public $timestamps = false;
    protected $fillable = [
        'idevento',
        'IMEI',
        'longitude',
        'latitude',
        'fechahora',
        'code',
        'idusuario'
    ];

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
