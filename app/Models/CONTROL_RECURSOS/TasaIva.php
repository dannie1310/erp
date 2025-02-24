<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class TasaIva extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'tasa_iva';
    protected $primaryKey = 'idtasa_iva';


    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estatus', 1);
    }
}
