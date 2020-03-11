<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion as Model;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionRepository as Repository;
use Illuminate\Support\Facades\Storage;
use Chumper\Zipper\Zipper;
use DateTime;

class SolicitudEdicionService
{
    /**
     * @var Repository
     */
    protected $repository;
    protected $arreglo_solicitud;
    protected $log;
    protected $carga;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);

    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    private function store()
    {
        $solicitud = $this->repository->registrar($this->arreglo_solicitud);
        return $solicitud;
    }

    private function procesaSolicitudXLS($nombre_archivo, $archivo_xls)
    {

    }
}