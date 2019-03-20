<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/11/18
 * Time: 05:24 PM
 */

namespace App\Models\IGH;

use App\Facades\Context;
use App\Models\CADECO\Seguridad\Rol;
use App\Traits\IghAuthenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Usuario extends Model implements JWTSubject, AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Notifiable, IghAuthenticatable, Authorizable, CanResetPassword, MustVerifyEmail;


    /**
     * @var string
     */
    protected $connection = 'igh';

    /**
     * @var string
     */
    protected $table = 'usuario';

    /**
     * @var string
     */
    protected $primaryKey = 'idusuario';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usuario', 'nombre', 'correo', 'clave',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'clave', 'remember_token'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param $value
     */
    public function setClaveAttribute($value)
    {
        $this->attributes['clave'] = md5($value);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function usuarioCadeco()
    {
        return $this->hasOne(\App\Models\CADECO\Usuario::class, 'usuario', 'usuario');
    }

    /**
     * Check if user has a permission by its name.
     *
     * @param string|array $permission Permission string or array of permissions.
     * @param bool $requireAll All permissions in the array are required.
     *
     * @return bool
     */
    public function can($permiso, $requireAll = false)
    {
        if (is_array($permiso)) {
            foreach ($permiso as $permName) {
                $hasPerm = $this->can($permName);
                if ($hasPerm && !$requireAll) {
                    return true;
                } elseif (!$hasPerm && $requireAll) {
                    return false;
                }
            }
            // If we've made it this far and $requireAll is FALSE, then NONE of the perms were found
            // If we've made it this far and $requireAll is TRUE, then ALL of the perms were found.
            // Return the value of $requireAll;
            return $requireAll;
        } else {
            foreach ($this->roles as $rol) {
                // Validate against the Permission table
                foreach ($rol->permisos as $perm) {
                    if (str_is($permiso, $perm->name)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, Context::getDatabase() . '.Seguridad.role_user', 'user_id', 'role_id');
    }

    public function permisos()
    {
        $permisos = new Collection();
        foreach ($this->roles as $rol) {
            // Validate against the Permission table
            foreach ($rol->permisos as $perm) {
                $permisos->push($perm);
            }
        }

        return $permisos;
    }


}