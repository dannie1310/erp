<?php


namespace App\Models\CADECO\Documentacion;


use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class Archivo extends Model
{

    protected $connection = 'cadeco';
    protected $table = 'Documentacion.archivos';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_archivo',
        'id_categoria',
        'id_transaccion',
        'hashfile',
        'usuario_registro',
        'fecha_hora_registro',
        'nombre',
        'extension',
        'descripcion',
        'observaciones',
    ];

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }


    public function transaccion()
    {
        return $this->belongsTo(Transaccion::class, 'id_transaccion','id_transaccion');
    }

    public function categoria()
    {
        return $this->belongsTo(CtgCategoria::class, 'id_categoria','id');
    }

    public function tipoArchivo()
    {
        return $this->belongsTo(CtgTipoArchivo::class, 'id_categoria','id');
    }

    public function getRegistroAttribute()
    {
        return $this->usuarioRegistro->nombre_completo;
    }

    public function getFechaRegistroFormatAttribute()
    {
        if($this->fecha_hora_registro){
            $date = date_create($this->fecha_hora_registro);
            return date_format($date,"d/m/Y H:i");
        }
        return '';
    }

    public function getNombreArchivoCompletoAttribute()
    {
        return $this->nombre_archivo .'.'. $this->extension_archivo;
    }

}
