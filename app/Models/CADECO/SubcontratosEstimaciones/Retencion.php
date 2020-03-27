<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/03/2019
 * Time: 03:57 PM
 */

namespace App\Models\CADECO\SubcontratosEstimaciones;


use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Subcontrato;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\SubcontratosEstimaciones\RetencionTipo;

class Retencion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosEstimaciones.retencion';
    protected $primaryKey = 'id_retencion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_tipo_retencion',
        'importe',
        'concepto',
        'estatus'
    ];

    public function estimacion()
    {
        return $this->belongsTo(Estimacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function retencion_tipo()
    {
        return $this->belongsTo(RetencionTipo::class, 'id_tipo_retencion', 'id_tipo_retencion');
    }

    public function liberaciones()
    {
        return $this->hasMany(Liberacion::class, 'id_retencion', 'id_retencion');
    }

    public function getImporteFormatAttribute(){
        return '$ ' . number_format($this->importe, 2, '.',',');
    }

    public function getTipoRetencionAttribute(){
        return $this->retencion_tipo->tipo_retencion;
    }

    public function validarEstadoEstimacion($tipo){
        if($this->estimacion->estado >= 1){
            $estado = $this->estimacion->estado == 1?'aprobada':'revisada';
            abort(403, 'La retención no puede ser '.$tipo.' porque la estimación se encuentra ' . $estado . '.');
        }
    }

    public function validarTotalRetencion()
    {
        if($this->estimacion->retenciones->sum('importe') == $this->estimacion->resta_importes_amortizacion)
        {
            abort(403, 'No hay monto disponible para retener en esta estimación.');
        }

        if(($this->estimacion->retenciones->sum('importe') + $this->importe) > $this->estimacion->resta_importes_amortizacion)
        {
            abort(403, 'La suma de retenciones no puede ser mayor al monto de la estimación.');
        }
    }

    public function scopeDisponible($query)
    {
        return $query->where('estatus', '=', 0);
    }

    public function scopePorEstimacion($query, $id_transaccion)
    {
        return $query->where('id_transaccion', '=', $id_transaccion);
    }

    public function scopeDisponiblesParaLiberar($query, $id_estimacion)
    {
        $estimacion = Estimacion::find($id_estimacion);
        $subcontrato =  Subcontrato::where('id_transaccion', '=', $estimacion->id_antecedente)->first();
        return $query->whereIn('id_transaccion',$subcontrato->estimaciones->whereNotIn('id_transaccion', $id_estimacion)->pluck('id_transaccion'));
    }

    public function getImporteDisponibleAttribute()
    {
        return number_format(((float) $this->importe - (float)$this->liberaciones->sum('importe')), 4, '.','');
    }

    public function getImporteDisponibleFormatAttribute()
    {
        return '$ ' . number_format($this->importe_disponible, 2, '.',',');
    }
}
