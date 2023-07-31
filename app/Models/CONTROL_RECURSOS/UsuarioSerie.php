<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class UsuarioSerie extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'usuarios_series';
    protected $primaryKey = 'idusuarios_series';
}
