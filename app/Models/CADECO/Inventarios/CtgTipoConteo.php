<?php


namespace App\Models\CADECO\Inventarios;


use Illuminate\Database\Eloquent\Model;

class CtgTipoConteo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.ctg_tipos_conteos';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id', '!=', 4);
        });
    }
}