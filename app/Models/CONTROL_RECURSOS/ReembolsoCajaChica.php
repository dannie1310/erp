<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Support\Facades\DB;

class ReembolsoCajaChica extends Documento
{
    public function registrar($datos)
    {
        dd("AUUI MAYBE", $datos);
        $fecha_inicial = new DateTime($data['fecha_inicio_editar']);
        $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_inicial'] = $fecha_inicial->format("Y-m-d");
        $fecha_final = new DateTime($data['fecha_final_editar']);
        $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_final'] = $fecha_final->format("Y-m-d");
        try {
            DB::connection('controlrec')->beginTransaction();

            $reembolso = ReembolsoGastoSol::create([
                'IdEmpresa' => $this->idempresa,
                'IdProveedor' => $this->idempleado,
                'Concepto' => $data['motivo'],
                'IdMoneda' => $this->idmoneda,
                'Fecha' => $data['fecha_inicial'],
                'FolioDocto' => $this->folio,
                'Importe' => $data['suma_importe'],
                'Retenciones' => $data['suma_retenciones'],
                'IVA' => $data['suma_iva'],
                'OtrosImpuestos' =>$data['suma_otros_imp'],
                'Total' => $data['total'],
                'Vencimiento' => $data['fecha_final'],
                'TasaIVA' => 16,
                'IdTipoDocto' => 13,
                'Estatus' => 11,
                'Alias_Depto' => $this->departamento->departamento_abreviatura,
                'IdSerie' => $this->idserie,
                'IdGenero' => auth()->id(),
                'registro_portal' => 1,
                'Departamento' => $this->departamento->departamento
            ]);

            $this->reembolsoGastoSol()->create([
                'idrelaciones_gastos' => $this->getKey(),
                'iddocumento' => $reembolso->getKey(),
                'idregistro' => auth()->id()
            ]);

            $this->crearCcDoctos($reembolso->getKey());

            DB::connection('controlrec')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}
