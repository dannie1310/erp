<?php


namespace App\Observers\CADECO\Inventarios;


use App\Models\CADECO\Inventarios\Conteo;

class ConteoObserver
{
    public function creating(Conteo $conteo){
        $conteo->id_usuario = auth()->id();
    }

}