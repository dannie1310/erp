<?php

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCarga;
use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartida;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class LayoutPasivoCargaRepository extends Repository implements RepositoryInterface
{
    public function __construct(LayoutPasivoCarga $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function asociarCFDI($id)
    {
        $layout_pasivos = $this->show($id);
        if($layout_pasivos)
        {
            foreach ($layout_pasivos->partidas as $partida) {
                $partida->asociaCFDI();
            }
        }
        return $layout_pasivos;
    }

    public function listarPosiblesCFDI($id_pasivo)
    {
        $pasivo = LayoutPasivoPartida::find($id_pasivo);
        return $pasivo->posibles_cfdi;
    }
}
