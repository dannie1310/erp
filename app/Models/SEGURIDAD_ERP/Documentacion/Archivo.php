<?php


namespace App\Models\SEGURIDAD_ERP\Documentacion;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Documentacion.archivos';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_archivo',
        'id_cfdi',
        'hashfile',
        'usuario_registro',
        'fecha_hora_registro',
        'nombre',
        'extension',
        'obligatorio',
        'descripcion',
        'observaciones',
    ];

    public function ctgTipoArchivo()
    {
        return $this->belongsTo(CtgTipoArchivo::class, 'id_tipo_archivo', 'id');
    }

    public function cfdi()
    {
        return $this->belongsTo(CFDSAT::class, 'id_cfdi', 'id');
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

}
