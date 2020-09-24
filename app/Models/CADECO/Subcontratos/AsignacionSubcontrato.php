<?php


namespace App\Models\CADECO\Subcontratos;

use App\Models\CADECO\Subcontrato;
use Illuminate\Database\Eloquent\Model;

class AsignacionSubcontrato extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.asignacion_subcontrato';
    protected $primaryKey = 'id_asignacion';

    public $timestamps = false;

    protected $fillable = [
        'id_asignacion',
        'id_transaccion',
    ];

    public function subcontrato(){
        return $this->belongsTo(Subcontrato::class, 'id_transaccion', 'id_transaccion');
    }
}