<?php


namespace App\Models\ACTIVO_FIJO;

use Illuminate\Database\Eloquent\Model;

class ResguardoPartida extends Model
{
    protected $connection = 'sci';
    protected $table = 'resguardos_partidas';
    public $primaryKey = 'IdPartida';
    protected $fillable = [
    ];

    /**
     * Relaciones Eloquent
     */

    public function estadoPartida(){
        return $this->belongsTo(CtgEstadoPartida::class, 'IdEstado', 'idEstado');
    }

    public function partidaCaracteristicas(){
        return $this->hasMany(ResguardoPartidaCaracteristica::class, 'IdPartida', 'IdPartida');
    }

    public function Resguardo(){
        return $this->belongsTo(Resguardo::class, 'IdResguardo', 'IdResguardo');
    }

    /**
     * scoopes
     */

    /**
     * attributes
     */

    public function getEsUltimaPartidaAttribute()
    {
        $ultima_partida = ResguardoPartida::
        where("IdResguardo", "=", $this->IdResguardo)
            ->orderBy("FechaAsignacion","desc")->first();

        if($ultima_partida->FechaAsignacion == $this->FechaAsignacion){
            return true;
        }
        return false;
    }

}
