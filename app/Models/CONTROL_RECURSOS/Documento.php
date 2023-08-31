<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Documento extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'documentos';
    protected $primaryKey = 'IdDocto';

    public $timestamps = false;

    protected $fillable = [
        'FolioDocto',
        'IdTipoDocto',
        'TipoDocto',
        'IdEmpresa',
        'IdProveedor',
        'Fecha',
        'Vencimiento',
        'Concepto',
        'Importe',
        'IVA',
        'Total',
        'Retenciones',
        'TasaIva',
        'OtrosImpuestos',
        'IdMoneda',
        'Alias_Depto',
        'Departamento',
        'IdSerie',
        'TC',
        'Creo',
        'Estatus',
        'Ubicacion',
        'tipo_cambio_excepcion',
        'uuid'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('IdSerie', UsuarioSerie::porUsuario()->activo()->pluck('idseries'));
        });
    }

    /**
     * Relaciones
     */
    public function moneda()
    {
        return $this->belongsTo(CtgMoneda::class, 'IdMoneda','id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'IdEmpresa', 'IdEmpresa');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'IdProveedor', 'IdProveedor');
    }

    public function serie()
    {
        return $this->belongsTo(Serie::class, 'IdSerie','idseries');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoDocto::class,'IdTipoDocto','IdTipoDocto');
    }

    /**
     * Scopes
     */
    public function scopePorTipo($query, $tipos)
    {
        return $query->whereIn('IdTipoDocto', [$tipos]);
    }

    public function scopePorEstado($query, $estados)
    {
        return $query->whereIn('Estatus', [$estados]);
    }

    /**
     * Atributos
     */
    public function getTotalFormatAttribute()
    {
        return '$' . number_format(($this->Total),2);
    }

    public function getFolioConSerieAttribute()
    {
        try {
            return $this->FolioDocto;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getSerieDescripcionAttribute()
    {
        try {
            return $this->serie->Descripcion;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getMonedaDescripcionAttribute()
    {
        try {
            return $this->moneda->moneda;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getTipoDocumentoAttribute()
    {
        try {
            return $this->tipo->Descripcion;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFechaVencimientoFormatAttribute()
    {
        $date = date_create($this->Vencimiento);
        return date_format($date,"d/m/Y");
    }

    public function getEmpresaDescripcionAttribute()
    {
        try {
            return $this->empresa->RazonSocial;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getProveedorDescripcionAttribute()
    {
        try {
            return $this->proveedor->RazonSocial;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getImporteFormatAttribute()
    {
        return '$' . number_format(($this->Importe),2);
    }

    public function getRetencionesFormatAttribute()
    {
        return '$' . number_format(($this->Retenciones),2);
    }

    public function getIvaFormatAttribute()
    {
        return '$' . number_format(($this->IVA),2);
    }

    /**
     * Métodos
     */
    public function registrar($data)
    {dd($data);
        try {
            DB::connection('controlrec')->beginTransaction();
            $factura = $this->repository->registrar($data);
            DB::connection('controlrec')->commit();
            return $factura;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}
