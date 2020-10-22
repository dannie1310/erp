<?php


namespace App\Models\CADECO\Subcontratos;

use App\Models\CADECO\Contrato;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Subcontratos\AsignacionContratista;

class AsignacionContratistaPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.partidas_asignacion';
    protected $primaryKey = 'id_partida_asignacion';

    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_asignacion',
        'id_concepto',
        'cantidad_asignada',
        'cantidad_autorizada',
    ];

    public function asignacion(){
        return $this->belongsTo(AsignacionContratista::class, 'id_asignacion', 'id_asignacion');
    }

    public function contrato(){
        return $this->belongsTo(Contrato::class, 'id_concepto', 'id_concepto');
    }
}