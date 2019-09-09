<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 22/02/2019
 * Time: 04:35 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cierre extends Model
{
    use SoftDeletes;
    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cierres';
    protected $fillable = [
        'anio',
        'mes'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function getAbiertoAttribute()
    {
        return (boolean)$this->aperturas()->abiertas()->count();
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'idusuario', 'registro');
    }

    public function aperturas()
    {
        return $this->hasMany(Apertura::class, 'id_cierre');
    }
}