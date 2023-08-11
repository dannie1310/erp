<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class TipoDocto extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'tiposdoctos';
    protected $primaryKey = 'IdTipoDocto';
}

