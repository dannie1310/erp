<?php


namespace App\Models\ACTIVO_FIJO;

use App\Models\IGH\Ubicacion;
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

    /**
     * Relaciones Eloquent
     */

    public function ubicacion(){
        return $this->belongsTo(Ubicacion::class, 'idUbicacion', 'idubicacion');
    }

    /**
     * scopes
     */

    public function scopePorUsuario($query){
        return $query->where('idUsuario', '=', auth()->id());
    }
}