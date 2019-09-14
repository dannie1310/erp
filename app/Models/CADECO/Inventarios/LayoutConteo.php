<?php


namespace App\Models\CADECO\Inventarios;


use Illuminate\Database\Eloquent\Model;

class LayoutConteo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.layouts_conteos';
    protected $primaryKey = 'id';

    public $timestamps = false;

    public function partidas(){
        return $this->hasMany(LayoutConteoPartida::class,'id_layout_conteo','id');
    }

    public function validar(){
        if(!InventarioFisico::query()->where('estado','=',0)->first()){
            abort(400,'No existe inventario fisico abierto');
        }
        return true;
    }

    public function partida(){
        abort(400,'rifaste');
    }

}