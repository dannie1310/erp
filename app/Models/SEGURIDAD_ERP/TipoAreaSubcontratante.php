<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class TipoAreaSubcontratante extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.dbo.ctg_areas_subcontratantes';
    protected $primaryKey = 'id';

    /**
     * Relaciones
     */
    public function usuariosAreasSubcontratantes()
    {
        return $this->hasMany(UsuarioAreaSubcontratante::class, 'id_area_subcontratante');
    }


    /**
     * Scopes
     */
    public function scopeAreasPorUsuario($query)
    {
        return $query->whereHas('usuariosAreasSubcontratantes', function ($q2) {
            return $q2->porUsuario();
        });
    }
}
