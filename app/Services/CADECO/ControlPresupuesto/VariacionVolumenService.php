<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:45 PM
 */

namespace App\Services\CADECO\ControlPresupuesto;

use App\Facades\Context;
use App\Models\CADECO\Concepto;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\PDF\ControlPresupuesto\VariacionVolumenFormato;
use App\Models\CADECO\ControlPresupuesto\VariacionVolumen;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioPartidaHistorico;
use App\Repositories\CADECO\ControlPresupuesto\VariacionVolumenRepository;

class VariacionVolumenService{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * VariacionVolumen constructor.
     *
     * @param VariacionVolumen $model
     */
    public function __construct(VariacionVolumen $model)
    {
        $this->repository = new VariacionVolumenRepository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }


    public function delete($data, $id)
    {
        return $this->repository->show($id)->rechazar($data['data'][0]);
    }

    public function store(array $data)
    {

        $solicitud_variacion_volumen = $this->repository->create($data);

        return $solicitud_variacion_volumen;
    }

    public function autorizar($id){
        return $this->repository->show($id)->autorizar();
    }

    public function pdfVariacionVolumen($id)
    {
        $pdf = new VariacionVolumenFormato($id);
        return $pdf;
    }
}
