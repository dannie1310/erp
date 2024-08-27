<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class TipoDoctoComp extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'tiposdoctoscomp';
    protected $primaryKey = 'IdTipoDoctoComp';
}
