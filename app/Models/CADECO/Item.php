<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/03/2019
 * Time: 07:15 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'items';
    protected $primaryKey = 'id_item';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'item_antecedente',
        'id_concepto',
        'cantidad',
        'cantidad_material',
        'cantidad_mano_obra',
        'importe',
        'precio_unitario',
        'precio_material',
        'precio_mano_obra',
    ];




}
