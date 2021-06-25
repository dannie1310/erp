<?php


namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class ComprobanteFondoEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.comprobantes_fondos_eliminados';
    public $primaryKey = 'id_transaccion';
    public $timestamps = false;
    protected $fillable = [
        'id_transaccion',
        'id_referente',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'impreso',
        'id_obra',
        'id_concepto',
        'id_moneda',
        'cumplimiento',
        'opciones',
        'monto',
        'impuesto',
        'referencia',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'id_usuario',
        'motivo',
        'usuario_elimina',
        'fecha_eliminacion'
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
