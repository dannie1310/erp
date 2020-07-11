<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;

use Illuminate\Database\Eloquent\Model;

class ProcesamientoListaEfos extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.procesamiento_lista_efos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'fecha_actualizacion_sat',
        'fecha_actualizacion_sat_txt',
        'nombre_archivo',
        'hash_file'
    ];

    public static function getFechaActualizacion(){
        $proceso = ProcesamientoListaEfos::orderBy("id","desc")->first();
        return $proceso->fecha_actualizacion_sat_txt;
    }
}