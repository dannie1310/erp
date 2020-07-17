<?php


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class OrdenCompraPartidaComplemento extends Model
{
    public $timestamps = false;
    protected $connection = 'cadeco';
    protected $table = 'Compras.ordenes_compra_partidas_complemento';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_item',
        'descuento',
        'id_moneda',
        'observaciones',
    ];
}