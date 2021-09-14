<?php


namespace App\Models\IGH92;


use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    /**
     * @var string
     */
    protected $connection = 'igh92';

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
        'usuario', 'nombre', 'apaterno', 'amaterno', 'usuario_estado', 'correo', 'clave'
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
}
