<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/25/19
 * Time: 5:48 PM
 */

namespace App\Models\SEGURIDAD_ERP;

use App\Utils\Normalizar;
use Illuminate\Database\Eloquent\Model;

/**
 * Ésta clase solo se utiliza para los poyectos que tengan un esquema de permisos global
 *
 * @package App\Models\SEGURIDAD_ERP
 */
class Rol extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.roles';

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
        return $this->belongsToMany(Permiso::class, 'dbo.permission_role', 'role_id', 'permission_id');
    }
}