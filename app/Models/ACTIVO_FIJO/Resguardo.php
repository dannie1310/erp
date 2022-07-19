<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Support\Facades\DB;
use App\Models\ACTIVO_FIJO\GrupoActivo;
use Illuminate\Database\Eloquent\Model;

class Resguardo extends Model
{
    protected $connection = 'sci';
    protected $table = 'resguardos';
    public $primaryKey = 'idResguardo';
    protected $fillable = [
    ];

    /**
     * Relaciones Eloquent
     */

    public function grupoActivo(){
        return $this->belongsTo(GrupoActivo::class, 'GrupoEquipo', 'idGrupo');
    }

    /**
     * Scopes
     */

    /**
     * Atributtes
     */

    public function getTipoGrupoActivoAttribute(){
        if($this->GrupoEquipo == 0){
            return 'Material de Oficina, Mobiliario y Equipo de Oficina';
        }
        // dd(2, $this->grupoActivo);
        return $this->grupoActivo->descripcion;
    }
}