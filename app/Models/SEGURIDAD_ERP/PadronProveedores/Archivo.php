<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.archivos';
    public $timestamps = false;
    protected $fillable = ["id_tipo_archivo", "id_tipo_empresa"];
}
