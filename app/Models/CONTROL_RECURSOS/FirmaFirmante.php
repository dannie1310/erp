<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\IGH92\Usuario;
use Illuminate\Database\Eloquent\Model;

class FirmaFirmante extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'firmas_firmantes';
    protected $primaryKey = 'idfirmas_firmantes';
    public $timestamps = false;

    /**
     * Relaciones
     */
    public function firmaSolicitante()
    {
        return $this->belongsTo(FirmaJuegoXSolicitante::class, 'idfirmas_firmantes','idsolicita');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class,  'iduser','idusuario');
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estatus', 1);
    }

    public function scopeFirmasSolicitantes($query)
    {
        return $query->whereHas('firmaSolicitante')->activo()->orderBy('descripcion_st','asc');
    }


    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
