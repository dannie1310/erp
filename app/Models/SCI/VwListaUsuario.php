<?php

namespace App\Models\SCI;

use Illuminate\Database\Eloquent\Model;

class VwListaUsuario extends Model
{
    protected $connection = 'sci';
    protected $table = 'vw_listaUsuarios';
    protected $primaryKey = 'idUsuario';

    /**
     * Relaciones
     */
    public function partidas()
    {
        return $this->hasMany(Partida::class, 'usr','idUsuario');
    }

    /**
     * Scopes
     */
    public function scopePartidasUbicacion($query)
    {
        return $query->selectRaw('DISTINCT idUsuario, Usuario, Ubicacion')
            ->join('partidas','vw_listaUsuarios.idUsuario','Partidas.usr')
            ->orderBy('Usuario','asc');
    }
}
