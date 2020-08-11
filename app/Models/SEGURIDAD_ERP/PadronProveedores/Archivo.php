<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoArchivo;

class Archivo extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.archivos';
    public $timestamps = false;

    public function ctgTipoArchivo(){
        return $this->belongsTo(CtgTipoArchivo::class, 'id_tipo_archivo', 'id');
    }

    public function usuarioRegsitro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function getRegistroAttribute(){
        return $this->usuarioRegsitro->nombre_completo;
    }

    public function getFechaRegistroFormatAttribute(){
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:m");
    }

    public function getNombreArchivoFormatAttribute(){
        return $this->nombre_archivo?$this->nombre_archivo . $this->extencion_archivo:'Pendiente';
    }

    public function getEstatusAttribute(){
        return $this->hash_file?'Completo':'Pendiente';
    }
}
