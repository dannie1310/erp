<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\Material;

class MaterialObserver
{
    /**
     * @param Material $material
     * @throws \Exception
     */
    public function creating(Material $material)
    {
        $material->validarUnidad();
        $material->validarExistente();
        $material->nivel = $material->nivelConsecutivo();
        $material->unidad_compra = $material->unidad;
        $material->codigo_familia_ins = $material->numero_parte;
        $material->FechaHoraRegistro = date('Y-m-d h:i:s');
        $material->UsuarioRegistro = auth()->user()->usuario;
    }

    public function deleting(Material $material)
    {
        $material->validarModificar('eliminar');
    }

    public function updating(Material $material)
    {
        if($material->getOriginal("nivel") == $material->nivel)
        {
            $material->validarModificar('editar');
        }else{
            if(!auth()->user()->can('editar_familia_material')) {
                throw new \Exception('No es posible editar el sistema porque no cuenta con el permiso, favor de solicitar la asignación al administrador del sistema.', 403);
            }
        }
    }
}
