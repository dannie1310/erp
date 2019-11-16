<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Configuracion\CtgTipoNodo;
use App\Models\CADECO\Configuracion\NodoTipo;

class NodoProyecto extends Concepto
{
    protected static function boot()
    {
        parent::boot();

        // Global Scope para proyecto
        self::addGlobalScope(function ($query) {
            return $query->whereRaw('LEN(nivel) = 4');
        });
    }

    public function nodoTipo(){
        return $this->hasMany(NodoTipo::class, 'id_concepto_proyecto', 'id_concepto');
    }

    public function getPendientesAttribute(){
        return CtgTipoNodo::whereNotIn('id', $this->nodoTipo->pluck('id_tipo_nodo'))->get();
    }

    public function getAsignadosAttribute(){
        return $this->nodoTipo()->with(['tipoNodo', 'concepto'])->get();
    }
}
