<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 21/02/19
 * Time: 05:05 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Contabilidad\CuentaCosto;
use App\Models\CADECO\Contabilidad\DatosContables;

class Costo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'costos';
    protected $primaryKey = 'id_costo';
    public $timestamps = false;
    public $searchable = [
        'descripcion',
        'observaciones'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function getTieneHijosAttribute()
    {
        return $this->hijos()->count() ? true : false;
    }

    public function cuenta()
    {
        return $this->hasOne(CuentaCosto::class, "id_costo")
            ->where('Contabilidad.cuentas_costos.estatus', '=', 1);
    }

    public function scopeSinCuenta($query)
    {
        return $query->has('cuenta', '=', 0);
    }

    public function scopeRoots($query)
    {
        return $query->whereRaw('LEN(nivel) = 4');
    }

    public function scopeConCuenta($query)
    {
        return $query->has('cuenta');
    }

    public function hijos()
    {
        return $this->hasMany(self::class, 'id_obra', 'id_obra')
            ->where('nivel', 'LIKE', $this->nivel . '___.');
    }

    public function scopeCostoFinanza($query)
    {
        return $query->whereRaw(" (descripcion like '5%' or
                                  descripcion like '6%' or
                                  descripcion like '7%' )");
    }

    public function scopeDatosContablesConfiguracion($query){
        if(DatosContables::where('id_obra', '=', Context::getIdObra())->first()->costo_en_tipo_gasto){
            return $this->scopeCostoFinanza($query);
        }
        return $query;
    }

}