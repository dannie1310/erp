<?php
namespace App\Models\SEGURIDAD_ERP;

use Illuminate\Database\Eloquent\Model;

class Aviso extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.avisos';
    public $timestamps = false;

    /*public static function getAvisoSAO()
    {
        $aviso = Aviso::where("tipo","=",1)->where("estatus","=",1)->first();
        if($aviso){
            return "data:image/png;base64,".$aviso->aviso;
        }
        return null;
    }*/

    public function leido()
    {
        return $this->hasOne(AvisoLeido::class,"id_aviso","id")->where("id_usuario","=", auth()->id());
    }

    public static function getRutaAvisoSAO()
    {
        $aviso = Aviso::where("tipo","=",1)->where("estatus","=",1)->first();
        if($aviso){
            return $aviso->ruta_aviso;
        }
        return null;
    }

    public static function getAvisoSAO()
    {
        $aviso = Aviso::noLeidoPorUsuario()->where("tipo","=",1)->where("estatus","=",1)->first();
        if($aviso){
            return $aviso;
        }
        return null;
    }

    public static function getAvisoPortal()
    {
        $aviso = Aviso::noLeidoPorUsuario()->where("tipo","=",2)->where("estatus","=",1)->first();
        if($aviso){
            return $aviso;
        }
        return null;
    }

    public function scopeNoLeidoPorUsuario($query)
    {
        return $query->whereDoesntHave('leido');
    }

}
