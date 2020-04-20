<?php


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class CotizacionComplemento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.cotizacion_complemento';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'parcialidades',
        'dias_credito',
        'vigencia',
        'descuento',
        'plazo_entrega',
        'anticipo',
        'importe',
        'tc_usd',
        'tc_eur',
        'registro',
        'timestamp_registro'
    ];
}