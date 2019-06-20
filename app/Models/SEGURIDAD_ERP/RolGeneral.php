<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 20/06/19
 * Time: 01:44 PM
 */

namespace App\Models\SEGURIDAD_ERP;

use App\Utils\Normalizar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolGeneral extends Model
{
    use SoftDeletes;

    protected $connection = 'seguridad';
    protected $table = 'dbo.roles_generales';

    protected $fillable = [
        'display_name',
        'description'
    ];

    public $searchable = [
        'name',
        'display_name',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $name = Normalizar::normaliza($model->display_name);
            $model->name = str_replace(' ', '_', $name);
        });
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'dbo.permiso_rol_general', 'id_rol_general', 'id_permiso');
    }
}