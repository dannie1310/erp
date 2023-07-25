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
        if(substr($material->getOriginal("nivel"), 0, 3) != substr($material->nivel,0,3) && ($material->getOriginal("numero_parte") == $material->numero_parte && $material->getOriginal("unidad") == $material->unidad && $material->getOriginal("unidad_compra") == $material->unidad_compra && str_replace(' ', '', $material->getOriginal("descripcion")) == str_replace(' ', '',$material->descripcion)))
        {
            if(!auth()->user()->can('editar_familia_material') && (!auth()->user()->can('editar_insumo_servicio') || !auth()->user()->can('editar_insumo_material') || !auth()->user()->can('editar_insumo_herramienta_equipo') || !auth()->user()->can('editar_insumo_maquinaria') || !auth()->user()->can('editar_insumo_mano_obra'))) {
                throw new \Exception('No es posible editar el sistema porque no cuenta con el permiso, favor de solicitar la asignación al administrador del sistema.', 403);
            }
        }else{
            $material->validarModificar('editar');
        }
    }
}
