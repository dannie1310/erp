<?php


namespace App\Observers\CADECO\Inventarios;


use App\Models\CADECO\Inventarios\ConteoCancelado;

class ConteoCanceladoObserver
{
    public function creating(ConteoCancelado $conteoCancelado){
        $conteoCancelado->id_usuario_cancelo = auth()->id();
    }
}