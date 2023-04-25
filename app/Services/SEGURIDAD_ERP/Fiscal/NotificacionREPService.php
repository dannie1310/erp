<?php

namespace App\Services\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\RepNotificacion;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Storage;

class NotificacionREPService
{
    protected $repository;

    public function __construct(RepNotificacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function pdf($id)
    {
        $dir_descarga = public_path("downloads/fiscal/comunicados_notificaciones/".$id.".pdf");
        if(is_file($dir_descarga)){
            return response()->file($dir_descarga);
        }
       return abort(400,"No se encontro el archivo.");
    }
}
