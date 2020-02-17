<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/03/2019
 * Time: 03:57 PM
 */

namespace App\Models\CADECO\SubcontratosEstimaciones;


use App\Models\CADECO\Estimacion;
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
    ];

    public function estimacion(){
        return $this->belongsTo(Estimacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function retencion_tipo(){
        return $this->belongsTo(RetencionTipo::class, 'id_tipo_retencion', 'id_tipo_retencion');
    }

    public function getImporteFormatAttribute(){
        return '$ ' . number_format($this->importe, 2, '.',',');
    }

    public function getTipoRetencionAttribute(){
        return $this->retencion_tipo->tipo_retencion;
    }

    public function validarRegistroRetencionesIva($retencion){
        if(($retencion->importe + $retencion->estimacion->retenciones->sum('importe')) > $this->estimacion->monto)
            abort(403, 'La suma de retenciones no puede ser mayor al monto de la estimación.');
    }

    public function validarEstadoEstimacion($tipo){
        if($this->estimacion->estado >= 1){
            $estado = $this->estimacion->estado == 1?'aprobada':'revisada';
            abort(403, 'La retención no puede ser '.$tipo.' porque la estimación se encuentra ' . $estado . '.');
        }
    }
    
}