<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/03/2019
 * Time: 07:15 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

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
    
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad,2,'.', ',');

    }
    public function getCantidadDecimalAttribute()
    {
        return round($this->cantidad,2);

    }
}
