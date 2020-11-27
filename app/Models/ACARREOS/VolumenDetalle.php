<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class VolumenDetalle extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'volumen_detalle';
    public $primaryKey = 'id';
    protected $fillable = [
        'id_viaje_neto',
        'volumen_origen',
        'volumen_entrada',
        'volumen',
        'idregistro',
        'fecha_hora_registro'
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
