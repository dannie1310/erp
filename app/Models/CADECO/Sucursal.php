<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.sucursales';
    protected $primaryKey = 'id_sucursal';

    public $timestamps = false;

    protected $fillable = [
        'id_empresa',
        'descripcion',
        'direccion',
        'ciudad',
        'codigo_postal',
        'estado',
        'telefono',
        'fax',
        'contacto',
        'casa_central',

    ];

    protected static function boot(){
        parent::boot();

        self::creating(function ($model){
            $model->UsuarioRegistro = auth()->id();
            $model->descripcion = mb_strtoupper($model->descripcion);
            $model->direccion = mb_strtoupper($model->direccion);
            $model->ciudad = mb_strtoupper($model->ciudad);
            $model->estado = mb_strtoupper($model->estado);
            $model->contacto = mb_strtoupper($model->contacto);

        });
    }



}
