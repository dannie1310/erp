<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.archivos';
    public $timestamps = false;
    protected $fillable = ["id_tipo_archivo", "id_tipo_empresa"];

    public function scopeCargados($query)
    {
        return $query->whereNotNull("hash_file");
    }
    public function scopeObligatorios($query)
    {
        return $query->join("PadronProveedores.ctg_tipos_archivos", "ctg_tipos_archivos.id","archivos.id_tipo_archivo")
            ->where('ctg_tipos_archivos.obligatorio', 1);
    }
    public function tipo_archivo()
    {
        return $this->belongsTo(CtgTipoArchivo::class,"id_tipo_archivo", "id");
    }
}
