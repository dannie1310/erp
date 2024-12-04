<?php

namespace App\Models\CTPQ\NomGenerales;

use App\Models\MODULOSSAO\InterfazNominas\ProyectoIFS;
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
    public function proyecto()
    {
        return $this->belongsTo(ProyectoIFS::class, 'nombre_base_contpaq', 'RutaEmpresa');
    }

    /**
     * Scopes
     */
    public function scopeEditable($query)
    {
        return $query->where('RutaEmpresa', 'like','nm%');
    }

    public function scopeConProyectoIfs($query)
    {
        $empresas = ProyectoIFS::whereNotNull('secuencia_ifs')->where('estatus', 1)->pluck('nombre_base_contpaq');
        return $query->whereIn('RutaEmpresa', $empresas);
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
