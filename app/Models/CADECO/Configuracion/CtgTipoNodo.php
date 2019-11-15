<?php


namespace App\Models\CADECO\Configuracion;


use Illuminate\Database\Eloquent\Model;

class CtgTipoNodo extends Model
{
    public $timestamps = false;
    protected $connection = 'cadeco';
    protected $table = 'Configuracion.ctg_tipos_nodos';


    public function nodoTipo(){
        return $this->belongsTo(NodoTipo::class, 'id', 'id_tipo_nodo');
    }
}
