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
    protected $fillable = ['anio',
                            'mes',
                            'registro',
                            'id_obra'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });

        self::creating(function ($model) {
            $model->id_obra = Context::getIdObra();
            $model->registro = auth()->id();
        });
    }

    public function apertura()
    {
        return $this->hasOne(Apertura::class, 'id_cierre', 'id')->orderBy('inicio_apertura','desc');
    }

    public function usuario(){
        return $this->hasOne(Usuario::class, 'idusuario', 'registro');
    }

    public function mes($model){
        switch ($model->mes){
            case 1:
                return 'Enero';
            case 2 :
                return 'Febrero';
            case 3 :
                return 'Marzo';
            case 4:
                return 'Abril';
            case 5:
                return 'Mayo';
            case 6:
                return 'Junio';
            case 7:
                return 'Julio';
            case 8:
                return 'Agosto';
            case 9:
                return 'Septiembre';
            case 10:
                return 'Octubre';
            case 11:
                return 'Noviembre';
            case 12:
                return 'Diciembre';
        }
    }

    public function scopeOrdenar($query){
        return $query->orderBy('id', 'desc');
    }
}