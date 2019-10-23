<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\Familia;

class FamiliaObserver
{
    /**
     * @param Familia $familia
     */
    public function creating(Familia $familia)
    {
        $familia->descripcion = strtoupper($familia->descripcion);
        $familia->validarFamiliaExistente($familia->tipo_material);
        $familia->nivel = $familia->nivelFamiliaConsecutivo($familia->tipo_material);
        if ($familia->tipo_material == 1 || $familia->tipo_material == 4) {
            $familia->marca = 0;
            $familia->equivalencia = 0;
        }
        if ($familia->tipo_material == 2) {
            $familia->marca = 0;
            $familia->equivalencia = 0;
        }
        $familia->FechaHoraRegistro = date('Y-m-d h:i:s');
        $familia->UsuarioRegistro = auth()->user()->usuario;
    }
}
