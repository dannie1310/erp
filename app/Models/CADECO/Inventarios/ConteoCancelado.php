<?php


namespace App\Models\CADECO\Inventarios;


use Illuminate\Database\Eloquent\Model;

class ConteoCancelado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.conteos_cancelados';
    protected $primaryKey = 'id';

    public $timestamps = false;
    protected $fillable = [
        'id_conteo',
        'id_marbete',
        'tipo_conteo',
        'id_layout_conteos_partida',
        'cantidad_usados',
        'cantidad_nuevo',
        'cantidad_inservible',
        'total',
        'iniciales',
        'id_usuario',
        'fecha_hora_registro',
        'observaciones',
        'motivo_cancelacion',
    ];

}