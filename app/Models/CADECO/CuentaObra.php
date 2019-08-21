<?php


namespace App\Models\CADECO;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class CuentaObra extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.cuentas_obras';
    public $timestamps = false;
    protected $fillable = [
        'id_obra',
        'id_cuenta'
    ];
    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }
}
