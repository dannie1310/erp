<?php


namespace App\Models\CADECO\Almacenes;


class AjusteEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Almacen.ajuste_inventario_eliminado';
    protected $primaryKey = 'id_transaccion';


    public $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'id_obra',
        'id_almacen',
        'opciones',
        'monto',
        'saldo',
        'referencia',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'usuario_elimina',
        'motivo_eliminacion',
        'fecha_eliminacion',

    ];




}
