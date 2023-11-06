<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class RelacionGastoXDocumento extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'relaciones_gastos_x_documento';
    public $timestamps = false;

    protected $fillable = [
        'idrelaciones_gastos',
        'iddocumento',
        'idregistro'
    ];
}
