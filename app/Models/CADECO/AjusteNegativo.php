<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 12:43 PM
 */

namespace App\Models\CADECO;


class AjusteNegativo extends Ajuste
{
    protected $fillable = [
        'id_almacen',
        'referencia',
        'observaciones',
        'id_usuario'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('opciones', '=', 1);
        });
    }

    public function partidas()
    {
        return $this->hasMany(AjusteNegativoPartida::class, 'id_transaccion', 'id_transaccion');
    }
}