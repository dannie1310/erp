<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class EmpresaUsuario extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.empresas_usuarios';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class,"id_empresa"
            ,"Id");
    }

}
