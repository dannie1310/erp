<?php


namespace App\Models\CADECO;


class ComprobanteFondo extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const TIPO = 101;
    public const OPCION = 0;
    public const NOMBRE = "Comprobante de Fondo";

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query)
        {
            return $query->where('tipo_transaccion', self::TIPO)->where('opciones', self::OPCION);
        });
    }
}
