<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\CTPQ;

use App\Facades\Context;
use App\Models\CADECO\Movimiento;
use App\Models\CADECO\Obra;
use App\Models\CTPQ\GeneralesSQL\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\LogEdicion;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionMovimientos;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Facades\DB;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use Illuminate\Support\Facades\Config;
use App\Models\CTPQ\DocumentMetadata\Comprobante;


class Poliza extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'Polizas';
    protected $primaryKey = 'Id';

    protected $fillable = [
        'Fecha',
        'Ejercicio',
        'Periodo',
        'TipoPol',
        'Folio',
        'Cargos',
        'Abonos'
    ];

    protected $dates = ["Fecha"];
    //protected $dateFormat = 'Y-m-d H:i:s';

    public $timestamps = false;

    /**
     * Relaciones
     */
    public function movimientos()
    {
        return $this->hasMany(PolizaMovimiento::class,  'IdPoliza','Id');
    }

    public function cuentas()
    {
        return $this->hasManyThrough(Cuenta::class, PolizaMovimiento::class, "IdPoliza", "Id","Id", "IdCuenta");
    }

    public function tipo_poliza()
    {
        return $this->belongsTo(TipoPoliza::class, 'TipoPol', 'Id');
    }

    public function logs()
    {
        return $this->hasMany(LogEdicion::class, 'id_poliza', 'Id');
    }

    public function asociacionCFDI()
    {
        return $this->hasMany(AsocCFDI::class, "GuidRef", "Guid");
    }

    public function expedientes()
    {
        return $this->hasMany(Expediente::class, 'Guid_Relacionado', 'Guid');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class,"IdUsuario","Id");
    }


    /**
     * Attributos
     */
    public function getCargosFormatAttribute()
    {
        return '$' . number_format(abs($this->Cargos), 2);
    }

    public function getAbonosFormatAttribute()
    {
        return '$' . number_format(abs($this->Abonos), 2);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date, "d/m/Y");
    }

    public function getFechaMesLetraFormatAttribute()
    {
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $mes = $meses[($this->Fecha->format('n')) - 1];
        //setlocale(LC_ALL,"es_ES");
        $fecha = New DateTime($this->Fecha);
        return strftime("%d/", $fecha->getTimestamp()) . substr($mes, 0, 3) . strftime("/%Y", $fecha->getTimestamp());
    }

    public function getFechaConsultaAttribute()
    {
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $mes = $meses[($this->Fecha->format('n')) - 1];
        $fecha = new DateTime($this->Fecha);
        $fecha->add(new \DateInterval('P5D'));
        $fecha = strftime("%d/", $fecha->getTimestamp()) . substr($mes, 0, 3) . strftime("/%Y", $fecha->getTimestamp());
        return $fecha;
    }
    public function getEmpresaAttribute()
    {
        $idEmpresa = Parametro::find(1)->pluck("IdEmpresa")->first();
        return Empresa::find($idEmpresa)->Nombre;
    }
    public function getBaseDatosAttribute()
    {
        $idEmpresa = Parametro::find(1)->pluck("IdEmpresa")->first();
        return Empresa::find($idEmpresa)->AliasBDD;
    }

    /**
     * Métodos
     */
    public function actualiza($datos)
    {
        $fecha = Carbon::createFromFormat('d/m/Y', date_format(date_create($datos['fecha_completa']['date']), "d/m/Y"));
        if ($this->Ejercicio != 2015) {
            try {
                DB::connection('cntpq')->beginTransaction();
                DB::connection('seguridad')->beginTransaction();
                if ($datos['concepto'] != $this->Concepto) {
                    $this->Concepto = $datos["concepto"];
                }
                if ($fecha->format('d/m/Y') != $this->fecha_format) {
                    $this->Fecha = $fecha;
                    $this->Ejercicio = $fecha->format('Y');
                    $this->Periodo = $fecha->format('m');
                }
                if ($datos['tipo']['id'] != $this->TipoPol) {
                    $this->TipoPol = $datos["tipo"]["id"];
                }
                if ($datos['folio'] != $this->Folio) {
                    $this->Folio = $datos["folio"];
                }
                if ($datos['cargo_nuevo'] != $this->Cargos) {
                    $this->Cargos = $datos["cargo_nuevo"];
                }
                if ($datos['abono_nuevo'] != $this->Abonos) {
                    $this->Abonos = $datos["abono_nuevo"];
                }
                $this->update();
                $find = 0;
                foreach ($this->movimientos as $movimiento) {
                    foreach ($datos["movimientos_poliza"]["data"] as $key => $dato_nuevo) {
                        if (array_key_exists('id', $dato_nuevo) && $dato_nuevo['id'] == $movimiento->Id) {
                            $find = 1;
                            if ($movimiento->Referencia != $dato_nuevo["referencia"]) {
                                $movimiento->Referencia = $dato_nuevo["referencia"];
                            }
                            if ($movimiento->Concepto != $dato_nuevo["concepto"]) {
                                $movimiento->Concepto = $dato_nuevo["concepto"];
                            }
                            if ($movimiento->NumMovto != $key) {
                                $movimiento->NumMovto = $key + 1;
                            }
                            if ($fecha->format('d/m/Y') != $movimiento->fecha_format) {
                                $movimiento->Fecha = $fecha->format('Y-m-d');
                                $movimiento->Ejercicio = $fecha->format('Y');
                                $movimiento->Periodo = $fecha->format('m');
                            }
                            if ($datos['tipo']['id'] != $movimiento->TipoPol) {
                                $movimiento->TipoPol = $datos["tipo"]["id"];
                            }
                            if ($datos['folio'] != $movimiento->Folio) {
                                $movimiento->Folio = $datos["folio"];
                            }
                            if ($movimiento->IdCuenta != $dato_nuevo["cuenta"]["id"]) {
                                $movimiento->IdCuenta = $dato_nuevo["cuenta"]["id"];
                            }
                            if ($movimiento->TipoMovto != $dato_nuevo["tipo"]) {
                                $movimiento->TipoMovto = $dato_nuevo["tipo"];
                            }
                            if ($movimiento->Importe != $dato_nuevo["importe"]) {
                                $movimiento->Importe = $dato_nuevo["importe"];
                            }
                            $movimiento->update();
                        }
                    }
                    if ($find == 0) {
                        $movimiento->delete();
                    }
                    $find = 0;
                }
                foreach ($datos["movimientos_poliza"]["data"] as $key => $dato_nuevo)
                {
                    if(array_key_exists('id', $dato_nuevo) == false){
                        $this->movimientos()->create([
                            'IdPoliza' => $this->Id,
                            'Ejercicio' => $fecha->format('Y'),
                            'Periodo' => $fecha->format('m'),
                            'TipoPol' => $datos["tipo"]["id"],
                            'Folio' => $datos["folio"],
                            'NumMovto' => $key + 1,
                            'IdCuenta' => $dato_nuevo["cuenta"]["id"],
                            'TipoMovto' => $dato_nuevo["tipo"],
                            'Importe' => $dato_nuevo["importe"],
                            'Referencia' => $dato_nuevo["referencia"],
                            'Concepto' => $dato_nuevo["concepto"],
                            'Fecha' => $fecha->format('Y-m-d'),
                        ]);
                    }
                }
                DB::connection('seguridad')->commit();
                DB::connection('cntpq')->commit();
                return $this;
            } catch (\Exception $e) {
                DB::connection('seguridad')->rollBack();
                DB::connection('cntpq')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        }
    }

    public function getPolizaReferencia(RelacionPolizas $relacion)
    {
        $poliza_relacionada = null;
        try {
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $relacion->base_datos_b);
            $poliza_relacionada = Poliza::find($relacion->id_poliza_b);
            $poliza_relacionada->load("movimientos");
            foreach ($poliza_relacionada->movimientos as $movimiento) {
                $movimiento->load("cuenta");
            }
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $relacion->base_datos_a);
        } catch (\Exception $e) {

        }
        return $poliza_relacionada;
    }

    public function getPolizaRelacionada($busqueda)
    {
        $poliza_referencia = null;
        try {
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $busqueda->base_datos_referencia);
            $poliza_referencia = Poliza::where("Ejercicio", $this->Ejercicio)->where("Periodo", $this->Periodo)
                ->where("TipoPol", $this->TipoPol)->where("Folio", $this->Folio)->first();
        } catch (\Exception $e) {

        }
        return $poliza_referencia;
    }

    public function relaciona($busqueda)
    {
        $poliza_relacionada = $this->getPolizaRelacionada($busqueda);
        $relaciones = [];
        if ($poliza_relacionada) {
            $datos_relacion = [
                "id_poliza_a" => $this->Id,
                "base_datos_a" => $busqueda->base_datos_busqueda,
                "id_poliza_b" => $poliza_relacionada->Id,
                "base_datos_b" => $busqueda->base_datos_referencia,
                "tipo_relacion" => $busqueda->id_tipo_busqueda,
                "folio" => $this->Folio,
                "ejercicio" => $this->Ejercicio,
                "periodo" => $this->Periodo,
                "tipo" => $this->TipoPol
            ];
            $relacion_poliza = RelacionPolizas::registrar($datos_relacion);
            $relaciones_movimientos = $this->relaciona_movimientos($busqueda);
            if ($relacion_poliza) {
                $relaciones["relacion_poliza"] = $relacion_poliza;
                $this->resuelveIncidenteNoEncontrada($busqueda);
            }
            if ($relaciones_movimientos) {
                $relaciones["relaciones_movimientos"] = $relaciones_movimientos;
            }
        } else {

            $relacion = RelacionPolizas::where("id_poliza_a","=", $this->Id)
                ->where("base_datos_a", "=", $busqueda->base_datos_busqueda)
                ->where("tipo_relacion","=", $busqueda->id_tipo_busqueda)
                ->first();
            if($relacion)
            {
                $relacion->activa = 0;
                $relacion->save();
            }
            $this->registraIncidenteNoEncontrada($busqueda);
        }
        return $relaciones;
    }

    private function resuelveIncidenteNoEncontrada($busqueda)
    {
        $datos_diferencia = [
            "id_poliza" => $this->Id,
            "base_datos_revisada" => $busqueda->base_datos_busqueda,
            "base_datos_referencia" => $busqueda->base_datos_referencia,
            "id_tipo" => 1,
            "tipo_busqueda" => $busqueda->id_tipo_busqueda,
            "id_busqueda" => $busqueda->id,
        ];
        $diferencia_prexistente = Diferencia::buscarSO($datos_diferencia);
        if ($diferencia_prexistente) {
            $diferencia_prexistente->corregir($busqueda->id);
        }
    }

    private function registraIncidenteNoEncontrada($busqueda)
    {
        $datos_diferencia = [
            "id_poliza" => $this->Id,
            "base_datos_revisada" => $busqueda->base_datos_busqueda,
            "base_datos_referencia" => $busqueda->base_datos_referencia,
            "id_tipo" => 1,
            "tipo_busqueda" => $busqueda->id_tipo_busqueda,
            "observaciones" => "",
            "id_busqueda" => $busqueda->id,
            "ejercicio"=>$this->Ejercicio,
            "periodo"=>$this->Periodo,
            "tipo_poliza"=>$this->tipo_poliza->Nombre,
            "folio_poliza"=>$this->Folio,
            "fecha_poliza"=>$this->Fecha
        ];
        Diferencia::registrar($datos_diferencia);
    }

    private function relaciona_movimientos($busqueda)
    {
        $relaciones_movimientos = [];
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $busqueda->base_datos_busqueda);
        $movimientos = $this->movimientos()->orderBy("NumMovto")->get();
        $poliza_relacionada = $this->getPolizaRelacionada($busqueda);
        $movimientos_referencia = $poliza_relacionada->movimientos()->orderBy("NumMovto")->get();
        if (count($movimientos) == count($movimientos_referencia)) {
            $i = 0;
            foreach ($movimientos as $movimiento) {
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $busqueda->base_datos_busqueda);
                $movimiento->load("cuenta");
                //$movimiento->load("poliza");

                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $busqueda->base_datos_referencia);
                $movimientos_referencia[$i]->load("cuenta");
                //$movimientos_referencia[$i]->load("poliza");
                try {

                    $datos_relacion = [
                        "id_movimiento_a" => $movimiento->Id,
                        "base_datos_a" => $busqueda->base_datos_busqueda,
                        "id_movimiento_b" => $movimientos_referencia[$i]->Id,
                        "base_datos_b" => $busqueda->base_datos_referencia,
                        "tipo_relacion" => $busqueda->id_tipo_busqueda,
                        "num_movto_a" => $movimiento->NumMovto,
                        "num_movto_b" => $movimientos_referencia[$i]->NumMovto,
                        "tipo_movto_a" => $movimiento->TipoMovto,
                        "tipo_movto_b" => $movimientos_referencia[$i]->TipoMovto,
                        "codigo_cuenta_a" => $movimiento->cuenta->Codigo,
                        "codigo_cuenta_b" => $movimientos_referencia[$i]->cuenta->Codigo,
                        "nombre_cuenta_a" => $movimiento->cuenta->Nombre,
                        "nombre_cuenta_b" => $movimientos_referencia[$i]->cuenta->Nombre,
                        "importe_a" => $movimiento->Importe,
                        "importe_b" => $movimientos_referencia[$i]->Importe,
                        "referencia_a" => $movimiento->Referencia,
                        "referencia_b" => $movimientos_referencia[$i]->Referencia,
                        "concepto_a" => $movimiento->Concepto,
                        "concepto_b" => $movimientos_referencia[$i]->Concepto,
                        "id_poliza_a" => $movimiento->IdPoliza,
                        "id_poliza_b" => $movimientos_referencia[$i]->IdPoliza,
                        "id_cuenta_a" => $movimiento->IdCuenta,
                        "id_cuenta_b" => $movimientos_referencia[$i]->IdCuenta,
                    ];
                } catch (\Exception $e) {
                    // dd($busqueda->base_datos_busqueda,$movimiento);
                }
                $relaciones_movimientos[$i] = RelacionMovimientos::registrar($datos_relacion);
                $i++;
            }
        }
        return $relaciones_movimientos;
    }

    public function sumaMismoPadreCargos($cuenta)
    {
        $suma = 0;
        foreach ($this->movimientos()->where("TipoMovto", "=", 0)->orderBy('IdCuenta')->get() as $movimiento) {
            if ($movimiento->cuenta->cuenta_mayor->Codigo == $cuenta->Codigo) {
                $suma = $suma + $movimiento->Importe;
            }
        }
        return $suma;
    }

    public function sumaMismoPadreAbonos($cuenta)
    {
        $suma = 0;
        foreach ($this->movimientos()->where("TipoMovto", "=", 1)->orderBy('IdCuenta')->get() as $movimiento) {
            if ($movimiento->cuenta->cuenta_mayor->Codigo == $cuenta->Codigo) {
                $suma = $suma + $movimiento->Importe;
            }
        }
        return $suma;
    }

    public function getConceptoPropuesta(SolicitudEdicion $solicitud_edicion)
    {
        $diferencias = array_values($solicitud_edicion->diferencias->where("id_tipo", "=", "2")->where("id_poliza", "=", $this->Id)->toArray());
        if (count($diferencias) > 0) {
            return $diferencias[0]["valor_b"];

        } else {
            return $this->Concepto;
        }
    }

    public function getConceptoOriginalT2(SolicitudEdicion $solicitud_edicion)
    {
        $diferencias = array_values($solicitud_edicion->diferencias->where("id_tipo", "=", "2")->where("id_poliza", "=", $this->Id)->toArray());
        if (count($diferencias) > 0) {
            return $diferencias[0]["valor_a"];

        } else {
            return $this->Concepto;
        }
    }

    public function getCuentasPadresAttribute()
    {
        $cuentas_padres = [];
        foreach ($this->cuentas()->orderBy("NumMovto")->get() as $cuenta) {
            $cuentas_padres [] = $cuenta->cuenta_mayor;
        }
        return array_unique($cuentas_padres);
    }

    public function getCuentasPadresReordenadas(SolicitudEdicion $solicitud_edicion)
    {
        $parametro = Parametro::find(1);
        $cuentas_padres = [];
        $diferencias = array_values($solicitud_edicion->diferencias->where("id_tipo", "=", "12")->where("id_poliza", "=", $this->Id)->toArray());

        if (count($diferencias) > 0) {
            $relacion = RelacionPolizas::where("id_poliza_a", "=", $this->Id)
                ->where("tipo_relacion", "=", $diferencias[0]["tipo_busqueda"])
                ->where("base_datos_a", Config::get('database.connections.cntpq.database'))->first();
            $poliza_relacion = $this->getPolizaReferencia($relacion);
            foreach ($poliza_relacion->movimientos as $movimiento_relacionado) {
                $cuenta = Cuenta::where("Codigo", $movimiento_relacionado->cuenta->getCodigoLongitud($parametro->longitud_cuenta))->first();
                try {
                    $movimiento = $this->movimientos()->where("Importe", $movimiento_relacionado->Importe)
                        ->where("TipoMovto", $movimiento_relacionado->TipoMovto)
                        ->where("IdCuenta", $cuenta->Id)
                        ->first();
                    if ($movimiento) {
                        $cuentas_padres [] = $movimiento->cuenta->cuenta_mayor;
                    } else {
                        $solicitud_edicion->partidas->where("id_diferencia", "=", $diferencias[0]["id"])->first()->cancelaPartidaSolicitudReordenamientoImprocedente();
                    }
                } catch (\Exception $e) {
                }
            }
            return array_unique($cuentas_padres);
        } else {
            return $this->cuentas_padres;
        }
    }

    public function getMovimientosReordenados(Cuenta $cuenta_padre, SolicitudEdicion $solicitud_edicion)
    {
        $parametro = Parametro::find(1);
        $cuentas_padres = [];
        $diferencias = array_values($solicitud_edicion->diferencias->where("id_tipo", "=", "12")->where("id_poliza", "=", $this->Id)->toArray());
        $relacion = RelacionPolizas::where("id_poliza_a", "=", $this->Id)
            ->where("tipo_relacion", "=", $diferencias[0]["tipo_busqueda"])
            ->where("base_datos_a", Config::get('database.connections.cntpq.database'))->first();
        $poliza_relacion = $this->getPolizaReferencia($relacion);
        $movimientos_cuenta_padre = [];
        foreach ($poliza_relacion->movimientos as $movimiento_relacionado) {
            $cuenta = Cuenta::where("Codigo", $movimiento_relacionado->cuenta->getCodigoLongitud($parametro->longitud_cuenta))->first();
            try {
                $movimiento = $this->movimientos()->where("Importe", $movimiento_relacionado->Importe)
                    ->where("TipoMovto", $movimiento_relacionado->TipoMovto)
                    ->where("IdCuenta", $cuenta->Id)
                    ->first();
                if ($movimiento) {
                    if ($movimiento->cuenta->cuenta_mayor->Codigo == $cuenta_padre->Codigo) {
                        $movimientos_cuenta_padre[] = $movimiento;
                    }
                } else {
                    $solicitud_edicion->partidas->where("id_diferencia", "=", $diferencias[0]["id"])->first()->cancelaPartidaSolicitudReordenamientoImprocedente();
                }
            } catch (\Exception $e) {
            }
        }
        return $movimientos_cuenta_padre;
    }

    public function getPrimerMovimiento(Cuenta $cuenta_padre)
    {
        $movimientos = $this->movimientos()->orderBy('NumMovto', 'asc')->get();
        $primer_movimiento = "";
        foreach ($movimientos as $movimiento) {
            if ($movimiento->cuenta->cuenta_mayor->Codigo == $cuenta_padre->Codigo) {
                $primer_movimiento = $movimiento;
                break;
            }
        }
        return $primer_movimiento;
    }

    public function getPrimerMovimientoReordenado(Cuenta $cuenta_padre, SolicitudEdicion $solicitud_edicion)
    {
        $movimientos = $this->getMovimientosReordenados($cuenta_padre, $solicitud_edicion);
        $primer_movimiento = "";
        foreach ($movimientos as $movimiento) {
            if ($movimiento->cuenta->cuenta_mayor->Codigo == $cuenta_padre->Codigo) {
                $primer_movimiento = $movimiento;
                break;
            }
        }
        return $primer_movimiento;
    }

    public function getMovimientos(Cuenta $cuenta_padre)
    {
        $movimientos = $this->movimientos()->orderBy('NumMovto', 'asc')->get();
        $movimientos_cuenta_padre = [];
        foreach ($movimientos as $movimiento) {
            if ($movimiento->cuenta->cuenta_mayor->Codigo == $cuenta_padre->Codigo) {
                $movimientos_cuenta_padre[] = $movimiento;
            }
        }
        return $movimientos_cuenta_padre;
    }

    public function getCuentasPadresOrdenOriginal(SolicitudEdicion $solicitud_edicion)
    {
        $parametro = Parametro::find(1);
        $cuentas_padres = [];
        $diferencia = $solicitud_edicion->diferencias->where("id_tipo", "=", "12")->where("id_poliza", "=", $this->Id)->first();
        $partida_solicitud = $diferencia->partida_solicitud;
        $movimientos_ordenados = [];

        if ($diferencia) {

            foreach ($this->movimientos as $movimiento) {
                $log = LogEdicion::where("id_solicitud_partida", "=", $partida_solicitud->id)
                    ->where("id_movimiento", $movimiento->Id)
                    ->first();
                if ($log) {
                    $movimientos_ordenados[$log->valor_original] = $movimiento;
                } else {
                    return $this->cuentas_padres;
                }

            }
            ksort($movimientos_ordenados);
            foreach ($movimientos_ordenados as $movimiento) {
                $cuentas_padres [] = $movimiento->cuenta->cuenta_mayor;
            }
            return array_unique($cuentas_padres);
        } else {
            return $this->cuentas_padres;
        }
    }

    public function getMovimientosOrdenOriginal(Cuenta $cuenta_padre, SolicitudEdicion $solicitud_edicion)
    {
        $parametro = Parametro::find(1);
        $cuentas_padres = [];
        $diferencia = $solicitud_edicion->diferencias->where("id_tipo", "=", "12")->where("id_poliza", "=", $this->Id)->first();
        $partida_solicitud = $diferencia->partida_solicitud;
        $movimientos_ordenados = [];

        if ($diferencia) {

            foreach ($this->movimientos as $movimiento) {
                $log = LogEdicion::where("id_solicitud_partida", "=", $partida_solicitud->id)
                    ->where("id_movimiento", $movimiento->Id)
                    ->first();
                if ($log) {
                    $movimientos_ordenados[$log->valor_original] = $movimiento;
                } else {
                    return $this->getMovimientos($cuenta_padre);
                }

            }
            ksort($movimientos_ordenados);
            foreach ($movimientos_ordenados as $movimiento) {
                if ($movimiento->cuenta->cuenta_mayor->Codigo == $cuenta_padre->Codigo) {
                    $movimientos_cuenta_padre[] = $movimiento;
                }
            }
            return $movimientos_cuenta_padre;
        } else {
            return $this->getMovimientos($cuenta_padre);
        }
    }

    public function getTipoAttribute()
    {
        if ($this->tipo_poliza) {
            return $this->tipo_poliza->Nombre;
        } else {
            return "";
        }
    }

    public function createLog($campo, $valor_original, $valor_modificado)
    {
        LogEdicion::create([
            'id_poliza' => $this->Id,
            'id_campo' => $campo,
            'valor_original' => $valor_original,
            'valor_modificado' => $valor_modificado
        ]);
    }

    public function validarReglas()
    {
        if(self::where('Ejercicio', $this->Ejercicio)->where('Periodo', $this->Periodo)->where('TipoPol', $this->TipoPol)->where('Folio', $this->Folio)->where('Id', '!=', $this->Id)->first())
        {
            abort(500,"No se puede realizar el cambio, existe una Póliza en el mismo ejercicio y periodo");
        }
    }

    public function getReferencia()
    {
        $empresa = Config::get('database.connections.cntpq.database');
        $referencia = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Referencia><Documento cadReferencia="Póliza de '.$this->tipo_poliza->Nombre.', ejercicio: '.$this->Ejercicio.', periodo: '.$this->Periodo.', número: '.$this->Folio.', empresa: '.$empresa.', guid: '.$this->Guid.'." edoPago="0" tipo="Poliza"/></Referencia>';
        return $referencia;
    }

    public function generaAsociacionCFDI(Comprobante $comprobante)
    {
        $previa = AsocCFDI::where("UUID","=",$comprobante->UUID)
            ->where("GuidRef","=",$this->Guid)->first();
        if($previa){
            return $previa;
        }
        return $this->asociacionCFDI()->create([
            'UUID'=>$comprobante->UUID,
            'Referencia'=>$this->getReferencia(),
            'AppType'=>"Contabilidad",
            'Reconstruir'=>true,
            "Id"=>AsocCFDI::getUltimoFolio()
        ]);
    }
}
