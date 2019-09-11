<?php


namespace App\Models\CADECO\Inventarios;


use Illuminate\Database\Eloquent\Model;

class InventarioFisico extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.inventario_fisico';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'folio'
    ];

}