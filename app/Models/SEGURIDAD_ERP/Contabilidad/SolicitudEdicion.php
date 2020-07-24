<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\CtgTipoSolicitudEdicion;
use App\Models\CADECO\FinanzasCBE\Solicitud;
use App\Models\CTPQ\Cuenta;
use App\Models\CTPQ\Poliza;
use App\Models\CTPQ\PolizaMovimiento;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionMovimientos;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Utils\BusquedaDiferenciasMovimientos;

class SolicitudEdicion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitudes_edicion';
    public $timestamps = false;
    protected $fillable =[
        "numero_folio",
        "id_lote_busqueda",
        "id_tipo",
        "base_datos"
    ];

    public function tipo()
    {
        return $this->belongsTo(CtgTipoSolicitudEdicion::class, "id_tipo", "id");
    }

    public function empresa_ctpq()
    {
        return $this->belongsTo(Empresa::class, "base_datos", "AliasBDD");
    }

    public function partidas()
    {
        return $this->hasMany(SolicitudEdicionPartida::class,"id_solicitud_edicion","id");
    }
    public function partidasActivas()
    {
        return $this->hasMany(SolicitudEdicionPartida::class,"id_solicitud_edicion","id")->activas();
    }

    public function polizas()
    {
        if($this->id_tipo == 1){
            return $this->hasManyThrough(SolicitudEdicionPartidaPoliza::class,SolicitudEdicionPartida::class,"id_solicitud_edicion","id_solicitud_partida","id","id");
        } else {
            return $this->hasManyThrough(Diferencia::class,SolicitudEdicionPartida::class,"id_solicitud_edicion","id","id","id_diferencia");
        }
    }

    public function diferencias()
    {
        return $this->hasManyThrough(Diferencia::class,SolicitudEdicionPartida::class,"id_solicitud_edicion","id","id","id_diferencia");
    }

    public function polizasAutorizadas()
    {
        return $this->hasManyThrough(SolicitudEdicionPartidaPoliza::class,SolicitudEdicionPartida::class,"id_solicitud_edicion","id_solicitud_partida","id","id")->autorizadas();
    }

    public function usuario_registro(){
        return $this->belongsTo(Usuario::class, 'id_usuario_registro', 'idusuario');
    }

    public function usuario_autorizo(){
        return $this->belongsTo(Usuario::class, 'id_usuario_autorizo', 'idusuario');
    }

    public function usuario_rechazo(){
        return $this->belongsTo(Usuario::class, 'id_usuario_rechazo', 'idusuario');
    }

    public function lote_busqueda(){
        return $this->belongsTo(LoteBusqueda::class, 'id_lote_busqueda', 'id');
    }

    public function usuario_aplico(){
        return $this->belongsTo(Usuario::class, 'id_usuario_aplico', 'idusuario');
    }

    public function getNumeroMovimientosAttribute()
    {
        if($this->id_tipo == 1)
        {
            $no_movimientos = 0;
            $polizas = $this->polizas;
            if($polizas){
                foreach($polizas as $poliza){
                    $no_movimientos+= $poliza->movimientos()->count();
                }
            }
            return $no_movimientos;
        } else if($this->id_tipo == 2)
        {
            $ids_movimientos = [];
            $diferencias = $this->diferencias;
            foreach($diferencias as $diferencia){
                if($diferencia->id_movimiento>0){
                    $ids_movimientos[] = $diferencia->id_movimiento;
                }
            }
            $ids_movimientos_unicos = array_unique($ids_movimientos);
            return count($ids_movimientos_unicos);
        } else if($this->id_tipo == 3)
        {
            $no_movimientos = 0;
            $diferencias = $this->diferencias;
            foreach($diferencias as $diferencia){
                $no_movimientos += $diferencia->poliza->movimientos()->count();
            }
            return $no_movimientos;
        } else {
            return '-';
        }
    }

    public function getNumeroMovimientosFormatAttribute()
    {
        if(is_numeric($this->numero_movimientos)){
            return number_format($this->numero_movimientos,0,"",",");
        }
        else {
            return $this->numero_movimientos;
        }
    }

    public function getNumeroPolizasAttribute()
    {
        if($this->id_tipo == 1)
        {
            return $this->polizas()->count();
        } else if($this->id_tipo == 2 || $this->id_tipo == 3)
        {
            $ids_polizas = [];
            $diferencias = $this->diferencias;
            foreach($diferencias as $diferencia){
                $ids_polizas[] = $diferencia->id_poliza;
            }
            $ids_polizas_unicos = array_unique($ids_polizas);
            return count($ids_polizas_unicos);
        } else {
            return '-';
        }
    }

    public function getNumeroPolizasFormatAttribute()
    {
        if(is_numeric($this->numero_polizas)){
            return number_format($this->numero_polizas,0,"",",");
        }
        else {
            return $this->numero_polizas;
        }
    }

    public function getNumeroCuentasAttribute()
    {
        if($this->id_tipo == 4)
        {
            return $this->diferencias()->count();
        } else {
            return '-';
        }
    }

    public function getNumeroCuentasFormatAttribute()
    {
        if(is_numeric($this->numero_cuentas)){
            return number_format($this->numero_cuentas,0,"",",");
        }
        else {
            return $this->numero_cuentas;
        }
    }

    public function getNumeroBDAttribute()
    {
        if($this->id_tipo == 1){
            $bd = [];
            $polizas = $this->polizas;
            if($polizas){
                foreach($polizas as $poliza){
                    $bd[]= $poliza->bd_contpaq;
                }
            }
            $no_bd = count(array_unique($bd));
        } else {
            $no_bd = 1;
        }

        return $no_bd;
    }

    public static function getFolio()
    {
        $solicitud = SolicitudEdicion::orderBy('numero_folio', 'DESC')->first();
        return $solicitud ? $solicitud->numero_folio + 1 : 1;
    }

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->numero_folio);
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estado){
            case 0 :
                return 'Registrada';
                break;
            case 1 :
                return 'Autorizada';
                break;
            case 2 :
                return 'Aplicada';
                break;
            case -1 :
                return 'Rechazada';
                break;
        }
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function getFechaHoraAutorizacionFormatAttribute()
    {
        $date = date_create($this->fecha_hora_autorizacion);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function getFechaHoraRechazoFormatAttribute()
    {
        $date = date_create($this->fecha_hora_rechazo);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function getFechaHoraAplicacionFormatAttribute()
    {
        $date = date_create($this->fecha_hora_aplicacion);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function registrar($datos)
    {
        try {
            DB::connection('seguridad')->beginTransaction();
            $solicitud = $this->create(["numero_folio"=>SolicitudEdicion::getFolio()]);
            foreach($datos["solicitud_partidas"] as $partida){
                $partida_obj = $solicitud->partidas()->create($partida);
                foreach ($partida["polizas"] as $poliza){
                    $poliza_obj = $partida_obj->polizas()->create($poliza);
                    foreach ($poliza["movimientos"] as $movimiento){
                        $poliza_obj->movimientos()->create($movimiento);
                    }
                }
            }
            DB::connection('seguridad')->commit();
            return $solicitud;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function autorizarPorPolizas($polizas)
    {
        if($this->estado == 0 && $this->id_tipo == 1){
            try {

                DB::connection('seguridad')->beginTransaction();
                $this->estado = 1;
                $this->save();
                foreach ($polizas as $poliza){
                    $poliza_obj = SolicitudEdicionPartidaPoliza::find($poliza["id"]);
                    $poliza_obj->estado = $poliza["estado"];
                    $poliza_obj->save();
                }
                DB::connection('seguridad')->commit();
                return $this;
            } catch (\Exception $e) {
                DB::connection('seguridad')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        } else {
            abort(500, "Estado de la solicitud es incorrecto, no se puede autorizar.");
            return $this;
        }
    }

    public function autorizarPorPartidas($partidas)
    {
        if($this->estado == 0 && $this->id_tipo != 1){
            try {

                DB::connection('seguridad')->beginTransaction();
                $this->estado = 1;
                $this->save();
                foreach ($partidas as $partida){
                    $partida_obj = SolicitudEdicionPartida::find($partida["id"]);
                    $partida_obj->estado = $partida["estado"];
                    $partida_obj->save();
                }
                DB::connection('seguridad')->commit();
                return $this;
            } catch (\Exception $e) {
                DB::connection('seguridad')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        } else {
            abort(500, "Estado de la solicitud es incorrecto, no se puede autorizar.");
            return $this;
        }

    }

    public function rechazar()
    {
        if($this->id_tipo == 1){
            $this->rechazarPorPolizas();
        } else {
            $this->rechazarPorPartidas();
        }
    }

    private function rechazarPorPolizas()
    {
        if($this->estado == 0){
            try {
                DB::connection('seguridad')->beginTransaction();
                $this->estado = -1;
                $this->save();
                $polizas = $this->polizas;
                foreach ($polizas as $poliza_obj){
                    $poliza_obj->estado = 0;
                    $poliza_obj->save();
                }
                DB::connection('seguridad')->commit();
                return $this;
            } catch (\Exception $e) {
                DB::connection('seguridad')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        } else {
            abort(500, "Estado de la solicitud es incorrecto, no se puede autorizar.");
            return $this;
        }
    }

    private function rechazarPorPartidas()
    {
        if($this->estado == 0){
            try {
                DB::connection('seguridad')->beginTransaction();
                $this->estado = -1;
                $this->save();
                $partidas = $this->partidas;
                foreach ($partidas as $partida_obj){
                    $partida_obj->estado = 0;
                    $partida_obj->save();
                }
                DB::connection('seguridad')->commit();
                return $this;
            } catch (\Exception $e) {
                DB::connection('seguridad')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        } else {
            abort(500, "Estado de la solicitud es incorrecto, no se puede autorizar.");
            return $this;
        }
    }

    public function aplicar()
    {
        switch ($this->id_tipo){
            case 1 :
                $this->aplicaEdicionMasivaConceptosReferencias();
                break;
            case 2 :
                $this->aplicaEdicionConceptosReferencias();
                break;
            case 3 :
                $this->aplicaReordenamientoMovimientos();
                break;
            case 4 :
                $this->aplicaEdicionNombresCuentas();
                break;
        }
    }

    private function aplicaEdicionMasivaConceptosReferencias()
    {
        if($this->estado == 1 && $this->id_tipo == 1){
            try {
                DB::connection('seguridad')->beginTransaction();
                $this->estado = 2;
                $this->save();
                $polizas = $this->polizasAutorizadas;
                foreach ($polizas as $poliza_obj){
                    DB::purge('cntpq');
                    \Config::set('database.connections.cntpq.database', $poliza_obj->bd_contpaq);
                    DB::connection('cntpq')->beginTransaction();
                    try {
                        $poliza_contpaq = Poliza::find($poliza_obj->id_poliza);
                        if ($poliza_obj->partida_solicitud->concepto != "" && $poliza_contpaq->Concepto == $poliza_obj->concepto_original) {
                            $poliza_contpaq->Concepto = $poliza_obj->partida_solicitud->concepto;
                            $poliza_contpaq->save();
                            $log_pol = $poliza_contpaq->logs()->orderBy("id", "desc")->first();
                            if ($log_pol) {
                                $log_pol->id_solicitud_partida = $poliza_obj->id;
                                $log_pol->save();
                            }
                        }
                        foreach ($poliza_obj->movimientos as $movimiento_obj) {
                            $movimiento_contpaq = PolizaMovimiento::find($movimiento_obj->id_movimiento);

                            if ($poliza_obj->partida_solicitud->concepto != "" && $movimiento_contpaq->Concepto == $movimiento_obj->concepto_original) {
                                $movimiento_contpaq->Concepto = $poliza_obj->partida_solicitud->concepto;
                            }

                            if ($poliza_obj->partida_solicitud->referencia != "" && $movimiento_contpaq->Referencia == $movimiento_obj->referencia_original) {
                                $movimiento_contpaq->Referencia = $poliza_obj->partida_solicitud->referencia;
                            }
                            $movimiento_contpaq->save();
                            $log = $movimiento_contpaq->logs()->orderBy("id", "desc")->first();
                            if ($log) {
                                $log->id_solicitud_partida = $poliza_obj->id;
                                $log->save();
                            }
                        }
                        DB::connection('cntpq')->commit();
                    } catch (\Exception $e) {
                        DB::connection('cntpq')->rollBack();
                        DB::connection('seguridad')->rollBack();
                        abort(400, $e->getMessage());
                        throw $e;
                    }

                }

                DB::connection('seguridad')->commit();
                return $this;
            } catch (\Exception $e) {
                DB::connection('seguridad')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        } else {
            abort(500, "Estado de la solicitud es incorrecto, no se puede aplicar.");
            return $this;
        }

    }

    private function aplicaEdicionConceptosReferencias()
    {
        if($this->estado == 1 && $this->id_tipo == 2){
            try {
                DB::connection('seguridad')->beginTransaction();
                $this->estado = 2;
                $this->save();
                $partidas = $this->partidasActivas;
                $cantidad_afectaciones_esperadas = count($partidas);
                $cantidad_afectaciones_aplicadas = 0;
                foreach ($partidas as $partida){
                    DB::purge('cntpq');
                    \Config::set('database.connections.cntpq.database', $partida->diferencia->base_datos_revisada);
                    if ($partida->diferencia->id_tipo == 2) {
                        $poliza_contpaq = Poliza::find($partida->diferencia->id_poliza);
                        $poliza_contpaq->Concepto = $partida->diferencia->valor_b;
                        $poliza_contpaq->save();
                    } else if ($partida->diferencia->id_tipo == 8) {
                        $movimiento_contpaq = PolizaMovimiento::find($partida->diferencia->id_movimiento);
                        $movimiento_contpaq->Referencia = $partida->diferencia->valor_b;
                        $movimiento_contpaq->save();
                    } else if ($partida->diferencia->id_tipo == 9) {
                        $movimiento_contpaq = PolizaMovimiento::find($partida->diferencia->id_movimiento);
                        $movimiento_contpaq->Concepto = $partida->diferencia->valor_b;
                        $movimiento_contpaq->save();
                    }
                    $log = $movimiento_contpaq->logs()->orderBy("id","desc")->first();
                    $log->id_solicitud_partida = $partida->id;
                    $log->save();
                    $partida->estado = 2;
                    $partida->save();
                    $partida->diferencia->activo = 0;
                    $partida->diferencia->fecha_hora_resolucion =  date('Y-m-d H:i:s');
                    $partida->diferencia->save();
                    $cantidad_afectaciones_aplicadas++;
                }
                if($cantidad_afectaciones_aplicadas == $cantidad_afectaciones_esperadas){
                    DB::connection('seguridad')->commit();
                } else {
                    DB::connection('seguridad')->rollBack();
                }

                return $this;
            } catch (\Exception $e) {
                DB::connection('seguridad')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        } else {
            abort(500, "Estado de la solicitud es incorrecto, no se puede aplicar.");
            return $this;
        }

    }

    private function aplicaReordenamientoMovimientos()
    {
        if($this->estado == 1 && $this->id_tipo == 3){
            try {
                DB::connection('seguridad')->beginTransaction();
                $this->estado = 2;
                $this->save();
                $partidas = $this->partidasActivas;
                $cantidad_afectaciones_esperadas = count($partidas);
                $cantidad_afectaciones_aplicadas = 0;
                foreach ($partidas as $partida){
                    $partida_improcedente  = false;

                    $relacion_movimientos = RelacionMovimientos::where("id_poliza_a","=",$partida->diferencia->id_poliza)
                    ->where("base_datos_a","=", $partida->diferencia->base_datos_revisada)->get();

                    $arreglo_a = [];
                    $arreglo_b = [];
                    $hashs_a = [];
                    $hashs_b = [];

                    $i = 0;

                    foreach($relacion_movimientos as $relacion_movimiento){
                        $codigos = $this->igualaLongitudCodigos($relacion_movimiento->codigo_cuenta_a, $relacion_movimiento->codigo_cuenta_b);
                        $hash_a = md5($codigos["codigo_a"] . $relacion_movimiento->tipo_movto_a . $relacion_movimiento->importe_a);
                        $hash_b = md5($codigos["codigo_b"] . $relacion_movimiento->tipo_movto_b . $relacion_movimiento->importe_b);
                        $hashs_a[$i] = $hash_a;
                        $hashs_b[$i] = $hash_b;

                        try{
                            $arreglo_a[$hash_a]["id_movimiento"] = $relacion_movimiento->id_movimiento_a;
                            $arreglo_a[$hash_a]["num_movto"] = $relacion_movimiento->num_movto_a;
                            $arreglo_a[$hash_a]["tipo_movto"] = $relacion_movimiento->tipo_movto_a;
                            $arreglo_a[$hash_a]["codigo_cuenta"] = $relacion_movimiento->codigo_cuenta_a;
                            $arreglo_a[$hash_a]["nombre_cuenta"] = $relacion_movimiento->nombre_cuenta_a;
                            $arreglo_a[$hash_a]["importe"] = $relacion_movimiento->importe_a;
                            $arreglo_a[$hash_a]["referencia"] = $relacion_movimiento->referencia_a;
                            $arreglo_a[$hash_a]["concepto"] = $relacion_movimiento->concepto_a;
                            $arreglo_a[$hash_a]["id_cuenta"] = $relacion_movimiento->id_cuenta_a;
                            $arreglo_a[$hash_a]["base_datos"] = $relacion_movimiento->base_datos_a;
                            $id_poliza_a = $relacion_movimiento->id_poliza_a;

                            $arreglo_b[$hash_b]["id_movimiento"] = $relacion_movimiento->id_movimiento_b;
                            $arreglo_b[$hash_b]["num_movto"] = $relacion_movimiento->num_movto_b;
                            $arreglo_b[$hash_b]["tipo_movto"] = $relacion_movimiento->tipo_movto_b;
                            $arreglo_b[$hash_b]["codigo_cuenta"] = $relacion_movimiento->codigo_cuenta_b;
                            $arreglo_b[$hash_b]["nombre_cuenta"] = $relacion_movimiento->nombre_cuenta_b;
                            $arreglo_b[$hash_b]["importe"] = $relacion_movimiento->importe_b;
                            $arreglo_b[$hash_b]["referencia"] = $relacion_movimiento->referencia_b;
                            $arreglo_b[$hash_b]["concepto"] = $relacion_movimiento->concepto_b;
                            $arreglo_b[$hash_b]["id_cuenta"] = $relacion_movimiento->id_cuenta_b;
                            $arreglo_b[$hash_a]["base_datos"] = $relacion_movimiento->base_datos_b;
                            $id_poliza_b = $relacion_movimiento->id_poliza_b;

                        }catch(\Exception $e){
                            abort(500, $e->getMessage());
                        }
                        $i++;
                    }

                    DB::purge('cntpq');
                    \Config::set('database.connections.cntpq.database', $arreglo_a[$hashs_a[0]]["base_datos"]);
                    $no_movtos_a = Poliza::find($id_poliza_a)->movimientos->max("NumMovto");

                    DB::purge('cntpq');
                    \Config::set('database.connections.cntpq.database', $arreglo_b[$hashs_b[0]]["base_datos"]);
                    $no_movtos_b = Poliza::find($id_poliza_b)->movimientos->max("NumMovto");
                    $error_edicion_movimientos = 0;
                    if($no_movtos_a == $no_movtos_b){
                        DB::purge('cntpq');
                        \Config::set('database.connections.cntpq.database', $arreglo_a[$hashs_a[0]]["base_datos"]);

                        DB::connection('cntpq')->beginTransaction();
                        foreach($arreglo_a as $hash=>$arreglo){
                            $movimiento_contpaq = PolizaMovimiento::find($arreglo["id_movimiento"]);
                            $movimiento_contpaq->NumMovto=$movimiento_contpaq->NumMovto*(-1);
                            $movimiento_contpaq->save();
                        }
                        $r = 0;
                        foreach($hashs_b as $k=>$hash_b){
                            try{
                                $movimiento_contpaq = PolizaMovimiento::find($arreglo_a[$hash_b]["id_movimiento"]);
                                $movimiento_contpaq->NumMovto=$arreglo_b[$hash_b]["num_movto"];
                                $movimiento_contpaq->save();
                            }catch(\Exception $e)
                            {
                                $error_edicion_movimientos++;
                            }
                            $log = $movimiento_contpaq->logs()->orderBy("id","desc")->first();
                            $log->id_solicitud_partida = $partida->id;
                            $log->save();
                            try{
                                $relacion_movimientos[$r]->id_movimiento_a = $arreglo_a[$hash_b]["id_movimiento"];
                                $relacion_movimientos[$r]->fecha_hora_asociacion = date('Y-m-d H:i:s');
                                $relacion_movimientos[$r]->tipo_movto_a = $arreglo_a[$hash_b]["tipo_movto"];
                                $relacion_movimientos[$r]->codigo_cuenta_a = $arreglo_a[$hash_b]["codigo_cuenta"];
                                $relacion_movimientos[$r]->nombre_cuenta_a = $arreglo_a[$hash_b]["nombre_cuenta"];
                                $relacion_movimientos[$r]->importe_a = $arreglo_a[$hash_b]["importe"];
                                $relacion_movimientos[$r]->referencia_a = $arreglo_a[$hash_b]["referencia"];
                                $relacion_movimientos[$r]->concepto_a = $arreglo_a[$hash_b]["concepto"];
                                $relacion_movimientos[$r]->id_cuenta_a = $arreglo_a[$hash_b]["id_cuenta"];
                                $relacion_movimientos[$r]->save();
                            }catch(\Exception $e)
                            {
                                abort(500, $e->getMessage());
                            }
                            $r ++;
                        }
                        if($error_edicion_movimientos==0)
                        {
                            DB::connection('cntpq')->commit();
                        } else {
                            DB::connection('cntpq')->rollBack();
                            $partida_improcedente  = true;
                        }

                    } else {
                        DB::connection('seguridad')->rollBack();
                        abort(500, "El número de movimientos no coincide");
                        return $this;
                    }

                    if($partida_improcedente)
                    {
                        $partida->cancelaPartidaSolicitudReordenamientoImprocedente();
                    } else {
                        $partida->estado = 2;
                        $partida->save();

                        $partida->diferencia->activo = 0;
                        $partida->diferencia->fecha_hora_resolucion =  date('Y-m-d H:i:s');
                        $partida->diferencia->save();
                    }

                    $cantidad_afectaciones_aplicadas++;
                }
                if($cantidad_afectaciones_aplicadas == $cantidad_afectaciones_esperadas){
                    DB::connection('seguridad')->commit();
                } else {
                    DB::connection('seguridad')->rollBack();
                }

                return $this;
            } catch (\Exception $e) {
                DB::connection('seguridad')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        } else {
            abort(500, "Estado de la solicitud es incorrecto, no se puede aplicar.");
            return $this;
        }

    }

    private function aplicaEdicionNombresCuentas()
    {
        if($this->estado == 1 && $this->id_tipo == 4){
            try {
                DB::connection('seguridad')->beginTransaction();
                $this->estado = 2;
                $this->save();
                $partidas = $this->partidasActivas;
                $cantidad_afectaciones_esperadas = count($partidas);
                $cantidad_afectaciones_aplicadas = 0;
                foreach ($partidas as $partida){
                    DB::purge('cntpq');
                    \Config::set('database.connections.cntpq.database', $partida->diferencia->base_datos_revisada);
                    DB::connection('cntpq')->beginTransaction();

                    $cuenta_contpaq = Cuenta::find($partida->diferencia->id_cuenta);
                    $cuenta_contpaq->Nombre = $partida->diferencia->valor_b;
                    $cuenta_contpaq->save();

                    $log = $cuenta_contpaq->logs()->orderBy("id","desc")->first();
                    $log->id_solicitud_partida = $partida->id;
                    $log->save();

                    $partida->estado = 2;
                    $partida->save();

                    $partida->diferencia->activo = 0;
                    $partida->diferencia->fecha_hora_resolucion =  date('Y-m-d H:i:s');
                    $partida->diferencia->save();
                    DB::connection('cntpq')->commit();

                    $cantidad_afectaciones_aplicadas++;
                }
                if($cantidad_afectaciones_aplicadas == $cantidad_afectaciones_esperadas){
                    DB::connection('seguridad')->commit();
                } else {
                    DB::connection('seguridad')->rollBack();
                }

                return $this;
            } catch (\Exception $e) {
                DB::connection('seguridad')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        } else {
            abort(500, "Estado de la solicitud es incorrecto, no se puede aplicar.");
            return $this;
        }
    }

    public function getPolizasSolicitud(){
        $salida = [];
        $polizas = $this->polizas;
        $i=0;
        foreach ($polizas as $poliza){
            DB::purge('cntpq');
            \Config::set('database.connections.cntpq.database', $poliza->bd_contpaq);
            $poliza_contpaq = Poliza::find($poliza->id_poliza);
            $movimientos = $poliza->movimientos;
            foreach ($movimientos as $movimiento) {
                $movimiento_contpaq = PolizaMovimiento::find($movimiento->id_movimiento);
                $salida[$i] =[
                    ($i+1),
                    $poliza->partida_solicitud->fecha_format,
                    $poliza->partida_solicitud->tipo_format,
                    $poliza->partida_solicitud->folio,
                    $poliza->partida_solicitud->concepto,
                    $poliza->partida_solicitud->referencia,
                    $poliza->bd_contpaq,
                    $poliza_contpaq->Id,
                    $poliza_contpaq->fecha_format,
                    $poliza_contpaq->tipo_poliza->Nombre,
                    $poliza_contpaq->Folio,
                    $poliza_contpaq->Concepto,
                    $movimiento_contpaq->Id,
                    "'".$movimiento_contpaq->cuenta->Codigo,
                    $movimiento_contpaq->tipo_format,
                    $movimiento_contpaq->importe_format,
                    $movimiento_contpaq->Referencia,
                    $movimiento_contpaq->Concepto
                ];
                $i++;
            }
        }
        return $salida;
    }

    public function getBaseDatosEmpresaAttribute()
    {
        if($this->base_datos != "")
        {
            if($this->empresa_ctpq)
            {
                return $this->base_datos. " [".$this->empresa_ctpq->Nombre."]";
            } else {
                return $this->base_datos;
            }
        } else {
            return "";
        }
    }

    private function igualaLongitudCodigos($codigo_a, $codigo_b){
        $lcodigo_a = strlen($codigo_a);
        $lcodigo_b = strlen($codigo_b);

        if ($lcodigo_b == 11 && $lcodigo_a == 13) {

            $g1 = substr($codigo_b, 0, 4);
            $g2 = substr($codigo_b, 4, 2);
            $g3 = substr($codigo_b, 6, 2);
            $g4 = substr($codigo_b, 8, 3);

            $codigo_b = $g1 . '0' . $g2 . '0' . $g3 . $g4;
        } else if ($lcodigo_a == 11 && $lcodigo_b == 13) {

            $g1 = substr($codigo_a, 0, 4);
            $g2 = substr($codigo_a, 4, 2);
            $g3 = substr($codigo_a, 6, 2);
            $g4 = substr($codigo_a, 8, 3);

            $codigo_a = $g1 . '0' . $g2 . '0' . $g3 . $g4;
        }
        return ["codigo_a"=>$codigo_a, "codigo_b"=>$codigo_b];
    }
}