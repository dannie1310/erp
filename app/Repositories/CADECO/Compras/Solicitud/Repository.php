<?php


namespace App\Repositories\CADECO\Compras\Solicitud;


use App\Models\CADECO\SolicitudCompra;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    public function __construct(SolicitudCompra $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }

    public function update(array $datos, $id)
    {
        return $this->show($id)->editar($datos);
    }

    public function delete(array $data, $id)
    {
        return $this->show($id)->eliminar($data);
    }

    public function aprobar($data, $id)
    {
        return $this->show($id)->aprobarSolicitud($data);
    }
}
