<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\CTPQ;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'Cuentas';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    public function getCuentaMayorAttribute()
    {
        if($this->asociacion->cuenta_superior->CtaMayor == 1)
        {
            return $this->asociacion->cuenta_superior;
        } else {
           return  $this->asociacion->cuenta_superior->getCuentaMayorAttribute();
        }
    }

    public function getCuentaSuperiorAttribute()
    {
        return $this->asociacion->cuenta_superior;

    }

    public function asociacion() {
        return $this->hasOne(Asociacion::class, "IdSubCtade", "Id");
    }

    public function getCuentaPadreAttribute()
    {
        return $this->cuenta_mayor;
    }

    public function getCuentaFormatAttribute()
    {
        if(strlen($this->Codigo) == 13)
        {
            return substr($this->Codigo, 0, 4).'-'.substr($this->Codigo, 4, 3).'-'.substr($this->Codigo, 7, 3).'-'.substr($this->Codigo, 10, 3);
        }
        if(strlen($this->Codigo) == 11)
        {
            return substr($this->Codigo, 0, 4).'-'.substr($this->Codigo, 4, 2).'-'.substr($this->Codigo, 6, 2).'-'.substr($this->Codigo, 8, 3);
        }
    }

    public function getTipoAttribute()
    {
        switch ($this->CtaMayor){
            case 1 :
                return 'De Mayor';
                break;
            case 2 :
                if($this->Afectable == 0){
                    return 'No Mayor';
                }else {
                    return 'Afectable';
                }
                break;
            case 3 :
                return 'De Titulo';
                break;
            case 4 :
                return 'De Subtitulo';
                break;
        }
    }
}
