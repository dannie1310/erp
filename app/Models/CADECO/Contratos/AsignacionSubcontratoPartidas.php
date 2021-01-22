<?php

namespace App\Models\CADECO\Contratos;

use Illuminate\Database\Eloquent\Model;

class AsignacionSubcontratoPartidasBORRAR extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.partidas_asignacion';
    protected $primaryKey = 'id_transaccion';

    public $timestamps = false;
}

