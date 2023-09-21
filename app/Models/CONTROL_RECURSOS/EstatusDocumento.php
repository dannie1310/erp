<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class EstatusDocumento extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'estatus_documentos';
    protected $primaryKey = 'Estatus';
}
