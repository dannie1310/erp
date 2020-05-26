<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes;


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
        "id_tipo_busqueda","cantidad_polizas_revisadas",
        "cantidad_polizas_existentes"
    ];

    public function busquedas()
    {
        return $this->hasMany(Busqueda::class, "id_lote", "id");
    }

    public function diferencias_detectadas()
    {
        return $this->hasManyThrough(Diferencia::class, Busqueda::class, "id_lote", "id_busqueda", "id", "id");
    }

    public function diferencias_corregidas()
    {
        return $this->hasManyThrough(DiferenciaCorregida::class, Busqueda::class, "id_lote", "id_busqueda", "id", "id");
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
            ->where("activo",1)
            ->first();
        return $dem->cantidad_polizas;
    }

    public function getCantidadDiferenciasDetectadasPorTipoAttribute()
    {
        $dem = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw("count(diferencias.id) as cantidad, count(distinct diferencias.id_poliza) as cantidad_polizas,  ctg_tipos.descripcion as descripcion"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
            ->join('PolizasCtpqIncidentes.ctg_tipos', 'ctg_tipos.id', '=', 'diferencias.id_tipo')
            ->where("busquedas_diferencias.id_lote", $this->id)
            ->groupBy("descripcion")
            ->get();
        return $dem;
    }

    public function getCantidadDiferenciasDetectadasPorTipoPorBaseAttribute()
    {
        $dem = DB::table('PolizasCtpqIncidentes.diferencias')
            ->select(DB::raw("count(diferencias.id) as cantidad, count(distinct diferencias.id_poliza) as cantidad_polizas,  ctg_tipos.descripcion as descripcion, empresa_revisada.Nombre +' ['+diferencias.base_datos_revisada +']' as base_datos_revisada, empresa_referencia.Nombre + ' ['+diferencias.base_datos_referencia + ']' as base_datos_referencia"))
            ->join('PolizasCtpqIncidentes.busquedas_diferencias', 'busquedas_diferencias.id', '=', 'diferencias.id_busqueda')
            ->join('PolizasCtpqIncidentes.ctg_tipos', 'ctg_tipos.id', '=', 'diferencias.id_tipo')
            ->join('Contabilidad.ListaEmpresas as empresa_revisada', 'empresa_revisada.AliasBDD', '=', 'diferencias.base_datos_revisada')
            ->join('Contabilidad.ListaEmpresas as empresa_referencia', 'empresa_referencia.AliasBDD', '=', 'diferencias.base_datos_referencia')
            ->where("busquedas_diferencias.id_lote", $this->id)
            ->groupBy(DB::raw("descripcion, diferencias.base_datos_revisada, diferencias.base_datos_referencia, empresa_revisada.Nombre, empresa_referencia.Nombre"))
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
        switch ($this->id_tipo_busqueda)
        {
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

    public function setCantidadPolizasExistentes($cantidad = 0){
        $this->cantidad_polizas_existentes = $cantidad;
        $this->save();
    }

    public function setCantidadPolizasRevisadas($cantidad = 0){
        $this->cantidad_polizas_revisadas += $cantidad;
        $this->save();
    }

    public function finaliza()
    {
        $this->fecha_hora_fin = date('Y-m-d H:i:s');
        $this->save();

        $catidad_polizas_existentes = $this->busquedas->sum("cantidad_polizas_existentes");
        $this->setCantidadPolizasExistentes($catidad_polizas_existentes);

        event(new FinalizaProcesamientoLoteBusquedas(
            $this
        ));
    }
}