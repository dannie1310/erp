<?php

namespace App\Models\CONTROL_RECURSOS;


use App\Models\IGH\Departamento;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;

class ReembolsoGastoSol extends Documento
{
    protected $fillable = [
        'IdEmpresa',
        'IdProveedor',
        'Concepto',
        'IdMoneda',
        'Fecha',
        'FolioDocto',
        'Importe',
        'Retenciones',
        'IVA',
        'OtrosImpuestos',
        'Total',
        'Vencimiento',
        'TasaIVA',
        'IdTipoDocto',
        'Estatus',
        'Alias_Depto',
        'IdSerie',
        'IdGenero',
        'registro_portal',
        'Departamento'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('IdTipoDocto', '13');
            //->where('Estatus', 11);
        });
    }

    /**
     * Relaciones
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'IdProveedor', 'IdProveedor');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'iddepartamento', 'iddepartamento');
    }

    public function proyecto()
    {
        return $this->belongsTo(VwUbicacionRelacion::class, 'idproyecto', 'idubicacion');
    }

    public function ccDoctos()
    {
        return $this->hasMany(CcDocto::class, 'IdDocto', 'IdDocto');
    }

    public function relacionXDocumento()
    {
        return $this->belongsTo(RelacionGastoXDocumento::class, 'IdDocto', 'iddocumento');
    }

    /**
     * Atributos
     */
    public function getEmpleadoDescripcionAttribute()
    {
        try {
            return $this->proveedor->RazonSocial;
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

    /**
     * Métodos
     */
    public function editar(array $data)
    {
        $fecha_inicial = new DateTime($data['fecha_inicio_editar']);
        $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_inicial'] = $fecha_inicial->format("Y-m-d");
        $fecha_final = new DateTime($data['fecha_final_editar']);
        $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_final'] = $fecha_final->format("Y-m-d");
        try {
            DB::connection('controlrec')->beginTransaction();

            $this->update([
                'Concepto' => $data['motivo'],
                'Fecha' => $data['fecha_inicial'],
                'Vencimiento' => $data['fecha_final'],
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
           $ccDocto->delete();
        }
    }
}
