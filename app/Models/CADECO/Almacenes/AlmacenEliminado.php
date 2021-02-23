<?php


namespace App\Models\CADECO\Almacenes;


use Illuminate\Database\Eloquent\Model;

class AlmacenEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Almacenes.almacenes_eliminados';
    protected $primaryKey = 'id_almacen';
    public $timestamps = false;
    public $fillable = [
        'id_almacen',
        'id_obra',
        'descripcion',
        'tipo_almacen',
        'id_material',
        'opciones',
        'cuenta_contable',
        'virtual',
        'direccion',
        'numero_economico',
        'clasificacion',
        'propiedad',
        'fecha_registro',
        'id_usuario',
        'motivo',
        'usuario_elimina',
        'fecha_eliminacion'
    ];
}
