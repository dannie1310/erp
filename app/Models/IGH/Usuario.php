<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/11/18
 * Time: 05:24 PM
 */

namespace App\Models\IGH;

use App\Traits\IghAuthenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
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
}