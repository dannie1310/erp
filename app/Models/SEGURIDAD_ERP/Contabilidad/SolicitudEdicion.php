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
use App\Models\CTPQ\Poliza;
use App\Models\CTPQ\PolizaMovimiento;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        return $this->hasManyThrough(SolicitudEdicionPartidaPoliza::class,SolicitudEdicionPartida::class,"id_solicitud_edicion","id_solicitud_partida","id","id");
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
        } else if($this->id_tipo == 2 || $this->id_tipo == 3)
        {
            $ids_movimientos = [];
            $diferencias = $this->diferencias;
            foreach($diferencias as $diferencia){
                $ids_movimientos[] = $diferencia->id_movimiento;
            }
            $ids_movimientos_unicos = array_unique($ids_movimientos);
            return count($ids_movimientos_unicos);
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

    public function autorizar($polizas)
    {
        if($this->estado == 0){
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

    public function rechazar()
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

    public function aplicar()
    {
        if($this->estado == 1){
            try {
                DB::connection('seguridad')->beginTransaction();
                $this->estado = 2;
                $this->save();
                $polizas = $this->polizasAutorizadas;
                foreach ($polizas as $poliza_obj){
                    DB::purge('cntpq');
                    \Config::set('database.connections.cntpq.database', $poliza_obj->bd_contpaq);
                    $poliza_contpaq = Poliza::find($poliza_obj->id_poliza);
                    if($poliza_obj->partida_solicitud->concepto != "" && $poliza_contpaq->Concepto == $poliza_obj->concepto_original){
                        $poliza_contpaq->Concepto = $poliza_obj->partida_solicitud->concepto;
                        $poliza_contpaq->save();
                    }
                    foreach($poliza_obj->movimientos as $movimiento_obj){
                        $movimiento_contpaq = PolizaMovimiento::find($movimiento_obj->id_movimiento);

                        if($poliza_obj->partida_solicitud->concepto != "" && $movimiento_contpaq->Concepto == $movimiento_obj->concepto_original){
                            $movimiento_contpaq->Concepto = $poliza_obj->partida_solicitud->concepto;
                        }

                        if($poliza_obj->partida_solicitud->referencia != "" && $movimiento_contpaq->Referencia == $movimiento_obj->referencia_original){
                            $movimiento_contpaq->Referencia = $poliza_obj->partida_solicitud->referencia;
                        }
                        $movimiento_contpaq->save();
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

}