<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class DescargaLayoutBanco extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'descargas_layout_banco';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'semana',
        'anio',
        'usuario_descargo'
    ];
}
