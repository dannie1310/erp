<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Support\Facades\DB;

class ReembolsoPagoAProveedor extends Documento
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('IdTipoDocto', 11);
        });
    }



    /**
     * Métodos
     */
    public function registrar($data)
    {
        dd("AQUI");
        $fecha_inicial = new DateTime($data['fecha_inicio_editar']);
        $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_inicial'] = $fecha_inicial->format("Y-m-d");
        $fecha_final = new DateTime($data['fecha_final_editar']);
        $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_final'] = $fecha_final->format("Y-m-d");
        $relacion = RelacionGasto::where('idrelaciones_gastos', $data['id'])->first();
        $caja = CajaChica::where('idcajas_chicas', $data['id_caja'])->first();
        try {
            DB::connection('controlrec')->beginTransaction();

            $reembolso = $this->create([
                'IdEmpresa' => $relacion->idempresa,
                'Concepto' => $data['motivo'],
                'IdMoneda' => $relacion->idmoneda,
                'Fecha' => $data['fecha_inicial'],
                'FolioDocto' => $relacion->folio,
                'Importe' => $data['suma_importe'],
                'Retenciones' => $data['suma_retenciones'],
                'IVA' => $data['suma_iva'],
                'OtrosImpuestos' => $data['suma_otros_imp'],
                'Total' => $data['total'],
                'Vencimiento' => $data['fecha_final'],
                'TasaIVA' => 16,
                'IdTipoDocto' => 11,
                'Estatus' => 15,
                'IdGenero' => auth()->id(),
                'registro_portal' => 1,
                'Departamento' => $relacion->departamento->departamento,
                'IdSerie' => $caja->idserie,
                'Alias_Depto' => $caja->serie->Descripcion,
                'IdProveedor' => $caja->idempleado,
            ]);

            $this->relacionXDocumento()->create([
                'idrelaciones_gastos' => $relacion->getKey(),
                'iddocumento' => $reembolso->getKey(),
                'idregistro' => auth()->id()
            ]);

            $this->crearCcDoctos($reembolso->getKey(), $relacion);

            $this->cajaChicaReembolso()->create([
                'iddocto' => $reembolso->getKey(),
                'idcajas_chicas' => $data['id_caja']
            ]);

            DB::connection('controlrec')->commit();
            return $reembolso;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function crearCcDoctos($id_docto, $relacion)
    {
        $centro_costo = $relacion->departamentoSn->centroCosto;
        if($centro_costo == null)
        {
            $centro_costo = CentroCosto::where('Estatus', 1)->orderBy('IdCC')->pluck('IdCC')->first();
        }

        foreach ($relacion->documentos as $documento)
        {
            CcDocto::create([
                'IdDocto' => $id_docto,
                'IdCC' => $centro_costo->getKey(),
                'IdTipoGasto' => $documento->tipoGasto->getKey(),
                'Importe' => $documento->importe,
                'IVA' => $documento->iva,
                'OtrosImpuestos' => $documento->otros_impuestos,
                'Retenciones' => $documento->retenciones,
                'Total' => $documento->total,
                'Facturable' => 'N'
            ]);
        }
    }

}
