<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 03/10/2020
 * Time: 05:03 PM
 */

namespace App\Observers\CADECO\Subcontratos;


use App\Facades\Context;
use App\Models\CADECO\Subcontratos\ClasificacionSubcontrato;

class ClasificacionSubcontratoObserver
{
    /**
     * @param ClasificacionSubcontrato $clasificacionSubcontrato
     * @throws \Exception
     */
    public function creating(ClasificacionSubcontrato $clasificacionSubcontrato)
    {
        $clasificacionSubcontrato->id_obra = Context::getIdObra();
        $clasificacionSubcontrato->generarFolio();
    }
}