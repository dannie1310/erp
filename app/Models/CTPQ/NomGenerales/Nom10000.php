<?php

namespace App\Models\CTPQ\NomGenerales;

use Illuminate\Database\Eloquent\Model;

class Nom10000 extends Model
{
    protected $connection = 'cntpq_nom_gen';
    protected $table = 'NOM10000';
    protected $primaryKey = 'IDEmpresa';
    public $timestamps = false;

    /**
     * Relaciones
     */

    /**
     * Scopes
     */
    public function scopeEditable($query)
    {
        return $query->where('RutaEmpresa', 'like','nm%');
    }

    /**
     * Atributos
     */
    public function getEmpresaNombreAttribute()
    {
        $array_base = explode( '_', $this->RutaEmpresa);
        return substr($array_base[0], 2, strlen($array_base[0]));
    }

    public function getActividadAttribute()
    {
        $array_base = explode( '_', $this->RutaEmpresa);
        return $array_base[1];
    }

    /**
     * Métodos
     */
}
