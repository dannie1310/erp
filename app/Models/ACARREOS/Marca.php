<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'marcas';
    public $primaryKey = 'IdMarca';
    protected $fillable = [
        'Descripcion',
        'usuario_registro'
    ];
}
