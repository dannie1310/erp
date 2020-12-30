<?php


namespace App\Services\CADECO\Contratos;

use App\Models\CADECO\SubcontratosCM\Solicitud as Model;
use App\Repositories\CADECO\SubcontratosCM\SolicitudRepository as Repository;

class SolicitudCambioService
{
    protected $repository;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function registrar($request)
    {
        $conceptos = $request->conceptos;
        $conceptos_extraordinarios = $request->conceptos_extraordinarios;
        $conceptos_nuevo_precio = $request->conceptos_cambios_precio;
        $fecha = $request->fecha;
        $id_subcontrato = $request->id_subcontrato;
        $observaciones = $request->observaciones;
        $archivo = $request->archivo;
        $archivo_nombre = $request->archivo_nombre;
        dd($archivo_nombre, $archivo);
    }

}
