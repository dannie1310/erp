<?php


namespace App\Models\CADECO;

use Illuminate\Database\Eloquent\Model;

class UnidadComplemento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Catalogos.unidad_complemento';
    protected $primaryKey = 'id_unidad';

    public $timestamps = false;

    protected $fillable = [
        'unidad',
        'IdUsuario'
    ];
}