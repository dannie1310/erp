<?php


namespace App\Models\CADECO\Subcontratos;

use App\Models\CADECO\Contrato;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\PresupuestoContratista;
use App\Models\CADECO\PresupuestoContratistaPartida;
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

    /**
     * Relaciones
     */
    public function asignacion(){
        return $this->belongsTo(AsignacionContratista::class, 'id_asignacion', 'id_asignacion');
    }

    public function contrato(){
        return $this->belongsTo(Contrato::class, 'id_concepto', 'id_concepto');
    }

    public function partidaEliminada()
    {
        return $this->belongsTo(AsignacionContratistaPartidaEliminada::class, 'id_partida_asignacion');
    }

    public function presupuestoPartida(){
        return $this->belongsTo(PresupuestoContratistaPartida::class, 'id_transaccion', 'id_transaccion')->where('id_concepto', '=', $this->id_concepto);
    }

    public function presupuesto(){
        return $this->belongsTo(PresupuestoContratista::class, 'id_transaccion', 'id_transaccion');
    }

    /**
     * Atributos
     */
    public function getImporteCalculadoAttribute()
    {
        return $this->cantidad_autorizada * $this->partidaPresupuesto->precio_unitario_compuesto;
    }

    public function getSumaCantidadAsignadaAttribute()
    {
        $suma = 0;
        foreach ($this->asignacion->partidas as $partida)
        {
            if($partida->id_concepto == $this->id_concepto)
            {
                $suma += $partida->cantidad_asignada;
            }
        }
        return $suma;
    }

    public function getSumaImportesAsignadosAttribute()
    {
        return $this->suma_cantidad_asignada * $this->partidaPresupuesto->precio_unitario_compuesto;
    }

    public function getSumaImportesConDescuentoAttribute()
    {
        return $this->suma_importes_asignados - (($this->suma_importes_asignados * $this->descuento)/100);
    }

    public function getDescuentoAttribute()
    {
        return $this->partidaPresupuesto->presupuesto->PorcentajeDescuento;
    }

    public function getImporteConDescuentoAttribute()
    {
        return  $this->importe_calculado - (($this->importe_calculado * $this->descuento)/100);
    }
}
