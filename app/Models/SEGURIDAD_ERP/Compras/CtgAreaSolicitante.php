<?php


namespace App\Models\SEGURIDAD_ERP\Compras;


use Illuminate\Database\Eloquent\Model;

class CtgAreaSolicitante extends Model
{
    protected $connection = 'seguridad';
    protected $table ='Compras.ctg_areas_solicitantes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function area_usuario()
    {
        return $this->hasMany(AreaSolicitanteUsuario::class, 'id_area_solicitante','id');
    }

    public function scopeUsuario($query)
    {
        return $query->join('area_usuario')->where('area_usuario.id_usuario','=', auth()->id());
//        return $this->hasMany('area_usuario')->where('AreaSolicitante.id_usuario','=',auth()->id());
//        return $query->with('area_usuario')->where('area_usuario.id_usuario','=',auth()->id());
    }
}
