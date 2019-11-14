<?php


namespace App\Models\IGH;


use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $connection = 'igh';
    protected $table = 'menu';
    protected $primaryKey = 'idmenu';

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('menu_estado', '=', 1)
                ->where('estado_portal', '=', 1);
        });
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'igh.menu_usuario', 'idmenu', 'idusuario');
    }

    public function scopePorUsuario($query, $idusuario)
    {
        return $query->whereHas('usuarios', function ($q) use ($idusuario) {
            return $q->where('usuario.idusuario', '=', $idusuario);
        });
    }

    public function getTargetAttribute()
    {
        if(strpos($this->ruta, "http")!==false){
            return '_blank';
        }else{
            return '_self';
        }
    }
}