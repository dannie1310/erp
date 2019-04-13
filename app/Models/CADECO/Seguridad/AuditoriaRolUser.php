<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 04/04/2019
 * Time: 19:28
 */

namespace App\Models\CADECO\Seguridad;

use Illuminate\Database\Eloquent\Model;

class AuditoriaRolUser extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Seguridad.auditoria_role_user';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id',
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