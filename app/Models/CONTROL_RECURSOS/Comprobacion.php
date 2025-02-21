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
        'registro_portal',
        'Estatus',
        'IdEstatus',
        'Cerro',
        'Fecha_Hora_Cerro'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('Alias_Depto', Serie::pluck('Descripcion'));
        });
    }

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

    public function solicitudesComprobadas()
    {
        return $this->hasMany(SolicitudComprobada::class, 'IdComprobacion', 'IdComprobacion');
    }

    public function reembolsos()
    {
        return $this->hasMany(ComprobacionReembolsada::class, 'idcomprobacion', 'IdComprobacion');
    }

    public function firmas()
    {
        return $this->hasMany(FirmaComprobaciones::class, 'idcomprobacion', 'IdComprobacion');
    }

    public function respaldo()
    {
        return $this->belongsTo(ComprobacionBorrada::class, 'IdComprobacion', 'IdComprobacion');
    }

    public function estado()
    {
        return $this->belongsTo(CtgEstadoComprobacion::class, 'IdEstatus', 'idctg_estados_comprobaciones');
    }

    public function moneda()
    {
        return $this->belongsTo(CtgMoneda::class,  'IdMoneda', 'id');
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

    public function getFechaDelFormatosAttribute()
    {
        $date = date_create($this->Del);
        return date_format($date,"d-m-Y");
    }

    public function getFechaAlFormatosAttribute()
    {
        $date = date_create($this->Al);
        return date_format($date,"d-m-Y");
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


    public function getDelEspecialAttribute()
    {
        try {
            return DoctoComp::selectRaw('MIN(FechaDocto) as fecha')->where('IdComprobacion', $this->IdComprobacion)->first()->fecha;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getAlEspecialAttribute()
    {
        try {
            return DoctoComp::selectRaw('MAX(FechaDocto) as fecha')->where('IdComprobacion', $this->IdComprobacion)->first()->fecha;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getEstadoFormatAttribute()
    {
        try {
            return  $this->estado->descripcion;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getEstadoColorAttribute()
    {
        try {
            if($this->IdEstatus == 0)
            {
                return  '#2d7ce8';
            }
            else if($this->IdEstatus == 1)
            {
                return  '#2d7ce8';
            }
            else if($this->IdEstatus == 2)
            {
                return  '#e8e22d';
            }
            else if($this->IdEstatus == 3)
            {
                return  '#29be22';
            }
            else{
                return '#51de48';
            }

           // return $this->Estatus == 2 ? '#e8e22d' : '#29be22';
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTotalComprobanteAttribute()
    {
        try {
            return  $this->documentos()->sum('Total');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getMonedaDescripcionAttribute()
    {
        try {
            return $this->moneda->moneda;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTotalDoctosAttribute()
    {
        try {
            return  $this->documentos()->count('IdDoctoComp');
        } catch (\Exception $e) {
            return null;
        }
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
           // $this->setFirmas();

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

    public function eliminar($motivo)
    {
        try {
            DB::connection('controlrec')->beginTransaction();

            $this->validaEliminacion();
        $esrelacion = 0;
        $escomprobacion = 0;
        $estareembolsadadocumento= 0;
        $estareembolsadoSolicitud = 0;
        $hayreembolsadadocumento= 0;
        $estaGenerada = 0;
        $estaAbierta = 0;
        $estaCerrada = 0;
        $nuevoEstatusRelacion = 0;

        if($this->solicitudesComprobadas->count() > 0)
        {
            $esrelacion = 0;
            $escomprobacion = 1;
        }else{
            $esrelacion = 1;
            $escomprobacion = 0;
        }
        if($escomprobacion == 1)
        {
            $this->solicitudesComprobadas()->delete();
        }

        if($this->reembolsos->count() > 0)
        {
            $estareembolsadadocumento= 1;
            if($this->reembolsos->solDoctos->count() > 0)
            {
                $estareembolsadoSolicitud = 1;
            }else{
                $estareembolsadoSolicitud = 0;
            }
        }else{
            $estareembolsadadocumento = 0;
            $hayreembolsadadocumento= 0;
        }


        if ($this->Estatus == 0 || $this->Estatus == 5) {
            $estaGenerada = 1;
        } else {
            if ($this->Estatus == 1 || $this->Estatus == 6) {
                $estaAbierta = 1;
            } else {
                if ($this->Estatus == 2 || $this->Estatus == 7) {
                    $estaCerrada = 1;
                }
            }
        }

        if ($estaCerrada == 1) {
            if ($this->Estatus == 2) {
                $nuevoEstatusRelacion = 1;
            }
            if ($this->Estatus == 7) {
                $nuevoEstatusRelacion = 6;
            }
            $this->update([
                'Estatus' => $nuevoEstatusRelacion
            ]);
        }
        $id= $this->getKey();
        if($hayreembolsadadocumento == 0)
        {
            $this->documentos()->delete();
            $this->firmas()->delete();
            $this->delete();
            $this->refresh();
            $comprobacion_eliminada = ComprobacionBorrada::where('IdComprobacion', $id)->first();
            $comprobacion_eliminada->usuario_elimina = auth()->id();
            $comprobacion_eliminada->motivo_eliminacion = $motivo;
            $comprobacion_eliminada->fecha_eliminacion = date('Y-m-d H:i:s');
            $comprobacion_eliminada->save();
        }else{
            if($estareembolsadadocumento == 1 && $estareembolsadoSolicitud == 0)
            {
                /**
                 * DELETE FROM cajas_chicas_reembolsos WHERE iddocto=IdDocumento1;

                DELETE FROM comprobaciones_reembolsadas WHERE idcomprobacion=idRelacion;

                DELETE FROM documentos where IdDocto=IdDocumento1;
                 */

                abort(500, "La comprobación no puede ser eliminada .");

                /**
                 *
                DELETE FROM doctoscomp where IdComprobacion=idRelacion;

                DELETE FROM firmas_comprobaciones where idcomprobacion=idRelacion;

                DELETE FROM comprobaciones where IdComprobacion=idRelacion;

                UPDATE comprobacionesborradas set Elimino=ejecuta, Fecha_Hora_Elimino=CURRENT_TIMESTAMP WHERE IdComprobacion=idRelacion;

                 */

            }else{
                if($estareembolsadadocumento == 1 && $estareembolsadoSolicitud == 1) {

                    /**
                    DELETE FROM cajas_chicas_reembolsos WHERE iddocto = IdDocumento1;

        DELETE FROM comprobaciones_reembolsadas WHERE idcomprobacion = idRelacion;

        CALL scr_sp_elimina_solicitud(IdSolicitud1, 1, CONCAT(ejecuta, 'Al eliminar Relacion: ', idRelacion));

        DELETE FROM documentos where IdDocto = IdDocumento1;
                     * */
                    abort(500, "La comprobación no puede ser eliminada .");

/*
                    DELETE FROM doctoscomp where IdComprobacion=idRelacion;

		DELETE FROM firmas_comprobaciones where idcomprobacion=idRelacion;

        DELETE FROM comprobaciones where IdComprobacion=idRelacion;

		UPDATE comprobacionesborradas set Elimino=ejecuta, Fecha_Hora_Elimino=CURRENT_TIMESTAMP WHERE IdComprobacion=idRelacion;
*/
                 }

            }
        }



            DB::connection('controlrec')->commit();
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    private function validaEliminacion()
    {
        if ($this->Estatus != 1 || $this->Estatus != 2 || $this->Estatus != 3)
        {
            abort(500, "La comprobación de gastos (" . $this->folio_format . ") su estado es: '". $this->estado_format ."' no puede eliminarse.");
        }
    }

    public function cerrar()
    {
        $this->validaCierre();
        try {
            DB::connection('controlrec')->beginTransaction();

            $estatus_nvo = $this->Estatus;
            $id_estatus_nvo = $this->IdEstatus;
            if($this->Estatus == 1)
            {
                $estatus_nvo = 2;
                $id_estatus_nvo = 3;
            }
            if($this->Estatus == 6)
            {
                $estatus_nvo = 7;
                $id_estatus_nvo = 3;
            }

            $this->update([
                'Del' => $this->del_especial,
                'Al' => $this->al_especial,
                'Estatus' =>  $estatus_nvo,
                'IdEstatus' => $id_estatus_nvo,
                'Cerro' => auth()->id(),
                'Fecha_Hora_Cerro' => date('Y-m-d H:i:s')
            ]);

            DB::connection('controlrec')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function validaCierre()
    {
        if($this->documentos->count() < 1)
        {
            abort(500, "La comprobación de gastos (" . $this->folio . ") no cuenta con documentos, por lo tanto, no puede cerrarse.");
        }

        if ($this->Estatus != 1)
        {
            abort(500, "La comprobación de gastos (" . $this->folio . ") su estado es: '". $this->estado_format ."' no puede cerrarse.");
        }

        $comprobacion = self::where('DoctoOrigen', $this->DoctoOrigen)->orderBy('IdComprobacion', 'desc')->first();

        if ($comprobacion->getKey() != $this->getKey())
        {
            abort(500, "La comprobación de gastos (" . $this->folio_format . ") no puede ser abierta porque se afectarian los saldos de las comprobaciones posteriores a esta aplicadas a la solicitud: ".$this->solicitud->folio_formato.".");
        }
    }

    public function abrir()
    {
        $this->validaApertura();
        dd("PASO");
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
        $comprobacion = self::where('DoctoOrigen', $this->DoctoOrigen)->orderBy('IdComprobacion', 'desc')->first();

        if ($comprobacion->getKey() != $this->getKey())
        {
            abort(500, "La comprobación de gastos (" . $this->folio_format . ") no puede ser abierta porque se afectarian los saldos de las comprobaciones posteriores a esta aplicadas a la solicitud: ".$this->solicitud->folio_formato.".");
        }

        if ($this->Estatus != 2)
        {
            abort(500, "La comprobación de gastos (" . $this->folio_format . ") su estado es: '". $this->estado_format ."' no puede abrirse.");
        }
    }
}
