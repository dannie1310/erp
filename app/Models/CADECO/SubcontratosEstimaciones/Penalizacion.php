<?php


namespace App\Models\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\Estimacion;
use Illuminate\Database\Eloquent\Model;

class Penalizacion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosEstimaciones.penalizacion';
    protected $primaryKey = 'id_penalizacion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'importe',
        'concepto',
        'estatus'
    ];
    
    public function getImporteFormatAttribute()
    {
        return '$ '. number_format($this->importe, 2, '.', ',');
    }

    public function getImporteDisponibleAttribute()
    {
        return $this->importe;
    }

    public function scopePorEstimacion($query, $id_transaccion)
    {
        return $query->where('id_transaccion', '=', $id_transaccion);
    }

    public function validarTotalPenalizacion()
    {
        if($this->estimacion->penalizaciones->sum('importe') == $this->estimacion->resta_importes_amortizacion)
        {
            abort(403, 'No hay monto disponible para retener en esta estimación.');
        }

        if(($this->estimacion->penalizaciones->sum('importe') + $this->importe) > $this->estimacion->resta_importes_amortizacion)
        {
            abort(403, 'La suma de penalizaciones no puede ser mayor al monto de la estimación.');
        }
    }

    public function validarEstadoPenalizacion($tipo)
    {
        if($this->estimacion->estado >= 1)
        {
            $estado = ($this->estimacion->estado == 1) ? 'aprobada' : 'revisada';
            abort(403, 'La penalizacion no puede ser '.$tipo.' porque la estimación se encuentra '.$estado.'.');
        }
    }

    public function estimacion()
    {
        return $this->belongsTo(Estimacion::class, 'id_transaccion');
    }
}