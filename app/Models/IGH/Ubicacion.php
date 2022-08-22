<?php


namespace App\Models\IGH;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\ACTIVO_FIJO\UsuarioUbicacion;

class Ubicacion extends Model
{
    protected $connection = 'igh';
    protected $table = 'ubicacion';
    public $primaryKey = 'idubicacion';
    protected $fillable = [
    ];

    /**
     * Relaciones Eloquent
     */
    public function ubicacionesSci(){
        return $this->hasMany(UsuarioUbicacion::class, 'idubicacion', 'idubicacion');
    }


     /**
      * Scopes
      */
    public function scopeUsuarioUbicacion($query){
        $ubicaciones = UsuarioUbicacion::where('idUsuario', '=', auth()->id())->get(['idubicacion'])->toArray();
        array_push($ubicaciones, auth()->user()->idubicacion);
        return $query->whereIn('idubicacion', $ubicaciones);
    }


    /**
     * Atributtes
     */

     Public function usuarioUbicaciones(){
        $ubicaciones = UsuarioUbicacion::where('idUsuario', '=', auth()->id())->get(['idubicacion']);
        return $this->whereIn('idubicacion', $ubicaciones->toArray())->get();
     }


       
}