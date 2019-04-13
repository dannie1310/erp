<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 04/04/2019
 * Time: 19:40
 */

namespace App\Models\CADECO\Seguridad;

use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class AuditoriaPermisoRol extends Model
{

    protected $connection = 'cadeco';
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'permission_id',
        'action'
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Context::getDatabase() . '.Seguridad.auditoria_permission_role';
    }

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->usuario_registro = auth()->id();
            $model->created_at = date('Y-m-d h:i:s');
        });
    }
}