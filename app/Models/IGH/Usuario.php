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
use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora;
use App\Models\SEGURIDAD_ERP\Compras\CtgAreaSolicitante;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion;
use App\Models\SEGURIDAD_ERP\ControlInterno\UsuarioNotificacion;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Models\SEGURIDAD_ERP\Permiso;
use App\Models\SEGURIDAD_ERP\TipoAreaCompradora;
use App\Models\SEGURIDAD_ERP\TipoAreaSolicitante;
use App\Models\SEGURIDAD_ERP\UsuarioAreaSubcontratante;
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
use Illuminate\Notifications\Notification;

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

    public function usuarioNotificacionCI()
    {
        return $this->hasOne(UsuarioNotificacion::class, 'id_usuario', 'idusuario');
    }

    public function suscripciones(){
        return $this->hasMany(Suscripcion::class, "id_usuario", "idusuario");
    }

    public function solicitudesEdicion(){
        return $this->hasMany(SolicitudEdicion::class, "id_usuario_registro", "idusuario");
    }

    public function scopeSolicitudEdicion($query, $solicitudes_edicion){
        $arreglo_usuarios = [];
        foreach($solicitudes_edicion as $solicitud)
        {
            $arreglo_usuarios[] = $solicitud->id_usuario_registro;
        }
        $arreglo_usuarios = array_unique($arreglo_usuarios);
        return $query->whereIn("idusuario",$arreglo_usuarios);
    }

    public function scopeEmpresaPadron($query, $empresas_padron){
        $arreglo_usuarios = [];
        foreach($empresas_padron as $empresa)
        {
            $arreglo_usuarios[] = $empresa->usuario_registro;
        }
        $arreglo_usuarios = array_unique($arreglo_usuarios);
        return $query->whereIn("idusuario",$arreglo_usuarios);
    }

    public function scopeSuscripcion($query, $suscripciones, $id_usuario=null){
        $arreglo_usuarios = [[$id_usuario]];
        foreach($suscripciones as $suscripcion)
        {
            $arreglo_usuarios[] = $suscripcion->id_usuario;
        }
        return $query->whereIn("idusuario",$arreglo_usuarios);
    }

    public function scopeNotificacionCI($query){
        $usuarios = UsuarioNotificacion::all();
        $usuarios->transform(function($item, $key){
            return $item->id_usuario;
        });
        return $query->whereIn("idusuario",$usuarios->all());
    }

    /**
     * Check if user has a permission by its name.
     *
     * @param string/array $permission Permission string or array of permissions.
     * @param bool $requireAll All permissions in the array are required.
     *
     * @return bool
     */
    public function can($permiso, $requireAll = false, $global = false)
    {
        if (is_array($permiso)) {
            foreach ($permiso as $permName) {
                $hasPerm = $this->can($permName, $requireAll, $global);
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
            if($global){
                foreach ($this->rolesSinContexto as $rol) {
                    // Validate against the Permission table
                    foreach ($rol->permisos as $perm) {
                        if (str_is($permiso, $perm->name)) {
                            return true;
                        }
                    }
                }
            }
            else {
                foreach ($this->roles as $rol) {
                    // Validate against the Permission table
                    foreach ($rol->permisos as $perm) {
                        if (str_is($permiso, $perm->name)) {
                            return true;
                        }
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

    public function rolesSinContexto()
    {
        //return $this->belongsToMany(\App\Models\SEGURIDAD_ERP\Rol::class, 'SEGURIDAD_ERP.dbo.role_user_global', 'user_id', 'role_id');
        return $this->belongsToMany(\App\Models\SEGURIDAD_ERP\Rol::class, 'SEGURIDAD_ERP.dbo.role_user', 'user_id', 'role_id');

    }

    public function rolesSinContextoAsignado()
    {
        return $this->belongsToMany(\App\Models\SEGURIDAD_ERP\Rol::class, 'SEGURIDAD_ERP.dbo.role_user_global', 'user_id', 'role_id');
        //return $this->belongsToMany(\App\Models\SEGURIDAD_ERP\Rol::class, 'SEGURIDAD_ERP.dbo.role_user', 'user_id', 'role_id');

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

    public function areasCompradoras()
    {
        return $this->belongsToMany( CtgAreaCompradora::class, 'Compras.areas_compradoras_usuario', 'id_usuario', 'id_area_compradora' );
    }

    public function areasSolicitantes()
    {
        return $this->belongsToMany( CtgAreaSolicitante::class, 'Compras.areas_solicitantes_usuario', 'id_usuario', 'id_area_solicitante' );
    }

    public function aplicaciones()
    {
        return $this->hasManyThrough(Sistema::class, SistemaPermiso::class,"permission_id","id", "id", "sistema_id");
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

    public function permisosAplicaciones()
    {
        $permisos = [];
        foreach ($this->rolesSinContexto as $rol) {
            foreach ($rol->permisos()->aplicacion()->get() as $perm) {
                array_push($permisos, $perm->name);
            }
        }

        foreach ($this->rolesSinContextoAsignado as $rol) {
            foreach ($rol->permisos()->aplicacion()->get() as $perm) {
                array_push($permisos, $perm->name);
            }
        }
        return array_unique($permisos);
    }

    public function reportesGenerales()
    {
        $permisos_reportes = [];
        foreach ($this->rolesGenerales as $rol) {
            foreach ($rol->permisos()->reporte()->get() as $perm) {
                array_push($permisos_reportes, $perm->name);
            }
        }
        return $permisos_reportes;
    }

    public function findPermisoGeneral($permiso)
    {
        foreach ($this->rolesGenerales as $rol) {
            // Validate against the Permission table
            foreach ($rol->permisos as $perm) {
                if($perm->name == $permiso){
                    return true;
                }
            }
        }
        return false;
    }

    public function rolesGenerales()
    {
        return $this->belongsToMany(\App\Models\SEGURIDAD_ERP\Rol::class, 'SEGURIDAD_ERP.dbo.role_user_global', 'user_id', 'role_id');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombre." ".$this->apaterno." ".$this->amaterno;
    }

    public function google2faSecret()
    {
        return $this->hasOne(Google2faSecret::class, 'id_user', 'idusuario');
    }

    public function routeNotificationForMail($notification)
    {
        return $this->correo;
    }
}
