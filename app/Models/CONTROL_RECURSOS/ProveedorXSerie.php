<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class ProveedorXSerie extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'proveedores_x_serie';
    protected $primaryKey = 'IDproveedor';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereHas('series');
        });
    }

    public function series()
    {
        return $this->hasMany(Serie::class, 'idseries', 'IDserie');
    }
}
