<?php

namespace App\Models\CADECO;

use App\Models\IGH\Usuario;

class PresupuestoContratista extends Transaccion
{
    public const TIPO_ANTECEDENTE = 49;

    public $searchable = [        
        'fecha',
        'numero_folio',
        'empresa.razon_social',
        'contratoProyectado.referencia'
    ];


    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 50)->whereHas('contratoProyectado');
        });
    }

    public function contratoProyectado()
    {
        return $this->belongsTo(ContratoProyectado::class, 'id_antecedente', 'id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(PresupuestoContratistaPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function getUsdFormatAttribute()
    {
        return '$ ' . number_format(abs($this->TcUSD),4);
    }

    public function getEuroFormatAttribute()
    {
        return '$ ' . number_format(abs($this->TcEuro),4);
    }
}
