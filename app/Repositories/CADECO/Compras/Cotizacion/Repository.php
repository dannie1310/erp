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
        return $this->model->descargaLayout($id);
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
}
