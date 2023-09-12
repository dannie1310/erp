<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;
use App\Models\IGH\TipoCambio;

class CtgMoneda extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'ctg_monedas';
    protected $primaryKey = 'id';



    public function getTipoCambioAttribute()
    {
        if ($this->id == 3) {
            return 1;
        } else {
            return TipoCambio::where('moneda', $this->id)->orderBy('fecha', 'desc')->first()->tipo_cambio;
        }
    }
}
