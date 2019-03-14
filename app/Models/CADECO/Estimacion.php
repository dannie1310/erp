<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 01:12 PM
 */

namespace App\Models\CADECO;


class Estimacion extends Transaccion
{
    protected $fillable = ['id_transaccion', 'id_antecedente', 'tipo_transaccion'];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 52);
        });
    }

    public function subcontrato(){
        return $this->hasOne(Subcontrato::class, 'id_transaccion', 'id_antecedente');
    }
}