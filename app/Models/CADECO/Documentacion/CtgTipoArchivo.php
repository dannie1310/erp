<?php


namespace App\Models\CADECO\Documentacion;


use Illuminate\Database\Eloquent\Model;

class CtgTipoArchivo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Documentacion.ctg_tipos_archivo';
    public $timestamps = false;
}
