<?php


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class CotizacionComplementoPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.cotizacion_partidas_complemento';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_material',
        'descuento_partida',
        'observaciones',
        'estatus'
    ];
}