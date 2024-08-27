<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\IGH\Departamento;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;


class RelacionGasto extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'relaciones_gastos';
    protected $primaryKey = 'idrelaciones_gastos';
    public $timestamps = false;

    protected $fillable = [
        'numero_folio',
        'folio',
        'fecha_inicio',
        'fecha_fin',
        'idempresa',
        'idempleado',
        'idserie',
        'idmoneda',
        'iddepartamento',
        'idproyecto',
        'modifico_estado',
        'idestado',
        'motivo',
        'registro',
        'registro_portal'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('idserie', UsuarioSerie::porUsuario()->activo()->pluck('idseries'));
        });
    }

    /**
     * Relaciones
     */
    public function serie()
    {
        return $this->belongsTo(Serie::class, 'idserie', 'idseries');
    }

    public function documentos()
    {
        return $this->hasMany(RelacionGastoDocumento::class, 'idrelaciones_gastos', 'idrelaciones_gastos');
    }

    public function estados()
    {
        return $this->hasMany(RelacionGastoEstado::class, 'idrelaciones_gastos', 'idrelaciones_gastos');
    }

    public function estado()
    {
        return $this->belongsTo(CtgEstadoRelacion::class, 'idestado','idctg_estados_relaciones');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'idempresa', 'IdEmpresa');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'idempleado', 'IdProveedor');
    }

    public function proyecto()
    {
        return $this->belongsTo(VwUbicacionRelacion::class, 'idproyecto', 'idubicacion');
    }

    public function moneda()
    {
        return $this->belongsTo(CtgMoneda::class, 'idmoneda', 'id');
    }

    public function firmas()
    {
        return $this->hasMany(RelacionGastoFirma::class, 'idrelaciones_gastos', 'idrelaciones_gastos');
    }

    public function relacionEliminada()
    {
        return $this->belongsTo(RelacionGastoEliminado::class, 'idrelaciones_gastos', 'idrelaciones_gastos');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'iddepartamento', 'iddepartamento');
    }

    public function departamentoSn()
    {
        return $this->belongsTo(DepartamentoSn::class, 'iddepartamento', 'iddepartamento');
    }

    public function relacionGastoXDocumento()
    {
        return $this->belongsTo(RelacionGastoXDocumento::class, 'idrelaciones_gastos', 'idrelaciones_gastos');
    }

    public function reembolso()
    {
        return $this->hasManyThrough(Documento::class, RelacionGastoXDocumento::class, 'idrelaciones_gastos','IdDocto','idrelaciones_gastos','iddocumento');
    }

    /**
     * Scopes
     */


    /**
     * Atributos
     */
    public function getSerieDescripcionAttribute()
    {
        try {
            return $this->serie->Descripcion;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFechaInicioFormatAttribute()
    {
        $date = date_create($this->fecha_inicio);
        return date_format($date, "d/m/Y");
    }

    public function getFechaFinalFormatAttribute()
    {
        $date = date_create($this->fecha_fin);
        return date_format($date, "d/m/Y");
    }

    public function getEmpresaDescripcionAttribute()
    {
        try {
            return $this->empresa->RazonSocial;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getEmpleadoDescripcionAttribute()
    {
        try {
            return $this->proveedor->RazonSocial;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getRfcEmpleadoAttribute()
    {
        try {
            return $this->proveedor->RFC;
        } catch (\Exception $e) {
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

    public function getEstatusDescripcionAttribute()
    {
        try {
            return $this->estado->descripcion;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getColorEstadoAttribute()
    {
        switch ($this->idestado) {
            case 1:
                return '#EEE416';
            case 2:
                return '#EE9916';
            case 3:
                return '#EE5416';
            case 4:
                return '#5D9B23';
            case 5:
                return '#00a65a';
            case 6:
                return '#237A9B';
            case 7:
                return '#23559B';
            case 8:
                return '#B0B0BD';
            case 9:
                return '#A878DE';
            case 10:
                return '#E431F9';
            case 60:
                return '#B54AC2';
            case 70:
                return '#E22988';
            case 80:
                return '#E23F29';
            case 100:
                return '#E26529';
            case 600:
                return '#E2B029';
            case 700:
                return '#8B6C18';
            default:
                return '#d1cfd1';
        }
    }

    public function getTotalAttribute()
    {
       return $this->documentos()->sum('total');
    }

    public function getTotalFormatAttribute()
    {
        return '$' . number_format(($this->total),2);
    }

    public function getSumaImporteAttribute()
    {
        return $this->documentos()->sum('importe');
    }

    public function getSumaImporteFormatAttribute()
    {
        return '$' . number_format(($this->suma_importe),2);
    }

    public function getSumaIvaAttribute()
    {
        return $this->documentos()->sum('iva');
    }

    public function getSumaIvaFormatAttribute()
    {
        return '$' . number_format(($this->suma_iva),2);
    }

    public function getSumaRetencionesAttribute()
    {
        return $this->documentos()->sum('retenciones');
    }

    public function getSumaRetencionesFormatAttribute()
    {
        return '$' . number_format(($this->suma_retenciones),2);
    }

    public function getSumaOtrosImpAttribute()
    {
        return $this->documentos()->sum('otros_impuestos');
    }

    public function getSumaOtrosImpFormatAttribute()
    {
        return '$' . number_format(($this->suma_otros_imp),2);
    }

    public function getMonedaDescripcionAttribute()
    {
        try {
            return $this->moneda->moneda;
        }catch (\Exception $e)
        {
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

    public function getFechaInicioEditarAttribute()
    {
        $date = date_create($this->fecha_inicio);
        return date_format($date, "m/d/Y");
    }

    public function getFechaFinalEditarAttribute()
    {
        $date = date_create($this->fecha_fin);
        return date_format($date, "m/d/Y");
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->timestamp_registro);
        return date_format($date, "d/m/Y");
    }

    public function getDocumentosDeduciblesAttribute()
    {
        return $this->documentos()->deducibles();
    }

    public function getSumaTotalDeducibleAttribute()
    {
        return $this->documentos_deducibles->sum('total');
    }

    public function getSumaTotalDeducibleFormatAttribute()
    {
        return number_format(($this->suma_total_deducible),2);
    }

    public function getPorcentajeDeduciblesAttribute()
    {
        return (100 * $this->suma_total_deducible) / $this->total;
    }

    public function getPorcentajeDeduciblesFormatAttribute()
    {
        return number_format($this->porcentaje_deducibles, 2);
    }

    public function getSumaImporteDeducibleAttribute()
    {
        return $this->documentos_deducibles->sum('importe');
    }

    public function getSumaImporteDeducibleFormatAttribute()
    {
        return number_format(($this->suma_importe_deducible),2);
    }

    public function getSumaIvaDeducibleAttribute()
    {
        return $this->documentos_deducibles->sum('iva');
    }

    public function getSumaIvaDeducibleFormatAttribute()
    {
        return number_format(($this->suma_iva_deducible),2);
    }

    public function getSumaRetencionesDeducibleAttribute()
    {
        return $this->documentos_deducibles->sum('retenciones');
    }

    public function getSumaRetencionesDeducibleFormatAttribute()
    {
        return number_format(($this->suma_retenciones_deducible),2);
    }

    public function getSumaOtrosImpDeducibleAttribute()
    {
        return $this->documentos_deducibles->sum('otros_impuestos');
    }

    public function getSumaOtrosImpDeducibleFormatAttribute()
    {
        return number_format(($this->suma_otros_imp_deducible),2);
    }

    public function getDocumentosNoDeduciblesAttribute()
    {
        return $this->documentos()->noDeducibles();
    }

    public function getSumaTotalNoDeducibleAttribute()
    {
        return $this->documentos_no_deducibles->sum('total');
    }

    public function getSumaTotalNoDeducibleFormatAttribute()
    {
        return number_format(($this->suma_total_no_deducible),2);
    }

    public function getPorcentajeNoDeduciblesAttribute()
    {
        return (100 * $this->suma_total_no_deducible) / $this->total;
    }

    public function getPorcentajeNoDeduciblesFormatAttribute()
    {
        return number_format($this->porcentaje_no_deducibles, 2);
    }

    public function getSumaImporteNoDeducibleAttribute()
    {
        return $this->documentos_no_deducibles->sum('importe');
    }

    public function getSumaImporteNoDeducibleFormatAttribute()
    {
        return number_format(($this->suma_importe_no_deducible),2);
    }

    public function getSumaIvaNoDeducibleAttribute()
    {
        return $this->documentos_no_deducibles->sum('iva');
    }

    public function getSumaIvaNoDeducibleFormatAttribute()
    {
        return number_format(($this->suma_iva_no_deducible),2);
    }

    public function getSumaRetencionesNoDeducibleAttribute()
    {
        return $this->documentos_no_deducibles->sum('retenciones');
    }

    public function getSumaRetencionesNoDeducibleFormatAttribute()
    {
        return number_format(($this->suma_retenciones_no_deducible),2);
    }

    public function getSumaOtrosImpNoDeducibleAttribute()
    {
        return $this->documentos_no_deducibles->sum('otros_impuestos');
    }

    public function getSumaOtrosImpNoDeducibleFormatAttribute()
    {
        return number_format(($this->suma_otros_imp_no_deducible),2);
    }

    public function getNoDiasAttribute()
    {
        return $this->documentos()->selectRaw("COUNT(distinct(fecha)) AS dias")->first()->dias;
    }

    public function getNoDiasFormatAttribute()
    {
        return $this->no_dias . ($this->no_dias > 1 ? ' DIAS' : 'DIA');
    }

    public function getPromedioDiarioAttribute()
    {
        return $this->total / $this->no_dias;
    }

    public function getPromedioDiarioFormatAttribute()
    {
        return number_format(($this->promedio_diario),2);
    }

    public function getResumenGastosAttribute()
    {
        $consulta = " SELECT
            grupostipogastocomp.Descripcion AS concepto,
           (SELECT
                        SUM(relaciones_gastos_documentos.importe)
                    FROM
                        relaciones_gastos_documentos
                            INNER JOIN
                        controlrec.tiposgastocomp tiposgastocomp_ND ON (tiposgastocomp_ND.IdTipoGastoComp = relaciones_gastos_documentos.idtipo_gasto_comprobacion)
                            INNER JOIN
                        controlrec.grupostipogastocomp grupostipogastocomp_ND ON (tiposgastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp_ND.IdGrupoTipoGastoComp)
                    WHERE
                        relaciones_gastos_documentos.idtipo_docto_comp IN (1 , 2)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = " . $this->getKey() . "
                            AND grupostipogastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp.IdGrupoTipoGastoComp) AS importe_deducible,
           (SELECT
                        SUM(relaciones_gastos_documentos.iva)
                    FROM
                        relaciones_gastos_documentos
                            INNER JOIN
                        controlrec.tiposgastocomp tiposgastocomp_ND ON (tiposgastocomp_ND.IdTipoGastoComp = relaciones_gastos_documentos.idtipo_gasto_comprobacion)
                            INNER JOIN
                        controlrec.grupostipogastocomp grupostipogastocomp_ND ON (tiposgastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp_ND.IdGrupoTipoGastoComp)
                    WHERE
                        relaciones_gastos_documentos.idtipo_docto_comp IN (1 , 2)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = " . $this->getKey() . "
                            AND grupostipogastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp.IdGrupoTipoGastoComp) AS iva,
           (SELECT
                        SUM(relaciones_gastos_documentos.otros_impuestos)
                    FROM
                        relaciones_gastos_documentos
                            INNER JOIN
                        controlrec.tiposgastocomp tiposgastocomp_ND ON (tiposgastocomp_ND.IdTipoGastoComp = relaciones_gastos_documentos.idtipo_gasto_comprobacion)
                            INNER JOIN
                        controlrec.grupostipogastocomp grupostipogastocomp_ND ON (tiposgastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp_ND.IdGrupoTipoGastoComp)
                    WHERE
                        relaciones_gastos_documentos.idtipo_docto_comp IN (1 , 2)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = " . $this->getKey() . "
                            AND grupostipogastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp.IdGrupoTipoGastoComp) AS otros_impuestos,
            (SELECT
                        SUM(relaciones_gastos_documentos.retenciones)
                    FROM
                        relaciones_gastos_documentos
                            INNER JOIN
                        controlrec.tiposgastocomp tiposgastocomp_ND ON (tiposgastocomp_ND.IdTipoGastoComp = relaciones_gastos_documentos.idtipo_gasto_comprobacion)
                            INNER JOIN
                        controlrec.grupostipogastocomp grupostipogastocomp_ND ON (tiposgastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp_ND.IdGrupoTipoGastoComp)
                    WHERE
                        relaciones_gastos_documentos.idtipo_docto_comp IN (1 , 2)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = " . $this->getKey() . "
                            AND grupostipogastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp.IdGrupoTipoGastoComp) AS retenciones,
            (SELECT
                        SUM(relaciones_gastos_documentos.total)
                    FROM
                        relaciones_gastos_documentos
                            INNER JOIN
                        controlrec.tiposgastocomp tiposgastocomp_ND ON (tiposgastocomp_ND.IdTipoGastoComp = relaciones_gastos_documentos.idtipo_gasto_comprobacion)
                            INNER JOIN
                        controlrec.grupostipogastocomp grupostipogastocomp_ND ON (tiposgastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp_ND.IdGrupoTipoGastoComp)
                    WHERE
                        relaciones_gastos_documentos.idtipo_docto_comp IN (1 , 2)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = " . $this->getKey() . "
                            AND grupostipogastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp.IdGrupoTipoGastoComp) AS total_deducible,
            (SELECT
                        SUM(relaciones_gastos_documentos.total)
                    FROM
                        relaciones_gastos_documentos
                            INNER JOIN
                        controlrec.tiposgastocomp tiposgastocomp_ND ON (tiposgastocomp_ND.IdTipoGastoComp = relaciones_gastos_documentos.idtipo_gasto_comprobacion)
                            INNER JOIN
                        controlrec.grupostipogastocomp grupostipogastocomp_ND ON (tiposgastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp_ND.IdGrupoTipoGastoComp)
                    WHERE
                        relaciones_gastos_documentos.idtipo_docto_comp IN (3 , 4)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = " . $this->getKey() . "
                            AND grupostipogastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp.IdGrupoTipoGastoComp) AS importe_no_deducible,
            (SELECT
                        SUM(relaciones_gastos_documentos.total)
                    FROM
                        relaciones_gastos_documentos
                            INNER JOIN
                        controlrec.tiposgastocomp tiposgastocomp_ND ON (tiposgastocomp_ND.IdTipoGastoComp = relaciones_gastos_documentos.idtipo_gasto_comprobacion)
                            INNER JOIN
                        controlrec.grupostipogastocomp grupostipogastocomp_ND ON (tiposgastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp_ND.IdGrupoTipoGastoComp)
                    WHERE
                        relaciones_gastos_documentos.idrelaciones_gastos = " . $this->getKey() . "
                            AND grupostipogastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp.IdGrupoTipoGastoComp) AS total,";

        if ($this->no_dias > 0) {
            $consulta = $consulta . "((SELECT
                SUM(relaciones_gastos_documentos.total)
            FROM
                relaciones_gastos_documentos
                    INNER JOIN
                controlrec.tiposgastocomp tiposgastocomp_ND ON (tiposgastocomp_ND.IdTipoGastoComp = relaciones_gastos_documentos.idtipo_gasto_comprobacion)
                    INNER JOIN
                controlrec.grupostipogastocomp grupostipogastocomp_ND ON (tiposgastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp_ND.IdGrupoTipoGastoComp)
            WHERE
                relaciones_gastos_documentos.idrelaciones_gastos = " . $this->getKey() . "
                    AND grupostipogastocomp_ND.IdGrupoTipoGastoComp = grupostipogastocomp.IdGrupoTipoGastoComp) / " . $this->no_dias . ") AS promedio_diario";
        } else {
            $consulta = $consulta . "NULL as promedio_diario";
        }
        $consulta = $consulta . "
            FROM
                (controlrec.tiposgastocomp tiposgastocomp
                INNER JOIN controlrec.grupostipogastocomp grupostipogastocomp ON (tiposgastocomp.IdGrupoTipoGastoComp = grupostipogastocomp.IdGrupoTipoGastoComp))
                    INNER JOIN
                controlrec.relaciones_gastos_documentos relaciones_gastos_documentos ON (relaciones_gastos_documentos.idtipo_gasto_comprobacion = tiposgastocomp.IdTipoGastoComp)
            WHERE
                (relaciones_gastos_documentos.idrelaciones_gastos = " . $this->getKey() . ")
            GROUP BY grupostipogastocomp.Descripcion,controlrec.grupostipogastocomp.IdGrupoTipoGastoComp";

        $resumen = DB::connection('controlrec')->select(DB::raw($consulta));

        return $resumen;
    }

    public function getSumasResumenGastosAttribute()
    {
        $consulta = "
         SELECT format((select
                        SUM(relaciones_gastos_documentos.importe)
                    from
                        relaciones_gastos_documentos

                    where
                        relaciones_gastos_documentos.idtipo_docto_comp IN (1 , 2)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = ".$this->getKey()."
                            ),2) as importe_deducible,
                   format((select
                        SUM(relaciones_gastos_documentos.iva)
                    from
                        relaciones_gastos_documentos

                    where
                        relaciones_gastos_documentos.idtipo_docto_comp IN (1 , 2)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = ".$this->getKey()."
                            ),2) as iva,
                format((select
                        SUM(relaciones_gastos_documentos.otros_impuestos)
                    from
                        relaciones_gastos_documentos

                    where
                        relaciones_gastos_documentos.idtipo_docto_comp IN (1 , 2)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = ".$this->getKey()."
                            ),2) as otros_impuestos,
                format((select
                        SUM(relaciones_gastos_documentos.retenciones)
                    from
                        relaciones_gastos_documentos

                    where
                        relaciones_gastos_documentos.idtipo_docto_comp IN (1 , 2)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = ".$this->getKey()."
                            ),2) as retenciones,
                format((select
                        SUM(relaciones_gastos_documentos.total)
                    from
                        relaciones_gastos_documentos

                    where
                        relaciones_gastos_documentos.idtipo_docto_comp IN (1 , 2)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = ".$this->getKey()."
                            ),2) as total_deducible,
                format((select
                        SUM(relaciones_gastos_documentos.total)
                    from
                        relaciones_gastos_documentos

                    where
                        relaciones_gastos_documentos.idtipo_docto_comp IN (3,4)
                            AND relaciones_gastos_documentos.idrelaciones_gastos = ".$this->getKey()."
                            ),2) as importe_no_deducible,
                format((select
                        SUM(relaciones_gastos_documentos.total)
                    from
                        relaciones_gastos_documentos

                    where
                        relaciones_gastos_documentos.idrelaciones_gastos = ".$this->getKey()."
                            ),2) as total";

         if($this->no_dias > 0)
         {
             $consulta = $consulta . "
                 ,
                 format((select SUM(relaciones_gastos_documentos.total)
                        from
                            relaciones_gastos_documentos

                        where
                            relaciones_gastos_documentos.idrelaciones_gastos = ".$this->getKey()."
                                )/".$this->no_dias.",2) as promedio_diario ";
         }

        $consulta .= "FROM (controlrec.tiposgastocomp tiposgastocomp
                    INNER JOIN controlrec.grupostipogastocomp grupostipogastocomp ON (tiposgastocomp.IdGrupoTipoGastoComp = grupostipogastocomp.IdGrupoTipoGastoComp))
                        INNER JOIN
                    controlrec.relaciones_gastos_documentos relaciones_gastos_documentos ON (relaciones_gastos_documentos.idtipo_gasto_comprobacion = tiposgastocomp.IdTipoGastoComp)
                WHERE
                    (relaciones_gastos_documentos.idrelaciones_gastos = ".$this->getKey().")
                GROUP BY    relaciones_gastos_documentos.idrelaciones_gastos;";
        return DB::connection('controlrec')->select(DB::raw($consulta))[0];
    }

    public function getIdDocumentoAttribute()
    {
        try {
            return $this->relacionGastoXDocumento->iddocumento;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getIdSolicitudAttribute()
    {
        try {
            return $this->relacionGastoXDocumento->solicitudCheque->IdSolCheque;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    /**
     * Métodos
     */
    public function getNumeroFolio()
    {
        $folio = self::where('idserie', $this->idserie)->orderBy('numero_folio', 'desc')->pluck('numero_folio')->first();
        return $folio+1;
    }

    public function registrar($data)
    {
        try {
            DB::connection('controlrec')->beginTransaction();
            $relacion = $this->create([
                'fecha_inicio' => $data['fecha_inicial'],
                'fecha_fin' => $data['fecha_final'],
                'idempresa' => $data['id_empresa'],
                'idempleado' => $data['id_empleado'],
                'idserie' => $data['id_serie'],
                'idmoneda' => $data['id_moneda'],
                'iddepartamento' => $data['iddepartamento'],
                'idproyecto' => $data['id_proyecto'],
                'motivo' => $data['motivo']
            ]);
            DB::connection('controlrec')->commit();
            return $relacion;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function registrarDocumento($data)
    {
        try {
            DB::connection('controlrec')->beginTransaction();

            $documento = RelacionGastoDocumento::create([
                'idrelaciones_gastos' => $this->getKey(),
                'fecha' =>$data['fecha'],
                'folio' => $data['folio'],
                'idtipo_docto_comp' => $data['idtipo'],
                'idtipo_gasto_comprobacion' => $data['idtipogasto'],
                'no_personas' => $data['no_personas'],
                'importe' => (float)str_replace(',', '', $data['importe']),
                'iva' => (float)str_replace(',', '', $data['IVA']),
                'retenciones' => (float)str_replace(',', '', $data['retenciones']),
                'otros_impuestos' => (float)str_replace(',', '', $data['otro_imp']),
                'total' => (float)str_replace(',', '', $data['total']),
                'observaciones' => $data['observaciones'],
                'uuid' => $data['uuid']
            ]);

            DB::connection('controlrec')->commit();
            return $documento;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    /**
     * Se realiza la función para agregar los estados a tablas adicionales, pero ya se realiza por medio de SP
     */
    public function agregarEstados()
    {
        $this->estados()->create([
            'idrelaciones_gastos' => $this->getKey(),
            'idctg_estados_relaciones' => $this->idestado,
            'registro' => auth()->id()
        ]);
    }

    public function cerrar()
    {
        $this->validaCierre();
        try {
            DB::connection('controlrec')->beginTransaction();

            $this->update([
                'idestado' => 5
            ]);

            foreach ($this->documentos as $documento)
            {
                $documento->update([
                    'idestado' => 5
                ]);
            }

            DB::connection('controlrec')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function validaCierre()
    {
        if ($this->idestado != 2)
        {
            abort(500, "La relación de gastos (" . $this->folio . ") su estado es: '". $this->estatus_descripcion ."' no puede cerrarse.");
        }
    }

    public function abrir()
    {
        $this->validaApertura();
        try {
            DB::connection('controlrec')->beginTransaction();

            $this->update([
                'idestado' => 2
            ]);

            foreach ($this->documentos as $documento)
            {
                $documento->update([
                    'idestado' => 1
                ]);
            }

            DB::connection('controlrec')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function validaApertura()
    {
        if ($this->idestado != 5)
        {
            abort(500, "La relación de gastos (" . $this->folio . ") su estado es: '". $this->estatus_descripcion ."' no puede abrirse.");
        }
    }

    public function eliminar($motivo)
    {
        $this->validaEliminacion();
        try {
            DB::connection('controlrec')->beginTransaction();
            foreach ($this->documentos as $documento)
            {
                $documento->respaldar();
                if($documento->eliminadaErp == null)
                {
                    abort(400, "Error al eliminar, respaldo incorrecto.");
                }
                $documento->desvinculaFacturaRepositorio();
            }
            $this->delete();
            $this->relacionEliminada->update([
                'motivo_eliminacion' => $motivo
            ]);
            DB::connection('controlrec')->commit();
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    private function validaEliminacion()
    {
        if ($this->idestado != 1 && $this->idestado != 2)
        {
            abort(500, "La relación de gastos (" . $this->folio . ") su estado es: '". $this->estatus_descripcion ."' no puede eliminarse.");
        }
    }

    public function respaldar()
    {
        $this->relacionEliminada()->create([
            'idrelaciones_gastos' => $this->idrelaciones_gastos,
            'numero_folio' => $this->numero_folio,
            'folio' => $this->folio,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'idempresa' => $this->idempresa,
            'idempleado' => $this->idempleado,
            'idserie' => $this->idserie,
            'idmoneda' => $this->idmoneda,
            'iddepartamento' => $this->iddepartamento,
            'idproyecto' => $this->idproyecto,
            'modifico_estado' => $this->modifico_estado,
            'idestado' => $this->idestado,
            'motivo' => $this->motivo,
            'registro' => $this->registro,
            'timestamp_registro' => $this->timestamp_registro,
            'usuario_elimina' => auth()->id(),
            'fecha_eliminacion' => date('Y-m-d H:i:s')
        ]);
    }
}
