<?php

namespace App\Repositories\CADECO\PresupuestoContratista;

use App\Models\CADECO\PresupuestoContratista;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Requisicion $model
     */
    public function __construct(PresupuestoContratista $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->crear($data);
    }

    public function registrar(array $data, $invitacion)
    {
        return $this->model->registrarPortalProveedor($data, $invitacion);
    }

    public function editarPortalProveedor($id, $data, $invitacion)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $invitacion->base_datos);
        return $this->model->where('id_transaccion', $id)->withoutGlobalScopes()->first()->editarPortalProveedor($data, $invitacion);
    }

    public function descargaLayoutProveedor($id,$invitacion)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $invitacion->base_datos);
        return $this->model->where('id_transaccion', $id)->withoutGlobalScopes()->first()->descargaLayout();
    }

    public function findProveedor($id, $base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        return $this->model->where('id_transaccion', $id)->withoutGlobalScopes()->first();
    }

    public function eliminar($id, $base, $motivo)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        return $this->model->where('id_transaccion', $id)->withoutGlobalScopes()->first()->eliminarProveedor($motivo,$base);
    }

    public function liberaCotizacion($id_presupuesto, $base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        $presupuesto = $this->model
            ->where('id_transaccion', $id_presupuesto)
            ->where("tipo_transaccion","=",50)
            ->where("estado","=",1)
            ->where("opciones","=",10)
            ->withoutGlobalScopes()
            ->first();
        if($presupuesto){
            $presupuesto->opciones = 0;
            $presupuesto->save();
        }
        return $presupuesto;
    }
}
