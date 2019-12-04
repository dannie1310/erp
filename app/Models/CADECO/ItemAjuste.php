<?php
/**
 * Created by PhpStorm.
 * User: Emartinez
 * Date: 28/11/2019
 * Time: 08:15 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class ItemAjuste extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'items';
    protected $primaryKey = 'id_item';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'item_antecedente',
        'cantidad',
        'importe',
        'id_almacen',
        'id_material',
        'referencia'
    ];
    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->whereHas('ajuste');
        });
    }

    public function ajuste()
    {
        return $this->belongsTo(Ajuste::class, 'id_transaccion', 'id_transaccion');
    }
}
