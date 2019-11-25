<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 01:10 PM
 */

namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class RequisicionPartidaComplemento extends Model
{
    public $timestamps = false;
    protected $connection = 'cadeco';
    protected $table = 'Compras.requisiciones_partidas_complemento';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_item',
        'descripcion_material',
        'numero_parte',
        'unidad',
        'observaciones',
        'fecha_entrega',
        'usuario_registo',
        'timestamp_registro'
    ];
}