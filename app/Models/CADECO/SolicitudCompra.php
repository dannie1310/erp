<?php


namespace App\Models\CADECO;


use App\CSV\CotizacionLayout;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use Maatwebsite\Excel\Facades\Excel;

class SolicitudCompra extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 17);
        });
    }

    public $searchable = [
        'numero_folio',
        'observaciones',
        'fecha'
    ];

    public function getRegistroAttribute()
    {
        $comentario = explode('|', $this->comentario);
        return $comentario[1];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'usuario');
    }
    public function cotizaciones()
    {
        return $this->hasMany(CotizacionCompra::class, 'id_antecedente', 'id_transaccion');
    }
}