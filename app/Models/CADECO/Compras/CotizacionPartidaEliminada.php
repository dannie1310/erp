<?php


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class CotizacionPartidaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.cotizaciones_partidas_eliminadas';
    protected $primaryKey = 'id_transaccion';

    protected $fillable = [
        'id_transaccion',
        'id_material',
        'numero',
        'disponibles',
        'cantidad',
        'precio_unitario',
        'descuento',
        'descuento_adicional',
        'otros_descuentos',
        'flete',
        'anticipo',
        'dias_credito',
        'dias_entrega',
        'no_cotizado',
        'id_moneda',
        'capacidad',
        'descuento_partida',
        'observaciones',
        'estatus',
        'timestamp_registro',
        'usuario_elimina',
        'fecha_eliminacion'
    ];

    public $timestamps = false;
}
