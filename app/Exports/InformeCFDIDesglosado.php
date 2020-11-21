<?php


namespace App\Exports;


use App\Informes\EFOSEmpresaInformeCFDIDesglosado;
use Illuminate\Contracts\View\View;
use  Maatwebsite\Excel\Concerns\FromView;

class InformeCFDIDesglosado implements FromView
{

    /**
     * @inheritDoc
     */
    public function view(): View
    {
        return view('exports.informe_cfdi_desglosado', [
            'informe' => EFOSEmpresaInformeCFDIDesglosado::getInforme()
        ]);
    }
}
