<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 17/12/18
 * Time: 07:13 PM
 */

namespace App\Models\CADECO\Seguridad;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\Permiso;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Seguridad.roles';

    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });

        self::creating(function ($model) {
            $model->id_obra = Context::getIdObra();
        });
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, Context::getDatabase() . '.Seguridad.permission_role');
    }
}