<?php

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCarga;
use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoMonedaNacional;
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
        $monedas_nacionales = LayoutPasivoMonedaNacional::all()->pluck("descripcion")->toArray();
        if($layout_pasivos)
        {
            foreach ($layout_pasivos->partidas as $partida) {
                $posibles = $partida->posibles_cfdi;
                if(count($posibles)>0)
                {
                    $mejor_coincidencia = $posibles[count($posibles)-1];
                    $partida->uuid = $mejor_coincidencia->uuid;
                    $partida->coincide_rfc_empresa = $mejor_coincidencia->coincide_rfc_empresa;
                    $partida->coincide_rfc_proveedor = $mejor_coincidencia->coincide_rfc_proveedor;
                    $partida->coincide_folio = $mejor_coincidencia->coincide_folio;
                    $partida->coincide_fecha = $mejor_coincidencia->coincide_fecha;
                    $partida->coincide_importe = $mejor_coincidencia->coincide_importe;
                    $partida->coincide_moneda = $mejor_coincidencia->coincide_moneda;

                    if(in_array($mejor_coincidencia->moneda, $monedas_nacionales)){
                        if(in_array($partida->moneda_factura, $monedas_nacionales))
                        {
                            $partida->coincide_moneda = 1;
                            $partida->tc_factura = 1;
                            $partida->tc_saldo = 1;
                            $partida->moneda_factura = $mejor_coincidencia->moneda;
                        }
                    }else{
                        $partida->es_moneda_nacional = 0;
                    }

                    $partida->save();
                }
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
