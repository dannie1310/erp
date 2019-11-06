<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.entregas';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_item',
        'fecha',
        'cantidad',
        'numero_entrega',
        'id_concepto',
        'id_almacen',
        'surtida',
    ];

    public $timestamps = false;
}
