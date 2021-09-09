<?php

namespace App\Models\CADECO\ControlPresupuesto;


use App\Models\CADECO\Contabilidad\CuentaConcepto;
use App\Models\CADECO\PresupuestoObra\DatoConcepto;
use App\Models\CADECO\PresupuestoObra\Responsable;
use App\Scopes\ActivoScope;
use App\Scopes\ObraScope;
use Illuminate\Database\Eloquent\Model;

class ConceptoHistorico extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'ControlPresupuesto.conceptos_historicos';
    protected $primaryKey = 'id';


    public $timestamps = false;

    protected $guarded = [
        'id',
    ];

    public function confirmacionCambioReferente()
    {
        return $this->belongsTo(SolicitudCambioConfirmacion::class, 'id_confirmacion_cambio', 'id');
    }
}
