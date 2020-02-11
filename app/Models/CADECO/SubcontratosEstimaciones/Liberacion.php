<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/03/2019
 * Time: 04:01 PM
 */

namespace App\Models\CADECO\SubcontratosEstimaciones;


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
    ];

    public function estimacion(){
        return $this->belongsTo(Estimacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function getImporteFormatAttribute(){
        return '$ ' . number_format($this->importe, 2, '.',',');
    }
}