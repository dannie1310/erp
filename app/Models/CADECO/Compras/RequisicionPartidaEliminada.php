<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 27/11/2019
 * Time: 07:06 PM
 */

namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class RequisicionPartidaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.requisiciones_partidas_eliminadas';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_material',
        'cantidad',
        'descripcion_material',
        'unidad',
        'numero_parte',
        'observaciones',
        'fecha_entrega',
        'usuario_registro',
        'timestamp_registro'
    ];

    public $timestamps = false;
}