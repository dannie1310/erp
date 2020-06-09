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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

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


    public function poliza()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos_revisada);
        return $this->belongsTo(Poliza::class, "id_poliza", "Id");
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
}