<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:40 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use Carbon\Carbon;
use App\Models\CADECO\Fondo;

class FondoObserver
{
    /**
     * @param Fondo $fondo
     * @throws \Exception
     */
    public function creating(Fondo $fondo)
    {
        if(Fondo::query()->where([['id_tipo',$fondo->id_tipo],['id_responsable',$fondo->id_responsable]])->get()->toArray() == []){
            $fondo -> id_obra = Context::getIdObra();
            $fondo->fecha = Carbon::now()->format('Y-m-d');
        }else{
            throw New \Exception('El responsable ya tiene un fondo del tipo seleccionado');
        }
    }
}