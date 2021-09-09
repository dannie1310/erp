<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use Illuminate\Database\Eloquent\Model;

class ProcesamientoListaNoLocalizados extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.procesamiento_lista_no_localizados';
    protected $primaryKey = 'id';

    protected $fillable = [
        'rfc',
        'id_usuario',
        'fecha_actualizacion_sat',
        'fecha_hora_carga',
        'nombre_archivo',
        'hash_file'
    ];

    public $timestamps = false;

    public function bajasNoLocalizados()
    {
        return $this->hasMany(NoLocalizado::class,"id_procesamiento_baja", "id")->withoutGlobalScopes();
    }

    public function altasNoLocalizados()
    {
        return $this->hasMany(NoLocalizado::class,"id_procesamiento_registro", "id");
    }

    public function getFechaHoraCargaFormatAttribute()
    {
        $date = date_create($this->fecha_hora_carga);
        return date_format($date,"d/m/Y");
    }

    public static function getFechaUltimaCarga(){
        $proceso = ProcesamientoListaNoLocalizados::orderBy("id","desc")->first();
        try{
            $fecha_actualizacion_txt = "Lista de contribuyentes no localizados cargada el ".$proceso->fecha_hora_carga_format;

        } catch (\Exception $e){
            $fecha_actualizacion_txt = "Lista de contribuyentes no localizados no cargada";
        }
        return $fecha_actualizacion_txt;
    }

    public function logs()
    {
        return $this->hasMany(ProcesamientoListaNoLocalizadosLog::class, "id_procesamiento_no_localizados", "id");
    }
}
