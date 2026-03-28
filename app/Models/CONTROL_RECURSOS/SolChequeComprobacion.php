<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Support\Facades\DB;

class SolChequeComprobacion extends SolCheque
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('idserie', UsuarioSerie::porUsuario()->activo()->pluck('idseries'));
        });
    }

    /**
     * Relaciones
     */
    public function empleado()
    {
        return $this->belongsTo(Proveedor::class,  'IdProveedor', 'idproveedor')->empleados();
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class,  'IdProveedor', 'idproveedor');
    }

    public function comprobacion()
    {
        return $this->belongsTo(Comprobacion::class, 'IdSolCheques','DoctoOrigen');
    }

    public function devolucion()
    {
        return $this->belongsTo(DevolucionAbonadoSolicitud::class, 'IdSolCheques', 'IdSolicitud');
    }

    public function solicitudesComprobadas()
    {
        return $this->hasManyThrough(Comprobacion::class, SolicitudComprobada::class, 'IdSolCheque','IdComprobacion','IdSolCheques', 'IdComprobacion');
    }

    /**
     * Scopes
     */
    public function scopeImporteDisponible($query)
    {
        return $query->whereRaw("Importe > SaldoComprobado");
    }

    public function scopeValidarEstado($query, $estados)
    {
        return $query->whereRaw("estatus in (".$estados.")");
    }

    public function scopeTipoPago($query, $tipo)
    {
        return $query->whereRaw("IdTipoPago in (" . $tipo . ")");
    }

    public function scopeDocumentosParaRegistrar($query, $valores)
    {
        $valores = explode(",", $valores);
        $serie = Serie::where('idseries', $valores[0])->first();
        return $query->withoutGlobalScopes()->whereRaw("Importe>SaldoComprobado AND
                estatus in(24,25,34,35) AND
                IdTipoPago=4 AND
                Serie ='" . $serie->Descripcion . "' AND
                IdProveedor = ". $valores[1]);
    }

    /**
     * Atributos
     */
    public function getComprobadoFormatAttribute()
    {
        return '$' . number_format(($this->SaldoComprobado),2);
    }

    public function getSaldoAttribute()
    {
        return $this->Importe - $this->SaldoComprobado;
    }

    public function getSaldoFormatAttribute()
    {
        return '$' . number_format(($this->saldo),2);
    }

    public function getImporteFormatAttribute()
    {
        return  number_format(($this->Importe),2);
    }

    public function getEmpresaDescripcionAttribute()
    {
        try {
            return $this->empresa->RazonSocial;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getEmpleadoDescripcionAttribute()
    {
        try {
            return $this->proveedor->RazonSocial;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFolioFormatoAttribute()
    {
        return $this->Serie."-".$this->Folio;
    }

     public function getEstadoFormatAttribute()
    {
        try {
            return $this->comprobacion != null ? (($this->comprobacion->Estatus == 0 || $this->comprobacion->Estatus == 1) ? 'COMPROBANDO' : 'POR COMPROBAR') : 'POR COMPROBAR';
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getEstadoColorAttribute()
    {
        try {
            return $this->comprobacion != null ? '#e8e22d' : '#29be22';
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getDepartamentoAttribute()
    {
        try {
            return $this->empleado->usuario->departamento->departamento;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getIdDepartamentoAttribute()
    {
        try {
            return $this->empleado->usuario->iddepartamento;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getValidoAttribute()
    {
        try {
            return count($this->disponibleComprobacion());
        } catch (\Exception $e) {
            return null;
        }
    }


    /**
     * Métodos
     */
    public function disponibleComprobacion()
    {
        return DB::connection('controlrec')->select(DB::raw("
            SELECT
                c.folio as folio,
                sch.Serie as seriesol,
                sch.Folio as foliosol
            FROM
                comprobaciones as c,
                solicitudescomprobadas as scm,
                solcheques as sch
            WHERE
                scm.idsolcheque= ".$this->getKey()." AND
                scm.idsolcheque=sch.idsolcheques AND
                c.estatus in (0,1) AND
                scm.idcomprobacion=c.idcomprobacion
        "));
    }
}
