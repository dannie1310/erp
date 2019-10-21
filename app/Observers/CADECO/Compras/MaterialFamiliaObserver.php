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
        $materialFamilia->unidad_compra = $materialFamilia->unidad;
        $materialFamilia->codigo_familia_ins = $materialFamilia->numero_parte;
        $materialFamilia->FechaHoraRegistro = date('Y-m-d h:i:s');
        $materialFamilia->UsuarioRegistro = auth()->user()->usuario;
    }
}
