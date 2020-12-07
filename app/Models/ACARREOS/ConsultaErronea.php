<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class ConsultaErronea extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'cosultas_erroneas';
    public $primaryKey = 'idConsulta';
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
