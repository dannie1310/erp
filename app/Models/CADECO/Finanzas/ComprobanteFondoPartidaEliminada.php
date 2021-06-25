<?php


namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class ComprobanteFondoPartidaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.comprobantes_fondos_partidas_eliminadas';
    public $primaryKey = 'id_item';
    public $timestamps = false;
    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_concepto',
        'cantidad',
        'importe',
        'referencia',
        'estado'
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
