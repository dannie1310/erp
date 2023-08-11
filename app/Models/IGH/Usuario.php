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
use App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\Firmante;
use App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\NivelAutorizacion;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Models\SEGURIDAD_ERP\PadronProveedores\AsignacionValor;
use App\Models\SEGURIDAD_ERP\Permiso;
use App\Models\SEGURIDAD_ERP\RoleUser;
use App\Models\SEGURIDAD_ERP\RoleUserGlobal;
use App\Models\SEGURIDAD_ERP\TipoAreaCompradora;
use App\Models\SEGURIDAD_ERP\TipoAreaSolicitante;
use App\Models\SEGURIDAD_ERP\UsuarioAreaSubcontratante;
use App\Models\SEGURIDAD_ERP\Google2faSecret;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Models\SEGURIDAD_ERP\RolGeneral;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use App\Traits\IghAuthenticatable;
use App\Utils\Util;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notification;
use App\Models\SEGURIDAD_ERP\Rol as RolSeguridadERP;

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
        'usuario', 'nombre', 'apaterno', 'amaterno', 'usuario_estado', 'correo', 'clave', 'id_empresa'
        , 'pide_cambio_contrasenia', 'pide_datos_empresa', 'tipo_empresa', 'aviso_privacidad_leido_aceptado',
        'fecha_hora_aceptacion_aviso_privacidad', 'id_empresa_invito'
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

    public function firmante()
    {
        return $this->hasOne(Firmante::class,"id_usuario", "idusuario");
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'iddepartamento', 'iddepartamento');
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'idubicacion','idubicacion');
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

    public function scopeUsuarioRol($query, $roles_txt, $id_proyecto_obra){
        $obra = \App\Models\SEGURIDAD_ERP\Obra::find($id_proyecto_obra);
        $roles = \App\Models\SEGURIDAD_ERP\Rol::whereIn("name", $roles_txt)->pluck("id")->toArray();
        $usuarios = RoleUser::whereIn("role_id",$roles)->where("id_obra","=", $obra->id_obra)->where("id_proyecto","=",$obra->id_proyecto)->pluck("user_id")->toArray();
        $usuarios = array_unique($usuarios);
        return $query->whereIn("idusuario",$usuarios);
    }

    public function scopeUsuarioPermisoGlobal($query, $permiso_txt){
        $permiso = \App\Models\SEGURIDAD_ERP\Permiso::whereIn("name", $permiso_txt)->pluck("id")->toArray();
        $roles = \App\Models\SEGURIDAD_ERP\PermisoRol::whereIn("permission_id",$permiso)->pluck("role_id")->toArray();
        $usuarios = RoleUserGlobal::whereIn("role_id",$roles)->pluck("user_id")->toArray();
        $usuarios = array_unique($usuarios);
        return $query->whereIn("idusuario",$usuarios)->where("usuario_estado","=",2);
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
                foreach ($this->rolesSinContextoAsignado as $rol) {
                    // Validate against the Permission table
                    foreach ($rol->permisos as $perm) {
                        if (str_is($permiso, $perm->name)) {
                            return true;
                        }
                    }
                }
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
        if(Context::getIdObra())
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
        }else{
            return $this->belongsToMany(\App\Models\SEGURIDAD_ERP\Rol::class, 'SEGURIDAD_ERP.dbo.role_user_global', 'user_id', 'role_id');
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

    public function rolesUsuarioGlobal()
    {
        return $this->hasMany(RoleUserGlobal::class, "user_id", "idusuario");
    }

    public function usuario92()
    {
        return $this->belongsTo(\App\Models\IGH92\Usuario::class, 'idusuario', 'idusuario');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombre." ".$this->apaterno." ".$this->amaterno;
    }

    public function getNombreCompletoSinEspaciosAttribute()
    {
        return $this->nombre.$this->apaterno.$this->amaterno;
    }

    public function google2faSecret()
    {
        return $this->hasOne(Google2faSecret::class, 'id_user', 'idusuario');
    }

    public function routeNotificationForMail($notification)
    {
        return $this->correo;
    }

    /**
     * Cambia la clave del usuario igh
     * @param $clave_nueva
     */
    public function cambiarClave($clave_nueva)
    {
        $this->update([
            'clave' => $clave_nueva
        ]);
    }

    public function asignaRol($rol)
    {
        $rolObj = RolSeguridadERP::where('name',"=",$rol)->first();
        if($rolObj){
            $preexistente = RoleUserGlobal::where("user_id","=",$this->idusuario)
                ->where("role_id","=",$rolObj->id)
                ->first();
            if(!$preexistente){
                $this->rolesUsuarioGlobal()->create([
                    'role_id'=>$rolObj->id
                ]);
            }
        }
    }

    public static function calculaNombre($razon_social)
    {
        $arregloNombre = explode(" ",Util::eliminaCaracteresEspeciales($razon_social));
        if(count($arregloNombre)>4){
            $arregloNombre = Usuario::generaArregloNombre($razon_social);
        }
        $nombre = '';
        foreach($arregloNombre as $i=>$elemento_nombre)
        {
            $nombre.=substr($arregloNombre[$i],0,1).Usuario::getPrimeraVocal($arregloNombre[$i]);
        }

        $nombre.=Usuario::getHomonimia($razon_social);
        $nombre.=Usuario::getDigitoVerificador($nombre);
        return $nombre;
    }

    public static function getPrimeraVocal($string)
    {
        $vocales = ["a","e","i","o","u","A","E","I","O","U"];
        for($i=0; $i<strlen($string); $i++)
        {
            $letra = substr($string,$i,1);
            if(in_array($letra,$vocales) && $i>0)
            {
                return $letra;
            }
        }
        return "X";
    }

    public static function getDigitoVerificador($string)
    {
        $arreglo = [];
        for($i=0; $i<strlen($string); $i++)
        {
            $caracter = substr($string,$i,1);
            $arreglo[] = sprintf("%02d", AsignacionValor::where("caracter","=",$caracter)->first()->valor_codigo_verificador);
        }
        $multiplicaciones = [];
        $i=0;
        foreach($arreglo as $item){
            $multiplicaciones[$i]["resultado"] = $item * (count($arreglo)-$i+1);
            $multiplicaciones[$i]["a"] = $item;
            $multiplicaciones[$i]["b"] = (count($arreglo)-$i+1);
            $i++;
        }

        $suma_multiplicaciones = 0;
        foreach($multiplicaciones as $multiplicacion){
            $suma_multiplicaciones += $multiplicacion["resultado"];
        }

        $residuo = $suma_multiplicaciones%11;
        if($residuo == 0){
            return 0;
        } else if($residuo == 10){
            return "A";
        } else if($residuo>0){
            return 11-$residuo;
        }
    }

    public static function getHomonimia($string)
    {
        $cadena = '0';

        for($i=0; $i<strlen($string); $i++)
        {
            $caracter = substr($string,$i,1);
            try{
                $cadena.= sprintf("%02d", AsignacionValor::where("caracter","=",$caracter)->first()->valor_homonimo);
            }catch (\Exception $e){
                $cadena.='';
            }

        }

        $multiplicaciones = [];

        $j = 0;
        for($i=0; $i<strlen($cadena); $i++)
        {
            $extracto =  substr($cadena,$i,2);
            $digito = sprintf("%02d",substr($extracto,1,1));
            try{
                $multiplicaciones[$j]["resultado"] = $extracto * $digito;
                $multiplicaciones[$j]["a"] = $extracto;
                $multiplicaciones[$j]["b"] = $digito;
            }catch (\Exception $e)
            {
                dd($cadena,$extracto, $digito, $multiplicaciones);
            }
            $j++;

        }
        $suma_multiplicaciones = 0;
        foreach($multiplicaciones as $multiplicacion){
            $suma_multiplicaciones += $multiplicacion["resultado"];
        }

        $dividendo = substr($suma_multiplicaciones,strlen($suma_multiplicaciones)-3,3);
        $cociente = intval($dividendo/34);
        $residuo = $dividendo % 34;

        $clave_homonimia = AsignacionValor::where("caracter","like",$cociente)->first()->valor_coeficiente_residuo;
        $clave_homonimia .= AsignacionValor::where("caracter","like",$residuo)->first()->valor_coeficiente_residuo;

        return $clave_homonimia;

    }

    public static function generaArregloNombre($razon_social)
    {
        $rs_ex = explode(" ",Util::eliminaCaracteresEspeciales($razon_social));
        $longitud = count($rs_ex);
        $cantidadPorCampo = floor($longitud/3);
        $nombreArr = [];
        $icc = 0;
        for($i = 0;$i<$longitud;$i++)
        {
            if(key_exists($icc,$nombreArr)){
                $nombreArr[$icc] .= " ".$rs_ex[$i];
            } else {
                $nombreArr[] = $rs_ex[$i];
            }

            if($i==$cantidadPorCampo)
            {
                $icc++;
                $cantidadPorCampo+=$cantidadPorCampo;
            }
        }
        return $nombreArr;
    }

    public function cambiarClave92($clave_nueva)
    {
        if($this->usuario92)
        {
            $this->usuario92->update([
                'clave' => $clave_nueva
            ]);
        }
    }

    public function cambiarClaveModuloSAO($clave_nueva)
    {
        DB::connection('modulosao')->update(DB::raw("
                        DECLARE	@return_value int
                        EXECUTE	@return_value = [dbo].[sp_updateclavesDayta]
                        @usuario = '$this->usuario',
                        @password = '$clave_nueva'
                        SELECT	'res' = @return_value"));
    }

    public function findForPassport($usuario)
    {
        return $this->where('usuario', $usuario)->first();
    }
}
