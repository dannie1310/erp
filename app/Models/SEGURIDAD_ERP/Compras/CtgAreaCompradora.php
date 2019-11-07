<?php


namespace App\Models\SEGURIDAD_ERP\Compras;


use Illuminate\Database\Eloquent\Model;

class CtgAreaCompradora extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Compras.ctg_areas_compradoras';
    protected $primaryKey = 'id';
    public $timestamps = false;


    public function scopeUsuarioBelongs($query)
    {
        return $query->join('Compras.areas_compradoras_usuario', 'Compras.ctg_areas_compradoras.id', 'Compras.areas_compradoras_usuario.id_area_compradora')
            ->where('Compras.areas_compradoras_usuario.id_usuario','=', auth()->id())->select('Compras.ctg_areas_compradoras.*');
    }

}
