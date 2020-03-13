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

    public function estimacion(){
        return $this->belongsTo(Estimacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function retencion()
    {
        return $this->belongsTo(Retencion::class, 'id_retencion', 'id_retencion');
    }

    public function getImporteFormatAttribute(){
        return '$ ' . number_format($this->importe, 2, '.',',');
    }

    public function validarLiberacionImporte($importe){
        if(($importe + $this->estimacion->liberaciones->sum('importe')) > $this->estimacion->monto)
            abort(403, 'No se puede registar una liberación(es) mayor al monto de la estimación.');
    }

    public function validarEstadoEstimacion($tipo){
        if($this->estimacion->estado >= 1){
            $estado = $this->estimacion->estado == 1?'aprobada':'revisada';
            abort(403, 'La liberación no puede ser '.$tipo.' porque la estimación se encuentra ' . $estado . '.');
        }
    }

    public function scopePorEstimacion($query, $id_transaccion)
    {
        return $query->where('id_transaccion', '=', $id_transaccion);
    }
}
