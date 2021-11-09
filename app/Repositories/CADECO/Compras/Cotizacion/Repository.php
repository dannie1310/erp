<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/11/2019
 * Time: 05:43 PM
 */

namespace App\Repositories\CADECO\Compras\Cotizacion;



use App\Models\CADECO\CotizacionCompra;
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
    public function __construct(CotizacionCompra $model)
    {
        $this->model = $model;
    }

    public function descargaLayout($id)
    {
        return $this->model->where('id_transaccion', $id)->first()->descargaLayout();
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

    public function liberaCotizacion($id_cotizacion, $base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        $cotizacion = $this->model->where('id_transaccion', $id_cotizacion)
            ->where("tipo_transaccion","=",18)
            ->where("estado","=",1)->where("opciones","=",10)
            ->withoutGlobalScopes()
            ->first();
        if($cotizacion){
            $cotizacion->opciones = 1;
            $cotizacion->save();
        }
        return $cotizacion;
    }
}
