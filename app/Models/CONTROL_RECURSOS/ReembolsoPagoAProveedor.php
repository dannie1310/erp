<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;

class ReembolsoPagoAProveedor extends Documento
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('IdTipoDocto', 12);
        });
    }

    /**
     * Relaciones
     */
    public function relacionXDocumento()
    {
        return $this->belongsTo(RelacionGastoXDocumento::class, 'IdDocto', 'iddocumento');
    }

    public function ccDoctos()
    {
        return $this->hasMany(CcDocto::class, 'IdDocto', 'IdDocto');
    }

    public function departamentoSn()
    {
        return $this->belongsTo(DepartamentoSn::class, 'iddepartamento', 'iddepartamento');
    }

    public function relacion()
    {
        return $this->hasManyThrough(RelacionGasto::class, RelacionGastoXDocumento::class, 'iddocumento','idrelaciones_gastos','IdDocto','idrelaciones_gastos');
    }

    /**
     * Atributos
     */
    public function getEmpleadoDescripcionAttribute()
    {
        try {
            return $this->relacionXDocumento->relacion->empleado_descripcion;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getDepartamentoDescripcionAttribute()
    {
        try {
            return $this->departamento->departamento;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getProyectoDescripcionAttribute()
    {
        try {
            return $this->proyecto->ubicacion;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getOtrosImpuestosFormatAttribute()
    {
        return '$' . number_format(($this->OtrosImpuestos),2);
    }

    public function getFirmaSolicitanteAttribute()
    {
        try {
            return $this->relacionXDocumento->relacion->firmas[0]->idfirmas_firmantes;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getIdRelacionAttribute()
    {
        try {
            return $this->relacionXDocumento->idrelaciones_gastos;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Métodos
     */
    public function registrar($data)
    {
        $fecha_inicial = new DateTime($data['fecha_inicio_editar']);
        $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_inicial'] = $fecha_inicial->format("Y-m-d");
        $fecha_final = new DateTime($data['fecha_final_editar']);
        $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_final'] = $fecha_final->format("Y-m-d");
        $relacion = RelacionGasto::where('idrelaciones_gastos', $data['id'])->first();
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
                'Vencimiento' =>  date('Y-m-d'),
                'TasaIVA' => 16,
                'IdTipoDocto' => 12,
                'Estatus' => 1,
                'IdGenero' => auth()->id(),
                'registro_portal' => 1,
                'Departamento' => $relacion->departamento->departamento,
                'IdSerie' => $relacion->idserie,
                'Alias_Depto' => $relacion->serie->Descripcion,
                'IdProveedor' => $data['id_proyecto_seleccionado'],
            ]);

            $this->relacionXDocumento()->create([
                'idrelaciones_gastos' => $relacion->getKey(),
                'iddocumento' => $reembolso->getKey(),
                'idregistro' => auth()->id()
            ]);

            $this->crearCcDoctos($reembolso->getKey(), $relacion);

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

    public function editar(array $data)
    {
        $fecha_final = new DateTime($data['fecha_final_editar']);
        $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_final'] = $fecha_final->format("Y-m-d");
        try {
            DB::connection('controlrec')->beginTransaction();

            $this->update([
                'Concepto' => $data['motivo'],
                'Vencimiento' => $data['fecha_final'],
                'IdProveedor' => $data['id_proveedor'],
            ]);

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
            $this->solChequeDocto()->delete();
            $this->eliminarDocumentos();
            $this->relacionXDocumento()->delete();
            $this->delete();
            $this->respaldo();
            DB::connection('controlrec')->commit();
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    private function eliminarDocumentos()
    {
        foreach ($this->ccDoctos as $ccDocto)
        {
            $ccDocto->ccSolCheque->delete();
            $ccDocto->delete();

        }
    }
}
