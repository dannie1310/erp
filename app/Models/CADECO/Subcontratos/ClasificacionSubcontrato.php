<?php

namespace App\Models\CADECO\Subcontratos;

use Illuminate\Database\Eloquent\Model;

class ClasificacionSubcontrato extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.clasificacion_subcontrato';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    public function tipo()
    {
        return $this->belongsTo(TiposSubcontrato::class, 'id_tipo_contrato');
    }
}