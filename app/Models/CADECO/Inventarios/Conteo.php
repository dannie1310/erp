<?php

namespace App\Models\CADECO\Inventarios;


use Illuminate\Database\Eloquent\Model;

class Conteo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.conteos';
    protected $primaryKey = 'id';

    public $timestamps = false;
    protected $fillable = [
        'id_marbete',
        'tipo_conteo',
        'id_layout_conteos_partida',
        'cantidad_usados',
        'cantidad_nuevo',
        'cantidad_inservible',
        'total',
        'iniciales',
        'observaciones'
    ];

    public function marbete()
    {
        return $this->belongsTo(Marbete::class,'id_marbete','id');
    }

}
