<?php


namespace App\Models\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\Estimacion;
use Illuminate\Database\Eloquent\Model;

class PenalizacionLiberacion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosEstimaciones.penalizacion_liberacion';
    protected $primaryKey = 'id_liberacion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'importe',
        'concepto',
        'usuario',
        'id_penalizacion'
    ];

    public function scopePorEstimacion($query, $id_transaccion)
    {
        return $query->where('id_transaccion', '=', $id_transaccion);
    }

    public function getImporteFormatAttribute()
    {
        return '$ '. number_format($this->importe, 2, '.', ',');
    }

    public function estimacion()
    {
        return $this->belongsTo(Estimacion::class, 'id_transaccion');
    }

    public function penalizacion()
    {
        return $this->belongsTo(Penalizacion::class, 'id_penalizacion');
    }

    public function validadEstadoEstimacion($tipo)
    {
        if($this->estimacion->estado >= 1)
        {
            $estado = ($this->estimacion->estado == 1) ? 'aprobada' : 'revisada';
            abort(403, 'Laliberación de la penalización no puede ser '.$tipo.' porque la estimación se encuentra '.$estado. '.');
        }
    }

    public function getSumaLiberadoPorPenalizacionAttribute()
    {
        return self::where('id_penalizacion', '=', $this->id_penalizacion)->sum('importe');
    }

    public function validarImporteTotalALiberar()
    {
        if($this->suma_liberado_por_penalizacion == $this->penalizacion->importe)
        {
            abort(403, 'No hay importe disponible para liberar en esta penalización.');
        }

        if($this->suma_liberado_por_penalizacion + $this->importe > $this->penalizacion->importe)
        {
            abort(403, 'No hay importe suficiente en esta penalización para cubrir el importe a liberar.');
        }
    }

    public function cerrarPenalizacion()
    {
        return $this->penalizacion->update(['estatus' => 1]);
    }

    public function abrirPenalizacion()
    {
        return $this->penalizacion->update(['estatus' => 0]);
    }
}