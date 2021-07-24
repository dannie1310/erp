<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2019
 * Time: 11:03 AM
 */

namespace App\Models\CADECO;

use App\Models\IGH\TipoCambio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

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

    /**
     * Relaciones
     */
    public function cambio()
    {
        return $this->hasOne(Cambio::class, 'id_moneda', 'id_moneda')->orderByDesc('fecha');
    }

    public function cambioIgh()
    {
        return $this->hasOne(TipoCambio::class, 'moneda', 'id_moneda')->orderByDesc('fecha');
    }

    /**
     * Scopes
     */
    public function scopeMonedaExtranjera($query)
    {
        return $query->where('tipo', '=', 0);
    }

    /**
     * Atributos
     */
    public function getTipoCambioAttribute()
    {
        return $this->cambio ? $this->cambio->cambio : $this->tipo == 1 ? 1: null;
    }

    public function getTipoCambioIghAttribute()
    {
        if($this->id_moneda == 2 || $this->id_moneda == 3)
        {
            $moneda = ($this->id_moneda == 2) ? 1 : 2;
            return TipoCambio::where('moneda', '=', $moneda)->orderBy('fecha', 'DESC')->first()->tipo_cambio;
        }
        return 1;
    }

    /**
     * Métodos
     */
    public function buscarPorBase($base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        return $this->get();
    }
}
