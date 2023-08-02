<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'series';
    protected $primaryKey = 'idseries';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('idseries', UsuarioSerie::porUsuario()->activo()->pluck('idseries'))
                ->where('Estatus', 1);
        });
    }
}
