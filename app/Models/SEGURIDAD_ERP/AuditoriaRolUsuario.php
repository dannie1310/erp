<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class AuditoriaRolUsuario extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.auditoria_role_user';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id',
        'id_proyecto',
        'id_obra',
        'action'
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->usuario_registro = auth()->id();
            $model->created_at = date('Y-m-d h:i:s');
        });
    }
}