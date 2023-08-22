<?php


namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartida;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartidaRepository;
use DateTime;
use DateTimeZone;


class LayoutPasivoPartidaService{

    /**
     * @var LayoutPasivoPartidaRepository
     */
    protected $repository;

    /**
     * @param LayoutPasivoPartida $model
     */
    public function __construct(LayoutPasivoPartida $model)
    {
        $this->repository = new LayoutPasivoPartidaRepository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate()
    {
        return $this->repository->paginate();
    }

    public function update(array $data, $id)
    {
        $fecha = New DateTime($data['fecha']);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));

        $data["importe_factura"] = str_replace(",","",$data["importe_factura"]);
        $data["tc_factura"] = str_replace(",","",$data["tc_factura"]);
        $data["importe_mxn"] = str_replace(",","",$data["importe_mxn"]);
        $data["saldo"] = str_replace(",","",$data["saldo"]);
        $data["tc_saldo"] = str_replace(",","",$data["tc_saldo"]);
        $data["saldo_mxn"] = str_replace(",","",$data["saldo_mxn"]);
        $data["fecha_factura"] = $fecha->format("Y-m-d");


        $pasivo = $this->repository->update($data, $id);
        $pasivo->actualizaCoincidenciasConCFDI();
        $pasivo->actualizaInconsistenciaSaldo();
        return $pasivo;
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function asociarCFDI($id_pasivo)
    {
        return $this->repository->asociarCFDI($id_pasivo);
    }

    public function listarPosiblesCFDI($id_pasivo)
    {
        return $this->repository->listarPosiblesCFDI($id_pasivo);
    }
    public function delete($data, $id)
    {
        $this->repository->delete($data, $id);
    }

    public function getCasosSinCFDI()
    {
        return $this->repository->getCasosSinCFDI();
    }
}
