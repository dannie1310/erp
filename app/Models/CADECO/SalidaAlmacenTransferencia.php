<?php

namespace App\Models\CADECO;


class SalidaAlmacenTransferencia extends Transaccion
{
    public const TIPO = 34;
    public const OPCION = 65537;
    public const NOMBRE = "Transferencia de Almacén";
    public const ICONO = "fa fa-exchange-alt";
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 34)
                ->where('opciones', '=', 65537);
        });
    }

    public function items()
    {
        return $this->hasMany(ItemTransferenciaAlmacen::class, 'id_transaccion', 'id_transaccion');
    }

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_transaccion;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["usuario"] = $this->usuario_registro;
        $datos["observaciones"] = $this->observaciones;
        $datos["tipo"] = SalidaAlmacenTransferencia::NOMBRE;
        $datos["opcion"] = SalidaAlmacenTransferencia::OPCION;
        $datos["tipo_numero"] = SalidaAlmacenTransferencia::TIPO;
        $datos["icono"] = SalidaAlmacenTransferencia::ICONO;
        $datos["consulta"] = 0;

        return $datos;
    }
}
