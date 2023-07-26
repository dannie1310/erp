<?php

namespace App\Models\CTPQ\GeneralesSQL;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'cntpqg';
    protected $table = 'dbo.ListaEmpresas';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    public function empresaUsuarios()
    {
        return $this->hasMany(EmpresaUsuario::class,"IdEmpresa","IdEmpresa");
    }
    public function empresa()
    {
        return $this->hasOne(\App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::class,"IdEmpresaContpaq","Id");
    }
    public static function getIdEmpresa($bd){
        $empresa = Empresa::where("AliasBDD","=", $bd)->first();
        return $empresa->IdEmpresaContpaq;
    }
    public static function getNombreEmpresa($bd){
        $empresa = Empresa::where("AliasBDD","=", $bd)->first();
        return $empresa->Nombre;
    }
}
