<?php


namespace App\Models\CADECO;


class AvanceObra extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const TIPO = 98;
    public const OPCION = 0;
    public const NOMBRE = "Avance de Obra";

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'opciones',
        'fecha',
        'id_concepto',
        'observaciones',
        'comentario',
        'FechaHoraRegistro',
        'id_obra',
        'id_usuario',
        'cumplimiento',
        'vencimiento'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query)
        {
            return $query->where('tipo_transaccion', self::TIPO)->where('opciones', self::OPCION);
        });
    }
}
