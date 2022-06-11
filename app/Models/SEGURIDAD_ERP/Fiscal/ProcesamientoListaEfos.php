<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Reportes\CatalogoMeses;
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

    public function getFechaActualizacionListaEFOSAttribute(){
        //$proceso = ProcesamientoListaEfos::orderBy("id","desc")->first();
        try{
            $texto = $this->fecha_actualizacion_sat_txt;
            $texto_ex = explode(" al ",$texto);
            $fecha_ex = explode(" de ",$texto_ex[1]);
            $mes_num = $fecha_ex[1];
            if(is_string($fecha_ex[1])){
                $mes_obj = CatalogoMeses::where("NombreMes","=", strtoupper($fecha_ex[1]))->first();
                if($mes_obj){
                    $mes_num = $mes_obj->MesID;
                }
            }
            return $fecha_ex[0]."/".$mes_num."/".$fecha_ex[2];
        }catch (\Exception $e)
        {
            $texto = $this->fecha_actualizacion_sat_txt;
            $texto_ex = explode(" al ",$texto);
            $fecha_actualizacion_txt = $texto_ex[1];
            return $fecha_actualizacion_txt;
        }

    }



    public function cambios()
    {
        return $this->hasMany(EFOSCambio::class, "id_procesamiento_efos", "id")
            ->where("estado","=",1);
    }

    public function logs()
    {
        return $this->hasMany(ProcesamientoListaEfosLog::class, "id_procesamiento_lista_efos", "id");
    }

    public function getFechaHoraFormatAttribute()
    {
        $date = date_create($this->fecha_hora);
        return date_format($date,"d/m/Y H:m");
    }
}
