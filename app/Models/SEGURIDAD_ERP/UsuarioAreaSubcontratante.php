<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class UsuarioAreaSubcontratante extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.dbo.usuarios_areas_subcontratantes';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_area_subcontratante',
        ];

    /**
     * Scopes
     */
    public function scopePorUsuario($query)
    {
        return $query->where('id_usuario', '=', auth()->id());
    }
}
