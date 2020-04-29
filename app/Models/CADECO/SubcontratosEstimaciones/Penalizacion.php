<?php


namespace App\Models\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Subcontrato;
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
        return number_format(((float) $this->importe - (float) $this->liberaciones->sum('importe')), 4, '.', '');
    }

    public function getImporteDisponibleFormatAttribute()
    {
        return '$ '. number_format($this->importe_disponible, 2, '.', ',');
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
        if($this->estimacion->estado > 0)
        {
            abort(403, 'La penalizacion no puede ser '.$tipo.' porque la estimación se encuentra '.$this->estimacion->estado_descripcion.'.');
        }
    }

    public function estimacion()
    {
        return $this->belongsTo(Estimacion::class, 'id_transaccion');
    }

    public function liberaciones()
    {
        return $this->hasMany(PenalizacionLiberacion::class, 'id_penalizacion');
    }

    public function scopeDisponible($query)
    {
        return $query->where('estatus', '=', 0);
    }

    public function scopeDisponiblesParaLiberar($query, $id_estimacion)
    {
        $estimacion = Estimacion::find($id_estimacion);
        $subcontrato = Subcontrato::where('id_transaccion', '=', $estimacion->id_antecedente)->first();
        return $query->whereIn('id_transaccion', $subcontrato->estimaciones->whereNotIn('id_transaccion', $id_estimacion)->pluck('id_transaccion'));
    }
}
