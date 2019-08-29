<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 05:53 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'movimientos';
    protected $primaryKey = 'id_movimiento';

    public $timestamps = false;


    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'lote_antecedente', 'id_lote');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad,3,'.', '');

    }
}