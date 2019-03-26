<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/25/19
 * Time: 5:48 PM
 */

namespace App\Models\SEGURIDAD_ERP;

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
        'name',
        'display_name',
        'description'
    ];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'dbo.permission_role', 'role_id', 'permission_id');
    }
}