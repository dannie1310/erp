<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoArchivo;
use Illuminate\Support\Facades\DB;

class Archivo extends Model
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
    ];

    public function ctgTipoArchivo()
    {
        return $this->belongsTo(CtgTipoArchivo::class, 'id_tipo_archivo', 'id');
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function proveedora()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa_proveedor','id');
    }

    public function prestadora()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa_prestadora', 'id');
    }

    public function scopeCargados($query)
    {
        return $query->whereNotNull("hash_file");
    }

    public function scopeObligatorios($query)
    {
        return $query->join("PadronProveedores.ctg_tipos_archivos", "ctg_tipos_archivos.id","archivos.id_tipo_archivo")
            ->where('ctg_tipos_archivos.obligatorio', 1);
    }

    public function getRegistroAttribute()
    {
        return $this->usuarioRegistro?$this->usuarioRegistro->nombre_completo:'';
    }

    public function getFechaRegistroFormatAttribute()
    {
        if($this->fecha_hora_registro){
            $date = date_create($this->fecha_hora_registro);
            return date_format($date,"d/m/Y H:i");
        }
        return '';
    }

    public function getNombreArchivoFormatAttribute()
    {
        return $this->nombre_archivo?$this->nombre_archivo . $this->extencion_archivo:'Pendiente';
    }

    public function getEstatusAttribute()
    {
        return $this->hash_file?true:false;
    }

    public function eliminar()
    {
        try {
            DB::connection('seguridad')->beginTransaction();
            $this->update([
                'hash_file' => null,
                'nombre_archivo' => null,
                'extension_archivo' => null
            ]);
            DB::connection('seguridad')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
}
