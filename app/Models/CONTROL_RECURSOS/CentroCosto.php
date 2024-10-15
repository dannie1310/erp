<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CentroCosto extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'centroscosto';
    public $timestamps = false;
    protected $primaryKey = 'IdCC';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('Estatus', '1');
        });
    }
}
