<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2019
 * Time: 11:03 AM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.monedas';
    protected $primaryKey = 'id_moneda';

    public $timestamps = false;
    public $searchable = [
        'nombre',
        'abreviatura',
    ];

    public function cambio()
    {
        // dd('cambio');
        return $this->hasOne(Cambio::class, 'id_moneda', 'id_moneda')->orderByDesc('fecha');
    }

    public function scopeMonedaExtranjera($query)
    {
        return $query->where('tipo', '=', 0);
    }

    public function getTipoCambioAttribute()
    {
        return ($this->cambio) ? $this->cambio->cambio : 1 ;
    }
}
