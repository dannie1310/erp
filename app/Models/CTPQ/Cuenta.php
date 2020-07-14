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

    public function getCuentaPadreAttribute()
    {
        return self::where('Codigo', '=', str_pad(substr($this->Codigo, 0, 4), strlen($this->Codigo), "0", STR_PAD_RIGHT))->first();
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
}
