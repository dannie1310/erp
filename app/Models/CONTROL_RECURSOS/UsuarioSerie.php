<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class UsuarioSerie extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'usuarios_series';
    protected $primaryKey = 'idusuarios_series';

    /**
     * Relaciones
     */

    /**
     * Scopes
     */
    public function scopePorUsuario($query)
    {
        return $query->where('idusuario', auth()->id());
    }

    public function scopeActivo($query)
    {
        return $query->where('estatus', '=', 1);
    }


    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
