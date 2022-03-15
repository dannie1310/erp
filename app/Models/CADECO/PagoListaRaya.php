<?php


namespace App\Models\CADECO;


class PagoListaRaya extends Pago
{
    public const TIPO_ANTECEDENTE = 72;
    public const OPCION_ANTECEDENTE = 65537;

    protected $fillable = [
        'id_antecedente',
        'id_referente',
        'tipo_transaccion',
        'fecha',
        'estado',
        'id_obra',
        'id_cuenta',
        "id_moneda",
        'cumplimiento',
        'vencimiento',
        "opciones",
        'monto',
        "saldo",
        'referencia',
        "destino",
        'observaciones',
        'tipo_cambio',
        "id_usuario"
    ];
    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 82)
                ->where('opciones', '=', 65537)
                ->where('estado', '!=', -2);
        });
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudListaRaya::class, 'id_antecedente', 'id_transaccion');
    }

    public function listaRaya()
    {
        return $this->belongsTo(ListaRaya::class, 'id_transaccion','id_referente');
    }

    public function prestacion()
    {
        return $this->belongsTo(Prestacion::class, 'id_transaccion','id_referente');
    }
}
