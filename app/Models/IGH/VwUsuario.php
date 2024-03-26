<?php


namespace App\Models\IGH;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\ACTIVO_FIJO\UsuarioUbicacion;

class VwUsuario extends Model
{
    protected $connection = 'igh';
    protected $table = 'usuarios_intranet';
    public $primaryKey = 'IdUsuario';
    protected $fillable = [
    ];

    /**
     * Relaciones Eloquent
     */

     /**
      * Scopes
      */

    /**
     * Atributtes
     */

}
