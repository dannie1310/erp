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
        dd("aqi");
       /*
        $origen->FechaAlta = date('Y-m-d');
        $origen->HoraAlta = date('H:i:s');
        $origen->usuario_registro = auth()->id();
        $origen->Clave = 'B';
        $origen->IdProyecto = 1;
        $origen->Estatus = 1;
       */
    }
}
