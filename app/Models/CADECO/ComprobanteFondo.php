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

    /**
     * Relaciones Eloquent
     */
    public function fondo()
    {
        return $this->belongsTo(Fondo::class, 'id_referente', 'id_fondo');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    /**
     * Scopes
     */


    /**
     * Attributes
     */
    public function getTotalAttribute()
    {
        return $this->monto + $this->impuesto;
    }

    public function getTotalFormatAttribute()
    {
        return '$ ' . number_format($this->total, 2, '.', ',');
    }

    /**
     * Métodos
     */
}
