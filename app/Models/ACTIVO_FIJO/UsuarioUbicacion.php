<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UsuarioUbicacion extends Model
{
    protected $connection = 'sci';
    protected $table = 'sci.usuarios_ubicaciones';
    public $primaryKey = 'idUsuario';
    public $timestamps = false;
    protected $fillable = [
    ];
}