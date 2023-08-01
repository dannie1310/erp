<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'documentos';
    protected $primaryKey = 'IdDocto';

    public $timestamps = false;

    protected $fillable = [

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

    /**
     * Scopes
     */

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
            return $this->serie->Descripcion.' - '.$this->FolioDocto;
        }catch (\Exception $e)
        {
            return $this->FolioDocto;
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


    /**
     * Métodos
     */

}
