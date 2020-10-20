<?php


namespace App\Models\CADECO\Documentacion;


use Illuminate\Database\Eloquent\Model;

class CtgCategoria extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Documentacion.ctg_categorias';
    public $timestamps = false;
}
