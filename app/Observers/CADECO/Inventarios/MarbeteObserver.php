<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/11/2019
 * Time: 12:11 PM
 */

namespace App\Observers\CADECO\Inventarios;


use App\Models\CADECO\Inventarios\Marbete;
use App\Models\CADECO\Inventarios\MarbeteLog;

class MarbeteObserver
{

    /**
     * @param Marbete $marbete
     */
    public function creating(Marbete $marbete)
    {
        $marbete->precio_unitario = $marbete->precioUnitarioPromedio();
    }

    public function created(Marbete $marbete)
    {
        MarbeteLog::create([
            'id_marbete' => $marbete->id,
            'description' => 'Marbete Creado'
        ]);
    }

    public function updated(Marbete $marbete)
    {
        MarbeteLog::create([
            'id_marbete' => $marbete->id,
            'description' => "Marbete Editado"
        ]);
    }

    public function deleted(Marbete $marbete)
    {
        MarbeteLog::create([
            'id_marbete' => $marbete->id,
            'description' => "Marbete Eliminando"
        ]);
    }
}