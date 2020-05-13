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

    public function updating(SolicitudComplemento $solicitudComplemento)
    {
        if($solicitudComplemento->id_area_compradora != $solicitudComplemento->getOriginal('id_area_compradora') || $solicitudComplemento->id_tipo !=  $solicitudComplemento->getOriginal('id_tipo') || $solicitudComplemento->id_area_solicitante != (int) $solicitudComplemento->getOriginal('id_area_solicitante'))
        {
            $solicitudComplemento->folio_compuesto = $solicitudComplemento->generaFolioCompuesto();
        }
    }

    public function updated(SolicitudComplemento $solicitudComplemento)
    {
        if ($solicitudComplemento->id_tipo == 3 && is_null($solicitudComplemento->activoFijo))
        {
            $solicitudComplemento->generarActivoFijo();
        }
        elseif($solicitudComplemento->id_tipo != 3 && !is_null($solicitudComplemento->activoFijo))
        {
            $solicitudComplemento->activoFijo->delete();
        }
    }
}
