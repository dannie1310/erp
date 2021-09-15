<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 11/12/18
 * Time: 01:50 PM
 */

namespace App\Models\SEGURIDAD_ERP;


use App\Facades\Context;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Sistema extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'sistemas';
    public $timestamps = false;


    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'sistema_id', 'id');
        //return $this->belongsToMany(Permiso::class, 'dbo.sistemas_permisos', 'sistema_id', 'permission_id');
    }

    public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class,  'dbo.proyectos_sistemas', 'id_sistema', 'id_proyecto');
    }

    public function scopePorUsuario($query)
    {
        $permisos = auth()->user()->permisos();

        return $query
            ->whereHas('permisos', function($q) use ($permisos) {
            return $q->whereIn('name', $permisos);
        })
            ->whereHas('proyectos', function ($q) {
                return $q->where('base_datos', '=', Context::getDatabase())->where('id_obra','=',Context::getIdObra());
            });
    }

    public function  scopeAplicaciones($query)
    {
        $permisos = auth()->user()->permisosAplicaciones();
        return $query->where("aplicacion","=",1)->whereHas('permisos', function($q) use ($permisos) {
            return $q->whereIn('name', $permisos);
        });
    }
}
