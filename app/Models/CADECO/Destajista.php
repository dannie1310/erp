<?php


namespace App\Models\CADECO;


class Destajista extends Empresa
{
    protected $fillable = [
        'razon_social',
        'rfc',
        'tipo_empresa',
        'FechaHoraRegistro',
        'UsuarioRegistro'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_empresa', '=', 4);
        });
    }
}
