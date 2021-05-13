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
        return $this->hasMany(NoLocalizado::class,"id_procesamiento_baja", "id");
    }

    public function altasNoLocalizados()
    {
        return $this->hasMany(NoLocalizado::class,"id_procesamiento_alta", "id");
    }
}
