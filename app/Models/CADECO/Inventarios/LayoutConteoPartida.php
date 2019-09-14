<?php


namespace App\Models\CADECO\Inventarios;


use Illuminate\Database\Eloquent\Model;

class LayoutConteoPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.layouts_conteos_partidas';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'folio_marbete',
        'id_marbete',
        'tipo_conteo',
        'cantidad_usados',
        'cantidad_nuevo',
        'cantidad_inservible',
        'total',
        'iniciales',
        'id_layout_conteo',
        'observaciones'
    ];

    public function marbete()
    {
        return $this->belongsTo(Marbete::class,'id_marbete','id');
    }
}