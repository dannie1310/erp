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
        $texto = $proceso->fecha_actualizacion_sat_txt;
        $texto_ex = explode(" al ",$texto);
        $fecha_actualizacion_txt = "Lista de EFOS actualizada al ".$texto_ex[1];
        return $fecha_actualizacion_txt;
    }

    public function cambios()
    {
        return $this->hasMany(EFOSCambio::class, "id_procesamiento_efos", "id");
    }
}
