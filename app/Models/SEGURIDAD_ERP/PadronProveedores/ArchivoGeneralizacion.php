<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class ArchivoGeneralizacion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.archivos';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_archivo',
        'id_tipo_empresa',
        'hash_file',
        'usuario_registro',
        'fecha_hora_registro',
        'nombre_archivo',
        'extension_archivo',
        'id_empresa_proveedor',
        'id_empresa_prestadora',
        'obligatorio',
        'complemento_nombre',
        'nombre_archivo_usuario',
        'id_representante_legal'
    ];

    public function ctgTipoArchivo()
    {
        return $this->belongsTo(CtgTipoArchivo::class, 'id_tipo_archivo', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa','id');
    }

}
