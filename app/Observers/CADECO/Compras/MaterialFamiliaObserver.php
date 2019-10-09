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
        $materialFamilia->marca = 0;
        $materialFamilia->equivalencia = 0;
        $materialFamilia->unidad_compra = $materialFamilia->unidad;
        $materialFamilia->codigo_familia_ins = $materialFamilia->numero_parte;
        $materialFamilia->FechaHoraRegistro = date('Y-m-d h:i:s');
        $materialFamilia->UsuarioRegistro = auth()->user()->usuario;
    }
}
