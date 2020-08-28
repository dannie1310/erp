<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class EmpresaRepresentanteLegal extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.empresas_representantes_legales';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa',
        'id_representante_legal',
    ];

}
