<?php


namespace App\Models\CADECO\Subcontratos;

use App\Models\CADECO\Contrato;
use App\Models\CADECO\PresupuestoContratistaPartida;
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

    public function partidaPresupuesto()
    {
        return $this->belongsTo(PresupuestoContratistaPartida::class, 'id_transaccion', 'id_transaccion')->where('id_concepto', $this->id_concepto);
    }

    /**
     * Atributos
     */
    public function getImporteCalculadoAttribute()
    {
        return $this->cantidad_autorizada * $this->partidaPresupuesto->precio_compuesto;
    }

    public function getTotalPrecioMonedaAttribute()
    {
        switch ($this->partidaPresupuesto->IdMoneda)
        {
            case (1):
                return $this->importe_calculado;
                break;
            case (2):
                return($this->partidaPresupuesto->presupuesto->TcUSD) ? $this->importe_calculado * $this->partidaPresupuesto->presupuesto->TcUSD : $this->importe_calculado * $this->partidaPresupuesto->tipo_cambio;
                break;
            case (3):
                return ($this->partidaPresupuesto->presupuesto->TcEuro) ? $this->importe_calculado * $this->partidaPresupuesto->presupuesto->TcEuro : $this->importe_calculado * $this->partidaPresupuesto->tipo_cambio;
                break;
            /*case (4):
                return ($this->partidaPresupuesto->presupuesto->TcLibra) ? $this->importe_calculado * $this->partidaPresupuesto->presupuesto->TcLibra : $this->importe_calculado * $this->partidaPresupuesto->tipo_cambio;
                break;*/
        }
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
}
