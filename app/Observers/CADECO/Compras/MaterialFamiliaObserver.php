<?php


namespace App\Observers\CADECO\Compras;


use App\Models\CADECO\MaterialFamilia;

class MaterialFamiliaObserver
{
    /**
     * @param MaterialFamilia $materialFamilia
     */
    public function creating(MaterialFamilia $materialFamilia)
    {

        $materialFamilia->validarExistente($materialFamilia->descripcion);
        $materialFamilia->nivel = $materialFamilia->nivelConsecutivo($materialFamilia->tipo);
        dd($materialFamilia->nivel,auth()->user()->usuario,date('Y-m-d h:i:s'));
        dd(auth()->user()->usuario);
//        if ($familia->tipo_material == 1 || $familia->tipo_material == 4) {
//            $familia->marca = 0;
//            $familia->equivalencia = 0;
//        }
//        $familia->FechaHoraRegistro = date('Y-m-d h:i:s');
//        $familia->UsuarioRegistro = auth()->user()->usuario;
    }
}
