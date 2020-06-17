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

}