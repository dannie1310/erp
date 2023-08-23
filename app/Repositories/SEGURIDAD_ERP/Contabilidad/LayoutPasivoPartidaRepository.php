<?php

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCasoSinCFDI;
use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartida;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class LayoutPasivoPartidaRepository extends Repository implements RepositoryInterface
{
    public function __construct(LayoutPasivoPartida $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function asociarCFDI($id)
    {
        return $this->show($id)->asociaCFDI();
    }

    public function listarPosiblesCFDI($id_pasivo)
    {
        $pasivo = LayoutPasivoPartida::find($id_pasivo);
        return $pasivo->posibles_cfdi;
    }

    public function getCasosSinCFDI()
    {
        return LayoutPasivoCasoSinCFDI::orderBy("descripcion")->get();
    }
}
