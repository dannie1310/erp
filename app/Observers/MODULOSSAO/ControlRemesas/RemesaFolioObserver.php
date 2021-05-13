<?php


namespace App\Observers\MODULOSSAO\ControlRemesas;


use App\Models\MODULOSSAO\ControlRemesas\RemesaFolio;

class RemesaFolioObserver
{
    /**
     * @param RemesaFolio $folio
     */
    public function updating(RemesaFolio $folio)
    {
        $folio->validarEdicion();
    }
}
