<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\CTPQ;

use Illuminate\Database\Eloquent\Model;

class Poliza extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'Polizas';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    public function movimientos()
    {
        return $this->hasMany(PolizaMovimiento::class, 'IdPoliza', 'Id');
    }

    public function tipo_poliza()
    {
        return $this->belongsTo(TipoPoliza::class, 'TipoPol', 'Id');
    }

    public function getCargosFormatAttribute()
    {
        return '$ ' . number_format(abs($this->Cargos),2);
    }
    public function getFechaFormatAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date,"d/m/Y");
    }

}