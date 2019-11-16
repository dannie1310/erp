<?php


namespace App\Models\SEGURIDAD_ERP\Compras;


use App\Models\IGH\Usuario;
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

    public function scopeAsignadas($query)
    {
        return $query->join('Compras.areas_solicitantes_usuario', 'Compras.ctg_areas_solicitantes.id', 'Compras.areas_solicitantes_usuario.id_area_solicitante')
            ->where('Compras.areas_solicitantes_usuario.id_usuario','=', auth()->id())->select('Compras.ctg_areas_solicitantes.*');
    }
}
