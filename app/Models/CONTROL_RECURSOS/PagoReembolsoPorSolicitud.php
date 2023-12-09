<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Support\Facades\DB;

class PagoReembolsoPorSolicitud extends SolCheque
{
    /**
     * Métodos
     */
    public function registrar($datos)
    {
        try {
            $reembolso = ReembolsoGastoSol::where('IdDocto',$datos['reembolso']['id'])->first();

            if($reembolso->Estatus == 11)
            {
                abort(500, "Este reembolso ya se encuentra asociado a una solicitud.");
            }

            $fecha = new DateTime($datos['reembolso']['fecha_inicio_editar']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fecha_fin = new DateTime($datos['reembolso']['fecha_final_editar']);
            $fecha_fin->setTimezone(new DateTimeZone('America/Mexico_City'));

            DB::connection('controlrec')->beginTransaction();

            $sol_cheque = $this->create([
                'Fecha' => date('Y-m-d'),
                'Vencimiento' => $fecha_fin->format('Y-m-d'),
                'IdEmpresa' => $reembolso->IdEmpresa,
                'IdProveedor' => $reembolso->IdProveedor,
                'IdMoneda' => $reembolso->IdMoneda,
                'Importe' => $reembolso->Importe,
                'Retenciones' => $reembolso->Retenciones,
                'IVA' => $reembolso->IVA,
                'OtrosImpuestos' => $reembolso->OtrosImpuestos,
                'Total' => $reembolso->Total,
                'Concepto' => $datos['reembolso']['motivo'],
                'Estatus' => 30,
                'IdTipoSolicitud' => 4,
                'IdFormaPago' => $datos['forma_pago'],
                'IdTipoPago' => 6,
                'IdEntrega' => $datos['instruccion'],
                'Cuenta2' => $datos['cuenta'],
                'IdSerie' => $reembolso->IdSerie,
                'Serie' => $reembolso->Alias_Depto,
                'IdGenero' => auth()->id(),
                'FechaFactura' =>  $fecha->format("Y-m-d"),
            ]);

            foreach ($reembolso->ccDoctos as $docto)
            {
                CcSolCheque::create([
                    'IdCCDoctos' => $docto->IdCCDoctos,
                    'IdSolCheque' => $sol_cheque->getKey(),
                    'IdCC' => $docto->IdCC,
                    'IdTipoGasto' => $docto->IdTipoGasto,
                    'Importe' => $docto->Importe,
                    'IVA' => $docto->IVA,
                    'OtrosImpuestos' => $docto->OtrosImpuestos,
                    'Retenciones' => $docto->Retenciones,
                    'Total' => $docto->Total,
                    'PorcentajeFacturar' => $docto->PorcentajeFacturar,
                    'ImporteFacturar' => $docto->ImporteFacturar,
                    'Facturable' => $docto->Facturable
                ]);
            }

            SolChequeDocto::create([
                'IdSolCheque' => $sol_cheque->getKey(),
                'IdDocto' => $reembolso->getKey()
            ]);

            $reembolso->update([
                'Estatus'  => 13
            ]);
            DB::connection('controlrec')->commit();
            return $sol_cheque;

        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}
