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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Ésta clase solo se utiliza para los poyectos que tengan un esquema de permisos global
 *
 * @package App\Models\SEGURIDAD_ERP
 */
class Rol extends Model
{
    use SoftDeletes;

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

    /*protected $dateFormat = 'Y-m-d H:i:s';*/

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'dbo.permission_role', 'role_id', 'permission_id');
    }

    public function getUsadoAttribute()
    {
        $ids = DB::connection('seguridad')->table('dbo.role_user')->selectRaw('DISTINCT(role_id)')->get()->pluck('role_id')->toArray();
        return in_array($this->getKey(), $ids);
    }
}
