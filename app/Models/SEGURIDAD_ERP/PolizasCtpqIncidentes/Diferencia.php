<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Models\CTPQ\Cuenta;
use App\Models\CTPQ\Poliza;
use App\Models\CTPQ\PolizaMovimiento;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionPartida;

class Diferencia extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PolizasCtpqIncidentes.diferencias';
    public $timestamps = false;
    protected $fillable = [
        "id_poliza",
        "id_movimiento",
        "base_datos_revisada",
        "base_datos_referencia",
        "id_tipo",
        "fecha_hora_deteccion",
        "fecha_hora_resolucion",
        "observaciones",
        "tipo_busqueda",
        "id_busqueda",
        "valor_a",
        "valor_b",
        "id_cuenta",
        "id_relacion_movimiento"
    ];

    public function partida_solicitud()
    {
        return $this->hasOne(SolicitudEdicionPartida::class,"id_diferencia","id");
    }

    public function busqueda()
    {
        return $this->belongsTo(Busqueda::class,"id_busqueda", "id");
    }

    public static function buscarSO($data){
        $diferencia = null;
        if(key_exists("id_movimiento", $data)){
            $diferencia = Diferencia::activos()->where("id_poliza",$data["id_poliza"])
                ->where("id_movimiento",$data["id_movimiento"])
                ->where("base_datos_revisada",$data["base_datos_revisada"])
                ->where("base_datos_referencia",$data["base_datos_referencia"])
                ->where("id_tipo",$data["id_tipo"])
                ->where("tipo_busqueda",$data["tipo_busqueda"])
                ->first();
        } else {
            $diferencia = Diferencia::activos()->where("id_poliza",$data["id_poliza"])
                ->where("base_datos_revisada",$data["base_datos_revisada"])
                ->where("base_datos_referencia",$data["base_datos_referencia"])
                ->where("id_tipo",$data["id_tipo"])
                ->where("tipo_busqueda",$data["tipo_busqueda"])
                ->first();
        }
        return $diferencia;
    }

    public static function buscar($data)
    {
        $diferencia = null;
        if(key_exists("id_movimiento", $data)){
            $diferencia = Diferencia::activos()->where("id_poliza",$data["id_poliza"])
                ->where("id_movimiento",$data["id_movimiento"])
                ->where("base_datos_revisada",$data["base_datos_revisada"])
                ->where("base_datos_referencia",$data["base_datos_referencia"])
                ->where("id_tipo",$data["id_tipo"])
                ->where("tipo_busqueda",$data["tipo_busqueda"])
                ->where("observaciones",$data["observaciones"])
                ->first();
        } else {
            $diferencia = Diferencia::activos()->where("id_poliza",$data["id_poliza"])
                ->where("base_datos_revisada",$data["base_datos_revisada"])
                ->where("base_datos_referencia",$data["base_datos_referencia"])
                ->where("id_tipo",$data["id_tipo"])
                ->where("tipo_busqueda",$data["tipo_busqueda"])
                ->where("observaciones",$data["observaciones"])
                ->first();
        }
        return $diferencia;
    }
    public static function registrar($data){
        //se omite la validación
        try{
            Diferencia::create($data);
        } catch (\Exception $e){

        }

        /*$diferencia = Diferencia::buscar($data);
        if(!$diferencia)
        {
            Diferencia::create($data);
        }*/
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'base_datos_revisada','AliasBDD');
    }

    public function getFechaHoraDeteccionFormatAttribute()
    {
        $date = date_create($this->fecha_hora_deteccion);
        return date_format($date, "d/m/Y H:i:s");
    }

    public function getFechaHoraResolucionFormatAttribute()
    {
        if ($this->fecha_hora_resolucion) {
            $date = date_create($this->fecha_hora_resolucion);
            return date_format($date, "d/m/Y H:i:s");
        } else {
            return null;
        }

    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipo::class, "id_tipo", "id");
    }

    public function correccion(){
        return $this->hasOne(DiferenciaCorregida::class,"id_diferencia", "id");
    }

    public function scopeActivos($query)
    {
        return $query->where("activo",1);
    }

    public function scopeSinPartidaSolicitud($query)
    {
        //return $query->doesntHave("partida_solicitud");
        return $query->whereIn("id_tipo", [1,3,4,5,6,10,11]);
    }

    public function scopeConPartidaSolicitud($query)
    {
        //return $query->whereHas("partida_solicitud");
        return $query->whereIn("id_tipo", [2,7,8,9,12]);
    }

    public function poliza()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos_revisada);
        return $this->belongsTo(Poliza::class, "id_poliza", "Id");
    }

    public function movimiento()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos_revisada);
        return $this->belongsTo(PolizaMovimiento::class, "id_movimiento", "Id");
    }

    public function cuenta()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos_revisada);
        return $this->belongsTo(Cuenta::class, "id_cuenta", "Id");
    }

    public static function aplicarCorreccion($datos_correccion)
    {
        if(key_exists("id_movimiento", $datos_correccion)){
            $diferencia=Diferencia::activos()->where("id_poliza",$datos_correccion["id_poliza"])
                ->where("id_movimiento",$datos_correccion["id_movimiento"])
                ->where("base_datos_revisada",$datos_correccion["base_datos_revisada"])
                ->where("base_datos_referencia",$datos_correccion["base_datos_referencia"])
                ->where("id_tipo",$datos_correccion["id_tipo"])
                ->where("tipo_busqueda",$datos_correccion["tipo_busqueda"])
                ->first();
        } else {
            $diferencia=Diferencia::activos()->where("id_poliza",$datos_correccion["id_poliza"])
                ->where("base_datos_revisada",$datos_correccion["base_datos_revisada"])
                ->where("base_datos_referencia",$datos_correccion["base_datos_referencia"])
                ->where("id_tipo",$datos_correccion["id_tipo"])
                ->where("tipo_busqueda",$datos_correccion["tipo_busqueda"])
                ->first();

        }
        if($diferencia){
            $diferencia->corregir();
        }
    }

    public static function corregir($datos_diferencia,$id_busqueda = 1)
    {
        $datos["activo"] =0;
        $datos["fecha_hora_resolucion"] = date('Y-m-d H:i:s');
        $diferencia = Diferencia::buscarSO($datos_diferencia);
        if($diferencia){
            $diferencia->activo = 0;
            $diferencia->fecha_hora_resolucion = date('Y-m-d H:i:s');
            $diferencia->correccion()->create(["id_busqueda"=>$id_busqueda]);
            $diferencia->save();
        }
    }

    public static function cantidadTotalPolizasConErrores($tipo_busqueda)
    {
        $dem = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw(" count(distinct diferencias.id_poliza) as cantidad_polizas"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
            ->where("activo",1)
            ->where("diferencias.tipo_busqueda",$tipo_busqueda)
            ->first();
        if($dem->cantidad_polizas)
        return $dem->cantidad_polizas;
        else
            return 0;
    }

    public static function totalPorTipoPorEmpresa($tipo_busqueda)
    {
        $dem = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw("count(diferencias.id) as cantidad, count(distinct diferencias.id_poliza) as cantidad_polizas, ctg_tipos.descripcion as descripcion, empresa_revisada.Nombre +' ['+diferencias.base_datos_revisada +']' as base_datos_revisada, empresa_referencia.Nombre + ' ['+diferencias.base_datos_referencia + ']' as base_datos_referencia"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id','=','diferencias.id_busqueda')
            ->join('PolizasCtpqIncidentes.ctg_tipos', 'ctg_tipos.id','=','diferencias.id_tipo')
            ->join('Contabilidad.ListaEmpresas as empresa_revisada', 'empresa_revisada.AliasBDD','=','diferencias.base_datos_revisada')
            ->join('Contabilidad.ListaEmpresas as empresa_referencia', 'empresa_referencia.AliasBDD','=','diferencias.base_datos_referencia')
            ->where("diferencias.activo","1")
            ->where("diferencias.tipo_busqueda",$tipo_busqueda)
            ->groupBy(DB::raw("descripcion, diferencias.base_datos_revisada, diferencias.base_datos_referencia, empresa_revisada.Nombre, empresa_referencia.Nombre"))
            ->get();
        return $dem;
    }

    public static function cantidadDiferenciasDetectadas($id_lote)
    {
        $dem = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw("count(diferencias.id) as cantidad"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id','=','diferencias.id_busqueda')
            ->where("diferencias.activo","1")
            ->where("busquedas_diferencias.id_lote", $id_lote)
            ->first();
        return $dem->cantidad;
    }

    public static function cantidadDiferenciasCorregidas($id_lote)
    {
        $dem = DB::table('PolizasCtpqIncidentes.diferencias_corregidas')
            ->select(DB::raw("count(diferencias_corregidas.id) as cantidad"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id','=','diferencias_corregidas.id_busqueda')
            ->where("busquedas_diferencias.id_lote", $id_lote)
            ->first();
        return $dem->cantidad;
    }

    public function getCodigoCuentaAttribute()
    {
        if($this->id_cuenta>0)
        {
            return $this->cuenta->Codigo;
        } else {
            return "";
        }
    }

    public function getIdentificadorPolizaAttribute()
    {
        if($this->poliza)
        {
            return $this->poliza->Ejercicio .'-'. $this->poliza->Periodo .'-'. $this->poliza->tipo_poliza->Nombre .'-'. $this->poliza->Folio;
        } else {
            return "";
        }
    }

    public function getNumeroMovimientoAttribute()
    {
        if($this->movimiento)
        {
            return $this->movimiento->NumMovto;
        } else {
            return "-";
        }
    }
    public function getCampoAttribute()
    {
        switch ($this->id_tipo){
            case 2: return  "Concepto Póliza";
                break;
            case 8: return  "Referencia Movimiento";
                break;
            case 9: return  "Concepto Movimiento";
                break;
        }
    }

    public function getMovimientosOrdenarAttribute()
    {
        $arreglo = [];
        if($this->id_tipo ==12){
            $arreglo = [];
            if($this->poliza)
            {
                $relacion = RelacionPolizas::where("id_poliza_a","=", $this->id_poliza)
                    ->where("base_datos_a","=",$this->base_datos_revisada)
                    ->where("tipo_relacion","=",$this->tipo_busqueda)->first();
                $movimientos = $this->poliza->movimientos()->orderBy("NumMovto")->get();

                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $relacion->base_datos_b);
                $poliza_relacionada = Poliza::find($relacion->id_poliza_b);
                $movimientos_relacionados = $poliza_relacionada->movimientos()->orderBy("NumMovto")->get();
                $i=0;
                foreach($movimientos as $movimiento){
                    DB::purge('cntpq');
                    Config::set('database.connections.cntpq.database', $relacion->base_datos_a);
                    $movimiento->load("cuenta");

                    DB::purge('cntpq');
                    Config::set('database.connections.cntpq.database', $relacion->base_datos_b);
                    $movimientos_relacionados[$i]->load("cuenta");

                    $arreglo[] = [
                        "no_movto_a"=>$movimiento->NumMovto,
                        "no_movto_b"=>$movimientos_relacionados[$i]->NumMovto,
                        "codigo_a"=>$movimiento->cuenta->Codigo,
                        "codigo_b"=>$movimientos_relacionados[$i]->cuenta->Codigo,
                        "cuenta_a"=>$movimiento->cuenta->Nombre,
                        "cuenta_b"=>$movimientos_relacionados[$i]->cuenta->Nombre,
                        "cargo_a"=>$movimiento->cargo_format,
                        "cargo_b"=>$movimientos_relacionados[$i]->cargo_format,
                        "abono_a"=>$movimiento->abono_format,
                        "abono_b"=>$movimientos_relacionados[$i]->abono_format,
                    ];
                    $i++;
                }
            }
            return $arreglo;
        } else {
            return "";
        }
    }

    public function getValorAFormatAttribute()
    {
        if(is_numeric($this->valor_a) && $this->id_tipo != 6){
            return number_format($this->valor_a,2);
        } else {
            return $this->valor_a;
        }
    }

    public function getValorBFormatAttribute()
    {
        if(is_numeric($this->valor_b) && $this->id_tipo != 6){
            return number_format($this->valor_b,2);
        } else {
            return $this->valor_b;
        }
    }
}