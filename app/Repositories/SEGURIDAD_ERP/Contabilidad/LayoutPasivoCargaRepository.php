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
        $layout_pasivos = LayoutPasivoCarga::find($id);
        if($layout_pasivos)
        {
            foreach ($layout_pasivos->partidas as $partida) {
                $posibles = $partida->posibles_cfdi;
                $partida->uuid = $posibles[0]->uuid;
                $partida->coincide_rfc_empresa = $posibles[0]->coincide_rfc_empresa;
                $partida->coincide_rfc_proveedor = $posibles[0]->coincide_rfc_proveedor;
                $partida->coincide_folio = $posibles[0]->coincide_folio;
                $partida->coincide_fecha = $posibles[0]->coincide_fecha;
                $partida->coincide_importe = $posibles[0]->coincide_importe;
                $partida->coincide_moneda = $posibles[0]->coincide_moneda;
                $partida->save();
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
