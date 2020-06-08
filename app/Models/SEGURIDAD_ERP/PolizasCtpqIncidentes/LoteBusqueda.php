<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion;
use Illuminate\Database\Eloquent\Model;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\DB;
use App\Events\FinalizaProcesamientoLoteBusquedas;

class LoteBusqueda extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PolizasCtpqIncidentes.lotes_busquedas_diferencias';
    public $timestamps = false;
    protected $fillable = [
        "usuario_inicio",
        "fecha_hora_inicio",
        "fecha_hora_fin",
        "id_tipo_busqueda", "cantidad_polizas_revisadas",
        "cantidad_polizas_existentes",
        "cantidad_diferencias_detectadas",
        "cantidad_diferencias_corregidas"
    ];

    public function busquedas()
    {
        return $this->hasMany(Busqueda::class, "id_lote", "id");
    }

    public function bases_datos_inaccesibles()
    {
        return $this->hasMany(BaseDatosInaccesible::class, "id_lote_busqueda", "id");
    }

    public function bases_datos_revisadas()
    {
        return $this->hasMany(BaseDatosRevisada::class, "id_lote_busqueda", "id");
    }

    public function diferencias_detectadas()
    {
        return $this->hasManyThrough(Diferencia::class, Busqueda::class, "id_lote", "id_busqueda", "id", "id");
    }

    public function diferencias_corregidas()
    {
        return $this->hasManyThrough(DiferenciaCorregida::class, Busqueda::class, "id_lote", "id_busqueda", "id", "id");
    }

    public function solicitudes_edicion()
    {
        return $this->hasMany(SolicitudEdicion::class, "id_lote_busqueda", "id");
    }

    public function getFechaHoraInicioFormatAttribute()
    {
        $date = date_create($this->fecha_hora_inicio);
        return date_format($date, "d/m/Y H:i:s");
    }

    public function getFechaHoraFinFormatAttribute()
    {
        $date = date_create($this->fecha_hora_fin);
        return date_format($date, "d/m/Y H:i:s");
    }

    public function getCantidadPolizasConErroresAttribute2()
    {
        $dem = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw("count(diferencias.id) as cantidad, count(distinct diferencias.id_poliza) as cantidad_polizas,  ctg_tipos.descripcion as descripcion"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
            ->join('PolizasCtpqIncidentes.ctg_tipos', 'ctg_tipos.id', '=', 'diferencias.id_tipo')
            ->where("busquedas_diferencias.id_lote", $this->id)
            ->groupBy("descripcion")
            ->get();
        return $dem->sum("cantidad_polizas");
    }

    public function getCantidadPolizasConErroresAttribute()
    {
        $dem = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw(" count(distinct diferencias.id_poliza) as cantidad_polizas"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
            ->where("busquedas_diferencias.id_lote", $this->id)
            ->where("activo", 1)
            ->first();
        return $dem->cantidad_polizas;
    }

    public function getCantidadPolizasConErroresFormatAttribute()
    {
        return number_format($this->cantidad_polizas_con_errores, 0, ".", ",");
    }

    public function getCantidadDiferenciasDetectadasPorTipoAttribute()
    {
        $dem = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw("count(diferencias.id) as cantidad, count(distinct diferencias.id_poliza) as cantidad_polizas, 
             lotes_busquedas_diferencias.cantidad_polizas_revisadas as cantidad_polizas_revisadas, ctg_tipos.descripcion as descripcion"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
            ->join('PolizasCtpqIncidentes.lotes_busquedas_diferencias', 'busquedas_diferencias.id_lote', '=', 'lotes_busquedas_diferencias.id')
            ->join('PolizasCtpqIncidentes.ctg_tipos', 'ctg_tipos.id', '=', 'diferencias.id_tipo')
            ->where("busquedas_diferencias.id_lote", $this->id)
            ->groupBy(DB::raw("descripcion, lotes_busquedas_diferencias.cantidad_polizas_revisadas"))
            ->get();
        return $dem;
    }

    public function getPorcentajeDiferenciasAttribute()
    {
        $porcentaje = 0;
        if ($this->busquedas->sum("cantidad_polizas_revisadas") > 0) {
            $porcentaje = number_format($this->cantidad_polizas_con_errores / $this->busquedas->sum("cantidad_polizas_revisadas") * 100, 2);
        }
        return $porcentaje . " %";
    }

    public function getCantidadDiferenciasDetectadasPorTipoPorBaseAttribute()
    {
        $dem = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw("count(diferencias.id) as cantidad, count(distinct diferencias.id_poliza) as cantidad_polizas, 
            bases_datos_revisadas_x_lotes.cantidad_polizas_revisadas,
             ctg_tipos.descripcion as descripcion, empresa_revisada.Nombre +' ['+diferencias.base_datos_revisada +']' as base_datos_revisada, empresa_referencia.Nombre + ' ['+diferencias.base_datos_referencia + ']' as base_datos_referencia"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
            ->join('PolizasCtpqIncidentes.ctg_tipos', 'ctg_tipos.id', '=', 'diferencias.id_tipo')
            ->join('Contabilidad.ListaEmpresas as empresa_revisada', 'empresa_revisada.AliasBDD', '=', 'diferencias.base_datos_revisada')
            ->join('Contabilidad.ListaEmpresas as empresa_referencia', 'empresa_referencia.AliasBDD', '=', 'diferencias.base_datos_referencia')
            ->join("PolizasCtpqIncidentes.bases_datos_revisadas_x_lotes", function ($join) {
                $join->on("PolizasCtpqIncidentes.bases_datos_revisadas_x_lotes.id_lote_busqueda", "=", "PolizasCtpqIncidentes.busquedas_diferencias.id_lote");
                $join->on("PolizasCtpqIncidentes.bases_datos_revisadas_x_lotes.base_datos", "=", "PolizasCtpqIncidentes.busquedas_diferencias.base_datos_busqueda");
            })
            ->where("busquedas_diferencias.id_lote", $this->id)
            ->groupBy(DB::raw("descripcion, diferencias.base_datos_revisada, diferencias.base_datos_referencia, empresa_revisada.Nombre, empresa_referencia.Nombre, bases_datos_revisadas_x_lotes.cantidad_polizas_revisadas"))
            ->get();
        return $dem;
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_inicio', 'idusuario');
    }

    public static function getLoteActivo()
    {
        return LoteBusqueda::whereNull("fecha_hora_fin")->first();
    }

    public function getTipoStrAttribute()
    {
        $tipo = "";
        switch ($this->id_tipo_busqueda) {
            case 1:
                $tipo = "Individual vs Consolidada";
                break;
            case 2:
                $tipo = "Individual vs Individual Histórica";
                break;
            case 3:
                $tipo = "Consolidada vs Consolidada Histórica";
                break;
            case 4:
                $tipo = "Individual Histórica vs Consolidada Histórica";
                break;
        }
        return $tipo;
    }

    public function setCantidadPolizasRevisadas($cantidad = 0)
    {
        $this->cantidad_polizas_revisadas += $cantidad;
        $this->save();
    }

    public function finaliza()
    {
        $this->fecha_hora_fin = date('Y-m-d H:i:s');
        $this->cantidad_polizas_existentes = $this->bases_datos_revisadas->sum("cantidad_polizas_existentes");
        $this->cantidad_diferencias_detectadas = Diferencia::cantidadDiferenciasDetectadas($this->id);
        $this->cantidad_diferencias_corregidas = Diferencia::cantidadDiferenciasCorregidas($this->id);
        $this->save();
        $this->generaSolicitudesCambio();
        event(new FinalizaProcesamientoLoteBusquedas(
            $this
        ));
    }

    public function getCantidadPolizasRevisadasFormatAttribute()
    {
        return number_format($this->busquedas->sum("cantidad_polizas_revisadas"), 0, ".", ",");
    }

    public function getCantidadDiferenciasDetectadasFormatAttribute()
    {
        return number_format($this->cantidad_diferencias_detectadas, 0, ".", ",");
    }

    public function getCantidadPolizasExistentesFormatAttribute()
    {
        return number_format($this->cantidad_polizas_existentes, 0, ".", ",");
    }

    public function getCantidadDiferenciasCorregidasFormatAttribute()
    {
        return number_format($this->cantidad_diferencias_corregidas, 0, ".", ",");
    }

    public function generaSolicitudesCambio()
    {
        ini_set('max_execution_time', '7200') ;
        $this->generaSolicitudesCambioConceptoReferencia();
        $this->generaSolicitudesReordenamiento();
        $this->generaSolicitudesCambioNombreCuenta();
    }

    private function generaSolicitudesCambioConceptoReferencia()
    {
        /*2,8,9*/
        $bases_datos_revisadas = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw("diferencias.base_datos_revisada"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
            ->whereIn("diferencias.id_tipo", [2,8,9])
            ->where("activo","=",1)
            ->where("busquedas_diferencias.id_lote", $this->id)
            ->groupBy(DB::raw("base_datos_revisada"))
            ->orderByRaw("base_datos_revisada")
            ->get();

        if($bases_datos_revisadas){

            foreach ($bases_datos_revisadas as $base_datos_revisada)
            {
                $solicitud_edicion = $this->solicitudes_edicion()->create(["id_tipo"=>2, "base_datos" => $base_datos_revisada->base_datos_revisada]);
                $diferencias =(array) DB::table('PolizasCtpqIncidentes.diferencias')
                    ->select(DB::raw("diferencias.id as id_diferencia"))
                    ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
                    ->whereIn("diferencias.id_tipo", [2,8,9])
                    ->where("activo","=",1)
                    ->where("busquedas_diferencias.id_lote", $this->id)
                    ->where("diferencias.base_datos_revisada", $base_datos_revisada->base_datos_revisada)
                    ->orderByRaw("id_diferencia")
                    ->pluck("id_diferencia")->toArray();

                foreach ($diferencias as $diferencia)
                {
                    $solicitud_edicion->partidas()->create(["id_diferencia"=>$diferencia]);
                }
            }
        }
    }

    private function generaSolicitudesReordenamiento()
    {
        /*12*/
        $bases_datos_revisadas = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw("diferencias.base_datos_revisada"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
            ->whereIn("diferencias.id_tipo", [12])
            ->where("activo","=",1)
            ->where("busquedas_diferencias.id_lote", $this->id)
            ->groupBy(DB::raw("base_datos_revisada"))
            ->orderByRaw("base_datos_revisada")
            ->get();

        if($bases_datos_revisadas){
            foreach ($bases_datos_revisadas as $base_datos_revisada)
            {
                $solicitud_edicion = $this->solicitudes_edicion()->create(["id_tipo"=>3, "base_datos" => $base_datos_revisada->base_datos_revisada]);
                $diferencias =(array) DB::table('PolizasCtpqIncidentes.diferencias')
                    ->select(DB::raw("diferencias.id as id_diferencia"))
                    ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
                    ->whereIn("diferencias.id_tipo", [12])
                    ->where("activo","=",1)
                    ->where("busquedas_diferencias.id_lote", $this->id)
                    ->where("diferencias.base_datos_revisada", $base_datos_revisada->base_datos_revisada)
                    ->orderByRaw("id_diferencia")
                    ->pluck("id_diferencia")->toArray();

                foreach ($diferencias as $diferencia)
                {
                    $solicitud_edicion->partidas()->create(["id_diferencia"=>$diferencia]);
                }
            }
        }
    }

    private function generaSolicitudesCambioNombreCuenta()
    {
        /*7*/
        $bases_datos_revisadas = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw("diferencias.base_datos_revisada"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
            ->whereIn("diferencias.id_tipo", [7])
            ->where("activo","=",1)
            ->where("busquedas_diferencias.id_lote", $this->id)
            ->groupBy(DB::raw("base_datos_revisada"))
            ->orderByRaw("base_datos_revisada")
            ->get();

        if($bases_datos_revisadas){
            foreach ($bases_datos_revisadas as $base_datos_revisada)
            {
                $solicitud_edicion = $this->solicitudes_edicion()->create(["id_tipo"=>4, "base_datos" => $base_datos_revisada->base_datos_revisada]);
                $diferencias =(array) DB::table('PolizasCtpqIncidentes.diferencias')
                    ->select(DB::raw("min(diferencias.id) as id_diferencia, valor_a, valor_b"))
                    ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
                    ->whereIn("diferencias.id_tipo", [7])
                    ->where("activo","=",1)
                    ->where("busquedas_diferencias.id_lote", $this->id)
                    ->where("diferencias.base_datos_revisada", $base_datos_revisada->base_datos_revisada)
                    ->groupBy(DB::raw("valor_a, valor_b"))
                    ->orderByRaw("id_diferencia")
                    ->pluck("id_diferencia")->toArray();

                foreach ($diferencias as $diferencia)
                {
                    $solicitud_edicion->partidas()->create(["id_diferencia"=>$diferencia]);
                }
            }
        }
    }
}