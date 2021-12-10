<?php


namespace App\Repositories\CADECO;


use App\Models\CADECO\SolicitudAutorizacionAvance;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SolicitudAutorizacionAvanceRepository extends Repository implements RepositoryInterface
{
    public function __construct(SolicitudAutorizacionAvance $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function solicitudes()
    {
        return $this->model->solicitudes();
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }

    public function subcontratoAEstimar($id, $base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        return $this->model->withoutGlobalScopes()->where('id_transaccion', $id)->first()->subcontratoAEstimar($base);
    }

    public function update(array $data, $id)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $data['base']);
        return $this->model->withoutGlobalScopes()->where('id_transaccion', $id)->first()->editar($data);
    }

    public function eliminar($id, $data)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $data['base']);
        return $this->model->withoutGlobalScopes()->where('id_transaccion', $id)->first()->eliminar($data['base'],$data['motivo']);
    }
}
