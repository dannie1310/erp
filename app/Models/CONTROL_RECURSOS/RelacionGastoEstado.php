<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class RelacionGastoEstado extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'relaciones_gastos_estados';
    protected $primaryKey = 'idrelaciones_gastos_estados';
    public $timestamps =  false;

    protected $fillable = [
        'idrelaciones_gastos',
        'idctg_estados_relaciones',
        'registro'
    ];

    /**
     * Relaciones
     */
    public function estado()
    {
        return $this->belongsTo(CtgEstadoRelacion::class, 'idctg_estados_relaciones', 'idctg_estados_relaciones');
    }
}
