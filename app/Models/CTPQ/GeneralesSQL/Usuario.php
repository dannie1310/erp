<?php

namespace App\Models\CTPQ\GeneralesSQL;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $connection = 'cntpqg';
    protected $table = 'dbo.Usuarios';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    public function empresas()
    {
        return $this->hasManyThrough(Empresa::class,EmpresaUsuario::class,"IdUsuario"
            ,"Id","IdUsuario", "IdEmpresa");
    }

    public function empresasUsuario()
    {
        return $this->hasMany(EmpresaUsuario::class,"IdUsuario"
            ,"Id");
    }

}
