<?php


namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Almacen;
use App\Models\CADECO\Almacenes\AlmacenEliminado;

class AlmacenObserver
{
    public function creating(Almacen $almacen)
    {
        $almacen->id_obra = Context::getIdObra();
        $almacen->opciones = 0;
        $almacen->fecha_registro = date('Y-m-d H:i:s');
        $almacen->id_usuario = auth()->id();
    }

    public function deleting(Almacen $almacen)
    {
        AlmacenEliminado::create(
            [
                'id_almacen' => $almacen->id_almacen,
                'id_obra' => $almacen->id_obra,
                'descripcion' => $almacen->descripcion,
                'tipo_almacen' => $almacen->tipo_almacen,
                'id_material' => $almacen->id_material,
                'opciones' => $almacen->opciones,
                'cuenta_contable' => $almacen->cuenta_contable,
                'direccion' => $almacen->direccion,
                'numero_economico' => $almacen->numero_economico,
                'clasificacion' => $almacen->clasificacion,
                'propiedad' => $almacen->propiedad,
                'fecha_registro' => $almacen->fecha_registro,
                'id_usuario' => $almacen->id_usuario,
                'virtual' => $almacen->virtual,
                'motivo' => '',
                'usuario_elimina' => auth()->id(),
                'fecha_eliminacion' => date('Y-m-d H:i:s')
            ]
        );
    }

    public function deleted(Almacen $almacen)
    {
        if (is_null($almacen->almacenEliminado))
        {
            abort(500, "Error al respaldar el almacén a eliminar");
        }
    }
}
