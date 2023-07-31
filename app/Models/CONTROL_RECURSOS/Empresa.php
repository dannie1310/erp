<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'empresas';
    protected $primaryKey = 'IdEmpresa';
}
