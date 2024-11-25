<?php

namespace App\Models\CONTROL_RECURSOS;

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
       return $query->whereRaw("IdTipoPago in (".$tipo.")");
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
            return $this->empleado->RazonSocial;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFolioFormatoAttribute()
    {
        return $this->Serie."-".$this->Folio;
    }

     public function getEstadoAttribute()
    {
        try {
            return 'POR COMPROBAR';
        } catch (\Exception $e) {
            return null;
        }

        /**
         *   if($valido!=0)
        {
        ?>
        &nbsp; COMPROBANDO<?PHP }else{?>POR COMPROBAR<?PHP }?>
        </td>

         */
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


    /**
     * Métodos
     */
}
