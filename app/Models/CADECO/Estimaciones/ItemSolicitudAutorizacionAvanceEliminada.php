<?php


namespace App\Models\CADECO\Estimaciones;


use Illuminate\Database\Eloquent\Model;

class ItemSolicitudAutorizacionAvanceEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Estimaciones.items_solicitudes_autorizacion_avance_eliminadas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_antecedente',
        'item_antecedente',
        'id_concepto',
        'cantidad',
        'importe',
        'precio_unitario',
        'estado'
    ];

    public $timestamps = false;
}
