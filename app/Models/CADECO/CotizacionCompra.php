<?php


namespace App\Models\CADECO;


use App\CSV\CotizacionLayout;
use App\Models\CADECO\Compras\CotizacionComplemento;
use Maatwebsite\Excel\Facades\Excel;

class CotizacionCompra  extends Transaccion
{
    public const TIPO_ANTECEDENTE = 17;

    protected $searchable = [
        'id_transaccion'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 18)
            ->where('opciones','=', 1)->where('estado', '!=', 2);
        });
    }

    public function cotizaciones() {
        return $this->hasMany(Cotizacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function complemento()
    {
        return $this->belongsTo(CotizacionComplemento::class, 'id_transaccion', 'id_transaccion');
    }

    public function descargaLayout($id)
    {
        return Excel::download(new CotizacionLayout(CotizacionCompra::find($id)), 'LayoutCotizacion.xlsx');
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCompra::class, 'id_antecedente', 'id_transaccion');
    }
}