<?php


namespace App\Models\MODULOSSAO;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class BaseDatosObra extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'BaseDatosObra';
    protected $primaryKey = 'IDBaseDatos';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('BaseDatos', '=', Context::getDatabase());
        });
    }

    public function proyectosUnificados(){
        return $this->hasMany(UnificacionObra::class, 'IDBaseDatos', 'IDBaseDatos');
    }

    public function getIdsProyectosUnificadosAttribute(){
        if($this->proyectosUnificados){
            foreach ($this->proyectosUnificados as $p){
                $ids[] = $p->IDProyecto;
            }
            return $ids;
        }
        return [];
    }
}
