<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Support\Facades\DB;

class PagoAProveedor extends SolCheque
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('IdTipoSolicitud', '6');
        });
    }

    /**
     * Métodos
     */
    public function registrar($datos)
    {
        try {
            $reembolso = ReembolsoPagoAProveedor::where('IdDocto',$datos['reembolso']['id'])->first();

            if($reembolso->solChequeDocto != null)
            {
                abort(500, "Este reembolso ya se encuentra asociado a una solicitud.");
            }

            DB::connection('controlrec')->beginTransaction();

            $sol_cheque = $this->create([
                'Fecha' => date('Y-m-d'),
                'Vencimiento' => $reembolso->Fecha,
                'IdEmpresa' => $reembolso->IdEmpresa,
                'IdProveedor' => $reembolso->IdProveedor,
                'IdMoneda' => $reembolso->IdMoneda,
                'Importe' => $reembolso->Importe,
                'Retenciones' => $reembolso->Retenciones,
                'IVA' => $reembolso->IVA,
                'OtrosImpuestos' => $reembolso->OtrosImpuestos,
                'Total' => $reembolso->Total,
                'Concepto' => $datos['reembolso']['concepto'],
                'IdFormaPago' => $datos['forma_pago'],
                'IdEntrega' => $datos['instruccion'],
                'Cuenta2' => $datos['cuenta'],
                'IdSerie' => $reembolso->IdSerie,
                'Serie' => $reembolso->Alias_Depto,
                'FechaFactura' =>  $reembolso->Fecha
            ]);
dd($sol_cheque);
            /*
             * crear en documentos y solicitudes un id para saber q se creo desde el erp
             */
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

            $sol_cheque->setFirmasSolicitantes($datos['solicitante']);

            $reembolso->update([
                'Estatus'  => 2
            ]);

            $reembolso->relacion[0]->update([
                'idestado' => 7
            ]);

            DB::connection('controlrec')->commit();
            return $sol_cheque;

        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function editar($datos)
    {
        try {
            DB::connection('controlrec')->beginTransaction();
            $this->update([
                'Concepto' => $datos['concepto'],
                'IdFormaPago' => $datos['id_forma_pago'],
                'IdEntrega' => $datos['id_entrega'],
                'Cuenta2' => array_key_exists('cuenta', $datos) ? $datos['cuenta'] : null,
            ]);

            $this->updateFirmasSolicitantes($datos['id_solicitante']);

            DB::connection('controlrec')->commit();
            return $this;

        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function eliminar()
    {
        try {
            DB::connection('controlrec')->beginTransaction();
            $this->deleteFirmasSolicitantes();
            $reembolso = ReembolsoGastoSol::where('IdDocto',$this->solChequeDocto->IdDocto)->first();
            $this->solChequeDocto()->delete();
            $reembolso->update([
                'Estatus'  => 1
            ]);
            $this->ccSolCheques()->delete();
            $this->delete();
            DB::connection('controlrec')->commit();
            return [];

        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }

    }
}
