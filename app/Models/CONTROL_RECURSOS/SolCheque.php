<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class SolCheque extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'solcheques';
    protected $primaryKey = 'IdSolCheques';

    /**
     * Relaciones
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'IdProveedor', 'IdProveedor');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'IdEmpresa', 'IdEmpresa');
    }

    public function partida()
    {
        return $this->belongsTo(PartidaSolRec::class, 'IdSolCheques', 'IdSolCheque');
    }

    public function moneda()
    {
        return $this->belongsTo(CtgMoneda::class, 'IdMoneda','id');
    }

    public function cuentaProveedor()
    {
        return $this->hasOne(CuentaProveedor::class, 'IdCuenta','Cuenta2');
    }

    /**
     * Scopes
     */
    public function scopePartidaAutorizada($query)
    {
        return $query->whereHas('partida', function ($q){
            $q->autorizada();
        });
    }

    public function scopePorSemanaAnio($query, $idsemana)
    {
        $time = SolrecSemanaAnio::where('idsemana_anio', $idsemana)->first();
        $solicitudes = SolRecurso::autorizadas()->where('Semana', '=', $time->semana)->where('Anio', $time->anio)->pluck('IdSolRec');
        return $query->whereHas('partida', function ($q) use ($solicitudes){
            $q->autorizada()->whereIn('IdSolRec', $solicitudes);
        });
    }

    public function scopeOrdenaSerieFolio($query)
    {
        return $query->orderBy('Serie', 'desc')->orderBy('Folio', 'desc');
    }

    /**
     * Atributos
     */
    public function getMonedaDescripcionAttribute()
    {
        try {
            return $this->moneda->moneda;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getImporteFormatAttribute()
    {
        return '$' . number_format(($this->Importe),2);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date,"d/m/Y");
    }

    /**
     * Métodos
     */
}
