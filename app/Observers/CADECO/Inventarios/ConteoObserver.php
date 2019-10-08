<?php


namespace App\Observers\CADECO\Inventarios;


use App\Models\CADECO\Inventarios\Conteo;

class ConteoObserver
{
    public function creating(Conteo $conteo){
        $conteo->validar();
        $conteo->id_usuario = auth()->id();
        $conteo->fecha_hora_registro =  date('Y-m-d h:i:s');
    }
}