<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/11/18
 * Time: 05:24 PM
 */

namespace App\Models\IGH;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Seguridad\Rol;
use App\Models\SEGURIDAD_ERP\AreaSubcontratante;
use App\Models\SEGURIDAD_ERP\Google2faSecret;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Models\SEGURIDAD_ERP\RolGeneral;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use App\Traits\IghAuthenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
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
    use Notifiable, IghAuthenticatable, Authorizable, CanResetPassword, MustVerifyEmail, HasApiTokens;


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
     * @var array
     */
    public $searchable = [
        'usuario',
        'nombre',
        'correo'
    ];

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
     * @param string/array $permission Permission string or array of permissions.
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
        $obra =  Obra::query()->find(Context::getIdObra());

        if ($obra->configuracion) {
            if ($obra->configuracion->esquema_permisos == 1) {
                // Esquema Global
                return $this->belongsToMany(\App\Models\SEGURIDAD_ERP\Rol::class, 'dbo.role_user', 'user_id', 'role_id')
                    ->withPivot('id_obra', 'id_proyecto')
                    ->where('id_obra', $obra->getKey())
                    ->where('id_proyecto', Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey());
            } else if ($obra->configuracion->esquema_permisos == 2) {
                // Esquema Personalizado
                return $this->belongsToMany(Rol::class, Context::getDatabase() . '.Seguridad.role_user', 'user_id', 'role_id');
            }
        } else {
            // Esquema Global
            return $this->belongsToMany(\App\Models\SEGURIDAD_ERP\Rol::class, 'dbo.role_user', 'user_id', 'role_id')
                ->where('id_obra', $obra->getKey())
                ->where('id_proyecto', Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey());
        }
    }

    public function rolesGlobales()
    {
        $obra =  Obra::query()->find(Context::getIdObra());

        if (isset($obra->configuracion) && $obra->configuracion->esquema_permisos == 1) {
            // Esquema Global
            return $this->belongsToMany(\App\Models\SEGURIDAD_ERP\Rol::class, 'dbo.role_user', 'user_id', 'role_id')
                ->withPivot('id_obra', 'id_proyecto');
        }
    }

    public function rolesPersonalizados()
    {
        $obra = Obra::query()->find( Context::getIdObra() );

        if (isset( $obra->configuracion ) && $obra->configuracion->esquema_permisos == 2) {
            // Esquema Personalizado
            return $this->belongsToMany( Rol::class, Context::getDatabase() . '.Seguridad.role_user', 'user_id', 'role_id' );
        }
    }

    public function areasSubcontratantes()
    {
        return $this->belongsToMany( TipoAreaSubcontratante::class, 'dbo.usuarios_areas_subcontratantes', 'id_usuario', 'id_area_subcontratante' );

    }

    public function permisos()
    {
        $permisos = [];
        foreach ($this->roles as $rol) {
            // Validate against the Permission table
            foreach ($rol->permisos as $perm) {
                array_push($permisos, $perm->name);
            }
        }

        return $permisos;
    }

    public function permisosGenerales()
    {
        $permisos = [];
        foreach ($this->rolesGenerales as $rol) {
            // Validate against the Permission table
            foreach ($rol->permisos as $perm) {
                array_push($permisos, $perm->name);
            }
        }

        return $permisos;
    }

    public function rolesGenerales()
    {
        return $this->belongsToMany(\App\Models\SEGURIDAD_ERP\Rol::class, 'SEGURIDAD_ERP.dbo.role_user_global', 'user_id', 'role_id');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombre." ".$this->apaterno." ".$this->amaterno;
    }

    public static function getProyectoModuloSAO()
    {
        if (Context::getDatabase() && Context::getIdObra()) {
            $obra = Obra::where('id_obra','=',Context::getIdObra())->get();
            // $proyecto = \App\Models\MODULOSSAO\Proyectos\Proyecto::query()->where('Nombre', '=', "'".$obra[0]->nombre."'")->get();
            $proyecto = \App\Models\MODULOSSAO\Proyectos\Proyecto::query()->where('Nombre', '=', "PISTA 3 NAICM")->get();
            return $proyecto[0]->IDProyecto;
        }
    }

    public function google2faSecret()
    {
        return $this->hasOne(Google2faSecret::class, 'id_user', 'idusuario');
    }
}
