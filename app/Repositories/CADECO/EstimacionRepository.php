<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 17/02/2020
 * Time: 03:52 PM
 */

namespace App\Repositories\CADECO;

use App\Models\CADECO\Estimacion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class EstimacionRepository extends Repository implements RepositoryInterface
{
    public function __construct(Estimacion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }

    public function update(array $data, $id)
    {
        return $this->show($id)->editar($data);
    }

    public function estimacionesProveedor()
    {
        return $this->model->estimacionesProveedor();
    }

    public function createProveedor(array $datos)
    {
        return $this->model->registrarProveedor($datos);
    }

    public function subcontratoAEstimar($id, $base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        return $this->model->where('id_transaccion', $id)->withoutGlobalScopes()->first()->subcontratoAEstimarProveedor($id, $base);
    }

    public function updateProveedor($data, $id)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $data['base']);
        return $this->model->where('id_transaccion', $id)->withoutGlobalScopes()->first()->editarProveedor($data);
    }

    public function eliminar($id, $data)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $data['base']);
        return $this->model->where('id_transaccion', $id)->withoutGlobalScopes()->first()->eliminarProveedor($data['base'],$data['motivo']);
    }
}
