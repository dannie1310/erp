<?php

namespace App\Models\SCI;

use Illuminate\Database\Eloquent\Model;

class VwListaDepartamento extends Model
{
    protected $connection = 'sci';
    protected $table = 'vw_listaDepartamentos';
    protected $primaryKey = 'idDepartamento';

    /**
     * Relaciones
     */
    public function partidas()
    {
        return $this->hasMany(VwPartidaRegistrada::class, 'idDepartamento','idDepartamento');
    }

    /**
     * Scopes
     */
    public function scopePartidasPorDepartamento($query)
    {
        return $query->selectRaw('DISTINCT vw_listaDepartamentos.idDepartamento, vw_listaDepartamentos.Departamento ')
            ->join('vw_partidasRegistradas','vw_partidasRegistradas.idDepartamento','vw_listaDepartamentos.idDepartamento')
            ->orderBy('vw_listaDepartamentos.Departamento','asc');
    }
}
