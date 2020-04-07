<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 06:35 p. m.
 */


namespace App\Observers\CADECO\Compras;


use App\Models\CADECO\Compras\SolicitudComplemento;

class SolicitudComplementoObserver
{

    /**
     * @param SolicitudComplemento $solicitudComplemento
     */

    public function creating(SolicitudComplemento $solicitudComplemento)
    {
        $solicitudComplemento->folio_compuesto = $solicitudComplemento->generaFolioCompuesto();
        $solicitudComplemento->estado = 1;
        $solicitudComplemento->registro = auth()->id();
        if($solicitudComplemento->id_tipo === 3)
        {
            $solicitudComplemento->generarActivoFijo();
        }
    }
}
