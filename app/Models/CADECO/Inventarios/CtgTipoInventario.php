<?php


namespace App\Models\CADECO\Inventarios;


use Illuminate\Database\Eloquent\Model;

class CtgTipoInventario extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.ctg_tipos_inventario';
    protected $primaryKey = 'id';

    public $timestamps = false;
}