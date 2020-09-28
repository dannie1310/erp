<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Utils\Util;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoArchivo;

class Archivo extends ArchivoGeneralizacion
{

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereNull('id_archivo_consolidador');
        });
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
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

    public function archivosIntegrantes()
    {
        return $this->hasMany(ArchivoIntegrante::class,"id_archivo_consolidador", "id");
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

    public function getNombreDescargaAttribute()
    {
        $util = new Util();
        $nombre_format = $util->elimina_caracteres_especiales($this->complemento_nombre);
        return $this->ctgTipoArchivo->nombre."_".strtolower(str_replace(" ","_",$nombre_format));
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
            $this->eliminarArchivosIntegrantes();
            DB::connection('seguridad')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function eliminarArchivosIntegrantes()
    {
        $archivos_integrantes = $this->archivosIntegrantes;
        if($archivos_integrantes){
            foreach ($archivos_integrantes as $archivo_integrante){
                $archivo_integrante->delete();
            }
        }
    }

    public function agregarArchivoIntegrante($data){
        return $this->archivosIntegrantes()->create($data);
    }
}
