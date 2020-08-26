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
        'obligatorio',
        'complemento_nombre',
        'nombre_archivo_usuario',
        'id_representante_legal'
    ];

    public function ctgTipoArchivo()
    {
        return $this->belongsTo(CtgTipoArchivo::class, 'id_tipo_archivo', 'id');
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa','id');
    }

    public function prestadora()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa_prestadora','id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa_proveedor','id');
    }

    public function representanteLegal()
    {
        return $this->belongsTo(RepresentanteLegal::class, 'id_representante_legal','id');
    }

    public function scopeCargados($query)
    {
        return $query->whereNotNull("hash_file");
    }

    public function scopeObligatorios($query, $id_proveedor = null)
    {
        if($id_proveedor){
            return $query->join("PadronProveedores.ctg_tipos_archivos", "ctg_tipos_archivos.id","archivos.id_tipo_archivo")
                ->where("archivos.id_empresa_proveedor", $id_proveedor)
                ->where('archivos.obligatorio', 1);
        } else {
            return $query->where("obligatorio",1);
        }
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

    public function getNombreArchivoCompletoAttribute()
    {
        if($this->nombre_archivo != ""){
            return $this->nombre_archivo?$this->nombre_archivo .'.'. $this->extension_archivo:'Pendiente';
        } else {
            return null;
        }

    }

    public function getEstatusAttribute()
    {
        return $this->hash_file?true:false;
    }

    public function getDescripcionComplementadaAttribute()
    {
        return $this->ctgTipoArchivo->descripcion . " " . $this->complemento_nombre;
    }

    public function eliminar()
    {
        try {
            DB::connection('seguridad')->beginTransaction();
            $this->update([
                'hash_file' => null,
                'nombre_archivo' => null,
                'extension_archivo' => null,
                'nombre_archivo_usuario' => null
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
