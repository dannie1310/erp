<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 04/11/2019
 * Time: 01:41 PM
 */

namespace App\Models\CADECO\Estimaciones;


use Illuminate\Database\Eloquent\Model;

class EstimacionPartidaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Estimaciones.items_eliminados';
    protected $primaryKey = 'id_item';

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