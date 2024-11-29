<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comprobacion extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'comprobaciones';
    protected $primaryKey = 'IdComprobacion';

    public $timestamps = false;
    protected $fillable = [
        'IdDepartamento',
        'Fecha',
        'Del',
        'Al',
        'IdUbicacionVisitada',
        'Ubicacion',
        'EmpresaFacturar',
        'Empleado',
        'Motivo',
        'Serie',
        'Folio',
        'DoctoOrigen',
        'ImpSol',
        'SaldoComp',
        'IdMoneda',
        'Creo',
        'Solicita',
        'JefeArea',
        'DirectorArea',
        'AutTramiteFinanzas',
        'Version',
        'Alias_Depto' ,
        'Cerro',
        'Fecha_RcbCCH',
        'Recibio_CCH',
        'Fecha_RcbCTb',
        'registro_portal'
    ];

    /**
     * Relaciones
     */
    public function solicitud()
    {
        return $this->belongsTo(SolChequeComprobacion::class, 'DoctoOrigen','IdSolCheques');
    }

    public function empleado()
    {
        return $this->belongsTo(Proveedor::class,   'Empleado','IdProveedor')->empleados();
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class,  'EmpresaFacturar','IdEmpresa');
    }

    public function proyecto()
    {
        return $this->belongsTo(VwUbicacionRelacion::class, 'IdUbicacionVisitada', 'idubicacion');
    }

    public function documentos()
    {
        return $this->hasMany(DoctoComp::class, 'IdComprobacion','IdComprobacion');
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */
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
            return $this->empleado->RazonSocial;
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

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFechaDelFormatAttribute()
    {
        $date = date_create($this->Del);
        return date_format($date,"d/m/Y");
    }

    public function getFechaAlFormatAttribute()
    {
        $date = date_create($this->Al);
        return date_format($date,"d/m/Y");
    }

    public function getFechaDelEditarAttribute()
    {
        $date = date_create($this->Del);
        return date_format($date, "m/d/Y");
    }

    public function getFechaAlEditarAttribute()
    {
        $date = date_create($this->Al);
        return date_format($date, "m/d/Y");
    }

    public function getFolioFormatAttribute()
    {
        return $this->Serie."-".$this->Folio;
    }

    public function getDepartamentoAttribute()
    {
        try {
            return $this->empleado->usuario->departamento->departamento;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFolioSolicitudAttribute()
    {
        try {
            return $this->solicitud->folio_formato;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getImporteFormatAttribute()
    {
        return '$' . number_format(($this->ImpSol),2);
    }

    public function getSaldoCompFormatAttribute()
    {
        return '$' . number_format(($this->SaldoComp),2);
    }

    public function getImpDoctosFormatAttribute()
    {
        return '$' . number_format(($this->ImpDoctos),2);
    }


    /**
     * Métodos
     */
    public function registrar($data)
    {
        try {
            DB::connection('controlrec')->beginTransaction();
            $usuario = Usuario::where('idusuario',auth()->id())->first();
            $creo = $usuario->nombre_completo. "|" . date("d-m-Y") . "|" . date("H:i:s", time());
            $firmas = $this->getFirmas($data['solicitud']['id_proveedor']);
            $comprobacion = $this->create([
                'IdDepartamento' => $data['solicitud']['iddepartamento'],
                'Fecha' => date("Y-m-d"),
                'Del' => $data['fecha_inicial'],
                'Al' => $data['fecha_final'],
                'IdUbicacionVisitada' => $data['id_proyecto'],
                'Ubicacion' => $data['solicitud']['ubicacion'],
                'EmpresaFacturar' => $data['solicitud']['idempresa'],
                'Empleado' => $data['solicitud']['id_proveedor'],
                'Motivo' => $data['solicitud']['concepto'],
                'DoctoOrigen' => $data['solicitud']['id'],
                'ImpSol' => $data['solicitud']['importe'],
                'SaldoComp' => $data['solicitud']['importe'],
                'IdMoneda' => $data['solicitud']['id_moneda'],
                'Solicita' => $data['solicitud']['empleado_descripcion'],
                'JefeArea' => $firmas ? $firmas->Autoriza : '',
                'DirectorArea' => $firmas ? $firmas->VoBo : '',
                'AutTramiteFinanzas' => $firmas ? $firmas->Aplicacion : '',
                'Version' => 2,
                'Alias_Depto' => $data['solicitud']['serie'],
                'Cerro' => 0,
                'Recibio_CCH' => 0,
                'registro_portal' => 1
            ]);
            $this->setFirmas();

            SolicitudComprobada::create([
                'IdSolcheque' => $data['solicitud']['id'],
                'IdComprobacion' => $comprobacion->getKey(),
                'ImporteSolicitud' => $comprobacion->solicitud->Importe,
                'ImporteComprobadoAnterior' => $comprobacion->solicitud->SaldoComprobado,
                'ImportePorComprobar' => $comprobacion->solicitud->Importe - $comprobacion->solicitud->SaldoComprobado
            ]);
            DB::connection('controlrec')->commit();
            return $comprobacion;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function getNumeroFolio()
    {
        $folio = self::where('serie', 'REL')->orderBy('Folio', 'desc')->pluck('Folio')->first();
        return $folio+1;
    }

    public function getFolioVD()
    {
        $folio = self::orderBy('FolioVD', 'desc')->pluck('FolioVD')->first();
        return $folio+1;
    }

    public function getFirmas($id_solicitante)
    {
        $firma = FirmaComprobacionEspecial::where('IdSolicitante', $id_solicitante)->first();
        if($firma == null)
        {
            $firma= FirmaComprobacion::where('IdUsuario', auth()->id())->first();
        }
        return $firma;
    }

    public function setFirmas(){

    }

    public function registrarDocumento($data)
    {
        try {
            DB::connection('controlrec')->beginTransaction();

            if($data['tipo_docto_comp'] == 1 | $data['tipo_docto_comp'] == 2){
                $clasificacion = 1;
                $serieVD='';
                $folioVD=0;
            }else{
                $clasificacion = 2;
                if($this->FolioVD == 0)
                {
                    $this->update([
                        'SerieVD' => 'VDE',
                        'FolioVD' => $this->getFolioVD()
                    ]);
                }
                $serieVD = $this->SerieVD;
                $folioVD = $this->FolioVD;
            }


            $documento = DoctoComp::create([
                'IdComprobacion' => $this->getKey(),
                'FechaDocto' => $data['fecha'],
                'TipoDoctoComp' => $data['tipo_docto_comp'],
                'FolioDocto' => $data['folio_docto'],
                'Concepto' => $data['tipo_gasto'],
                'Importe' => $data['importe'],
                'IVA' => $data['iva'],
                // 'ImpHosp',
                'Total' => $data['total'],
                'Observaciones' => $data['observaciones'],
                'Clasificacion' => $clasificacion,
                'SerieVD' => $serieVD,
                'FolioVD' => $folioVD,
                'Registro' => auth()->id(),
                'uuid' => array_key_exists('uuid', $data) ? $data['uuid'] : null,
                'descuento_cfdi' =>  array_key_exists('descuento_cfdi', $data) ? $data['descuento_cfdi'] : '0.00'
            ]);

            DB::connection('controlrec')->commit();
            return $documento;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function sumaTotalDocumentos()
    {
        $suma = 0;
        foreach ($this->documentos as $documento)
        {
            $suma += $documento->Total;
        }
        return $suma;
    }

    public function actualizaSaldoPendienteSolicitud()
    {
        $this->refresh('documentos');
        $importe_comprobacion = $this->sumaTotalDocumentos();
        if($importe_comprobacion == null) {
            $importe_comprobacion = 0;
        }

        $importe_devoluciones = $this->solicitud->devolucion ? $this->solicitud->devolucion->ImporteDevueltoActual : null;
        if($this->solicitud->devolucion == null)
        {
            $importe_devoluciones = 0;
        }else{
            $importe_devoluciones = $this->solicitud->devolucion->ImporteDevueltoActual;
        }
        $total_comprobado =  $importe_comprobacion + $importe_devoluciones;

        $this->solicitud->update([
            'SaldoComprobado' => $total_comprobado
        ]);
    }

    public function estatusComprobacion()
    {
        if ($this->Estatus == 0 || $this->Estatus == 5) {
            switch ($this->Estatus) {
                case 0:
                    $this->Estatus = 1;
                    $this->IdEstatus = 2;
                    $this->save();
                    break;
                case 5:
                    $this->Estatus = 6;
                    $this->IdEstatus = 2;
                    $this->save();
                    break;
            }
        }
    }
}
