<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CtgEstadoRelacionDocumento extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'ctg_estados_relaciones_documentos';
    protected $primaryKey = 'idctg_estados_relaciones_documentos';
    public $timestamps = false;
}
