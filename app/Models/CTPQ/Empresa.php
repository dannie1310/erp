<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:54 AM
 */

namespace App\Models\CTPQ;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'cntpqg';
    protected $table = 'dbo.ListaEmpresas';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    public static function getIdEmpresa($bd){
        $empresa = Empresa::where("AliasBDD","=", $bd)->first();
        return $empresa->IdEmpresaContpaq;
    }
    public static function getNombreEmpresa($bd){
        $empresa = Empresa::where("AliasBDD","=", $bd)->first();
        return $empresa->Nombre;
    }
}
