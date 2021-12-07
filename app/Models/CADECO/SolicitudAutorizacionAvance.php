<?php


namespace App\Models\CADECO;


class SolicitudAutorizacionAvance extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;
    public const TIPO = 55;
    public const OPCION = 0;
    public const NOMBRE = "Solicitud de Autorización de Avance de Estimación";
    public const ICONO = "fa fa-building";

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'impuesto',
        'anticipo',
        'referencia',
        'observaciones',
        'tipo_transaccion',
        'id_usuario',
        'retencion',
        'id_empresa',
        'id_moneda',
        'numero_folio'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', self::TIPO)
                ->where('opciones', self::OPCION);
        });
    }
}
