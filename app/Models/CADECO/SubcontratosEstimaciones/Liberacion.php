<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/03/2019
 * Time: 04:01 PM
 */

namespace App\Models\CADECO\SubcontratosEstimaciones;


use App\Models\CADECO\Estimacion;
use Illuminate\Database\Eloquent\Model;

class Liberacion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosEstimaciones.retencion_liberacion';
    protected $primaryKey = 'id_liberacion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'importe',
        'concepto',
        'usuario',
        'id_retencion'
    ];

    public function estimacion()
    {
        return $this->belongsTo(Estimacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function retencion()
    {
        return $this->belongsTo(Retencion::class, 'id_retencion', 'id_retencion');
    }

    public function getImporteFormatAttribute()
    {
        return '$ ' . number_format($this->importe, 2, '.',',');
    }

    public function validarEstadoEstimacion($tipo)
    {
        if($this->estimacion->estado >= 1){
            $estado = $this->estimacion->estado == 1?'aprobada':'revisada';
            abort(403, 'La liberación no puede ser '.$tipo.' porque la estimación se encuentra ' . $estado . '.');
        }
    }

    public function scopePorEstimacion($query, $id_transaccion)
    {
        return $query->where('id_transaccion', '=', $id_transaccion);
    }

    public function getSumaLiberadoPorRetencionAttribute()
    {
        return self::where('id_retencion', '=', $this->id_retencion)->sum('importe');
    }

    public function validarImporteTotalALiberar()
    {
        if($this->suma_liberado_por_retencion == $this->retencion->importe)
        {
            abort(403, 'No hay importe disponible para liberar en esta retención.');
        }

        if($this->suma_liberado_por_retencion + $this->importe > $this->retencion->importe)
        {
            abort(403, 'No hay importe suficiente en esta retención para cubrir el importe a liberar.');
        }
    }

    public function cerrarRetencion()
    {
        return $this->retencion->update(['estatus' => 1]);
    }

    public function abrirRetencion()
    {
        return $this->retencion->update(['estatus' => 0]);
    }
}
