<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class RelacionGastoEliminado extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'relaciones_gastos_eliminados';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'idrelaciones_gastos',
        'numero_folio',
        'folio',
        'fecha_inicio',
        'fecha_fin',
        'idempresa',
        'idempleado',
        'idserie',
        'idmoneda',
        'iddepartamento',
        'idproyecto',
        'modifico_estado',
        'idestado',
        'motivo',
        'registro',
        'timestamp_registro',
        'usuario_elimina',
        'fecha_eliminacion',
        'motivo_eliminacion'
    ];
}
