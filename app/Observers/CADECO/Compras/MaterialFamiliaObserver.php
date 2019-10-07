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
        $materialFamilia->nivel = $materialFamilia->nivelConsecutivo($materialFamilia->nivel);
        $materialFamilia->tipo_material = 1;
        dd($materialFamilia->tipo_material,$materialFamilia->nivel);
        $materialFamilia->FechaHoraRegistro = date('Y-m-d h:i:s');
        $materialFamilia->UsuarioRegistro = auth()->user()->usuario;
//        dd('ya debe insertar');

    }
}
