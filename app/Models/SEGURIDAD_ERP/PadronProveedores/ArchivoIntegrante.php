<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


class ArchivoIntegrante extends ArchivoGeneralizacion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereNotNull('id_archivo_consolidador');
        });
    }

    public function archivoConsolidador()
    {
        return $this->belongsTo(Archivo::class,"id_archivo_consolidador", "id");
    }

}
