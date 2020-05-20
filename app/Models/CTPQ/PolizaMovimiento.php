<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 12:11 PM
 */

namespace App\Models\CTPQ;

use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\Contabilidad\LogEdicion;

class PolizaMovimiento extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'MovimientosPoliza';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'IdCuenta', 'Id');
    }

    public function logs()
    {
        return $this->hasMany(LogEdicion::class, 'id_movimiento', 'Id');
    }

    public function  getCargoAttribute(){
        if($this->TipoMovto == 0)
        {
            return $this->Importe;
        } else {
            return 0;
        }
    }

    public function  getAbonoAttribute(){
        if($this->TipoMovto == 1)
        {
            return $this->Importe;
        } else {
            return 0;
        }
    }

    public function  getCargoFormatAttribute(){
        if($this->cargo!=0){
            return "$ " . number_format($this->cargo,2,".",",");
        }
        else {
            return "-";
        }

    }

    public function  getAbonoFormatAttribute(){
        if($this->abono!=0){
            return "$ " . number_format($this->abono,2,".",",");
        }
        else {
            return "-";
        }
    }

    public function  getImporteComaFormatAttribute()
    {
        return number_format($this->Importe,2,".",",");
    }

    public function  getImporteFormatAttribute(){
        return "$ " . number_format($this->Importe,2,".",",");
    }

    public function getTipoFormatAttribute()
    {
        switch ($this->TipoMovto){
            case 0 :
                return 'Cargo';
                break;
            case 1 :
                return 'Abono';
                break;
        }
    }
}
