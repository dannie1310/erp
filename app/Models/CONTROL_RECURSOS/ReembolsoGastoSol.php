<?php

namespace App\Models\CONTROL_RECURSOS;


class ReembolsoGastoSol extends Documento
{
    protected $fillable = [
        'IdEmpresa',
        'IdProveedor',
        'Concepto',
        'IdMoneda',
        'Fecha',
        'FolioDocto',
        'Importe',
        'Retenciones',
        'IVA',
        'OtrosImpuestos',
        'Total',
        'Vencimiento',
        'TasaIVA',
        'IdTipoDocto',
        'Estatus',
        'Alias_Depto',
        'IdSerie',
        'IdGenero',
        'registro_portal'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('IdTipoDocto', '13');
            //->where('Estatus', 11);
        });
    }
}
