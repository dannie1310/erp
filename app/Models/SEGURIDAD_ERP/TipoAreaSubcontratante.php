<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class TipoAreaSubcontratante extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.dbo.ctg_areas_subcontratantes';
    protected $primaryKey = 'id';

    public function usuariosAreasSubcontratantes()
    {
        return $this->hasMany(UsuarioAreaSubcontratante::class, 'id_area_subcontratante');
    }
}