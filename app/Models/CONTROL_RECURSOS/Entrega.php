<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'entregas';
    protected $primaryKey = 'IdEntrega';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('Estatus', 1);
        });
    }

    /**
     * Scopes
     */
    public function scopeTipo($query, $forma_pago)
    {
        if($forma_pago == 2 || $forma_pago == 4) {
            return $query->where('IdEntrega', 6);
        }else{
            return $query->whereIn('IdEntrega', [5,3,2]);
        }
    }
}
