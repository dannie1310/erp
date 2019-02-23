<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 22/02/2019
 * Time: 04:35 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cierre extends Model
{
    use SoftDeletes;
    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cierres';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra())
                ->where('estatus', '=', 1);
        });

        self::creating(function ($model) {
            $model->id_obra = Context::getIdObra();
            $model->registro = auth()->id();
        });
    }

    public function registro()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }
}