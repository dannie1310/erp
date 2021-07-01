<?php


namespace App\Models\MODULOSSAO;


use App\Facades\Context;
use App\Models\MODULOSSAO\Proyectos\Proyecto;
use Illuminate\Database\Eloquent\Model;

class UnificacionObra extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'UnificacionProyectoObra';
    public $timestamps = false;

    protected static  function  boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query){
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function baseDatosObra()
    {
        return $this->belongsTo(BaseDatosObra::class, "IDBaseDatos", "IDBaseDatos");
    }

    public function baseDatosObraSinScopeGlobal()
    {
        return $this->belongsTo(BaseDatosObra::class, "IDBaseDatos", "IDBaseDatos")->withoutGlobalScopes();
    }

    public function proyecto()
    {
        return $this->hasMany(Proyecto::class, "IDProyecto", "IDProyecto");
    }
}
