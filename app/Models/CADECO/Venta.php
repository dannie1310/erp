<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 07:33 PM
 */

namespace App\Models\CADECO;

use App\PDF\VentaFormato;


class Venta extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 38);
        });
    }

    /**
     * Relaciones Eloquent
     */

    public function partidas()
    {
        return $this->hasMany(VentaPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function pdfVenta(){
        $venta = new VentaFormato($this);
        return $venta->create();
    }
}