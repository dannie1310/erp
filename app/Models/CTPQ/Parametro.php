<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 17/06/2020
 * Time: 01:44 AM
 */

namespace App\Models\CTPQ;


use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'Parametros';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    public function getLongitudCuentaAttribute()
    {
        $longitud = 0;
        $arr = explode("-",$this->EstructCta);
        foreach($arr as $grupo){
            $longitud+=$grupo;
        }
        return $longitud;
    }

    public static function getRFC(){
        $parametros = Parametro::find(1);
        return $parametros->RFC;
    }

    public static function getRegCamara(){
        $parametros = Parametro::find(1);
        return $parametros->RegCamara;
    }

    public static function getRegEstatal(){
        $parametros = Parametro::find(1);
        return $parametros->RegEstatal;
    }

    public static function getDireccion(){
        $parametros = Parametro::find(1);
        return $parametros->Direccion;
    }

    public static function getRazonSocial(){
        $parametros = Parametro::find(1);
        return $parametros->RazonSocial;
    }

    public static function getCodPostal(){
        $parametros = Parametro::find(1);
        return $parametros->CodPostal;
    }


}