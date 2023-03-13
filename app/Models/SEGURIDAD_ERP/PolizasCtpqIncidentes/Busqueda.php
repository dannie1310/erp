<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Events\FinalizaProcesamientoLoteBusquedas;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use App\Utils\BusquedaDiferenciasMovimientos;
use App\Utils\BusquedaDiferenciasPolizas;
use App\Models\CTPQ\Poliza;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Busqueda extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PolizasCtpqIncidentes.busquedas_diferencias';
    public $timestamps = false;
    protected $fillable = [
        "id_tipo_busqueda",
        "base_datos_busqueda",
        "base_datos_referencia",
        "ejercicio",
        "periodo",
        "fecha_hora_inicio",
        "fecha_hora_fin",
        "id_lote","cantidad_polizas_revisadas",
        "cantidad_polizas_existentes"
    ];

    public function lote()
    {
        return $this->belongsTo(LoteBusqueda::class,"id_lote", "id");
    }

    public function diferencias_detectadas()
    {
        return $this->hasMany(Diferencia::class,"id_busqueda","id");
    }

    public function diferencias_corregidas()
    {
        return $this->hasMany(DiferenciaCorregida::class,"id_busqueda","id");
    }

    public function empresa_busqueda()
    {
        return $this->belongsTo(Empresa::class, "base_datos_busqueda", "AliasBDD");
    }

    private function obtienePolizasRevisar()
    {
        DB::purge('cntpq');
        $polizas = [];
        Config::set('database.connections.cntpq.database', $this->base_datos_busqueda);
        try {
            $polizas = Poliza::where("Ejercicio", $this->ejercicio)->where("Periodo", $this->periodo)->get();
            BaseDatosRevisada::registrar(["base_datos"=>$this->base_datos_busqueda, "id_lote_busqueda"=>$this->lote->id, "cantidad_polizas_existentes"=>Poliza::count(),"cantidad_polizas_revisadas"=>count($polizas)]);
        } catch (\Exception $e) {
            BaseDatosInaccesible::registrar(["base_datos"=>$this->base_datos_busqueda, "id_lote_busqueda"=>$this->lote->id]);
            $this->finaliza();
        }
        return $polizas;
    }

    public static function actualizaDatosRelacion()
    {
        $relaciones = RelacionPolizas::all();
        foreach($relaciones as  $relacion){
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $relacion->base_datos_a);
            $poliza = Poliza::find($relacion->id_poliza_a);
            $relacion->folio = $poliza->Folio;
            $relacion->ejercicio = $poliza->Ejercicio;
            $relacion->periodo = $poliza->Periodo;
            $relacion->tipo = $poliza->TipoPol;
            $relacion->save();
        }
    }

    public function procesarBusquedaDiferencias()
    {
        $this->fecha_hora_inicio = date('Y-m-d H:i:s');
        $this->save();
        $polizas = $this->obtienePolizasRevisar();
        $this->lote->setCantidadPolizasRevisadas(count($polizas));
        $this->cantidad_polizas_existentes = $this->empresa_busqueda->getCantidadPolizas($this->ejercicio, $this->periodo);
        $this->save();

        foreach ($polizas as $poliza) {
            $relaciones = $poliza->relaciona($this);
            if(key_exists("relacion_poliza",$relaciones))
            {
                if($relaciones["relacion_poliza"]){
                    $busqueda = New BusquedaDiferenciasPolizas($relaciones, $this);
                    $busqueda->buscarDiferenciasPolizas();
                }
            }
        }
        $this->finaliza(count($polizas));
    }

    public function procesarBusquedaDiferenciasExtendida()
    {
        $this->fecha_hora_inicio = date('Y-m-d H:i:s');
        $this->save();
        $polizas = $this->obtienePolizasRevisar();
        //$this->cantidad_polizas_revisadas = count($polizas);
        $this->lote->setCantidadPolizasRevisadas(count($polizas));
        $this->cantidad_polizas_existentes = $this->empresa_busqueda->getCantidadPolizas($this->ejercicio, $this->periodo);
        $this->save();

        foreach ($polizas as $poliza) {
            $impedir_busqueda=false;
            $relaciones = $poliza->relaciona($this);
            if(key_exists("relacion_poliza",$relaciones))
            {
                if($relaciones["relacion_poliza"]){
                    $busqueda = New BusquedaDiferenciasPolizas($relaciones, $this);
                    $impedir_busqueda = $busqueda->buscarDiferenciasPolizas();
                }
            }
            if(!$impedir_busqueda){
                if(key_exists("relaciones_movimientos",$relaciones)){
                    foreach ($relaciones["relaciones_movimientos"] as $relacion_movimiento)
                    {
                        if($relacion_movimiento){
                            $busqueda_movimiento = New BusquedaDiferenciasMovimientos($relacion_movimiento, $this);
                            $busqueda_movimiento->buscarDiferenciasMovimientos();
                        }
                    }
                }
            }
        }
        $this->finaliza(count($polizas));
    }

    public function finaliza($numero_polizas = 0){
        $this->cantidad_polizas_revisadas = $numero_polizas;
        $this->fecha_hora_fin = date('Y-m-d H:i:s');
        $this->save();
        $ultima_busqueda_sin_finalizar = $this->lote->busquedas()->whereNull("fecha_hora_fin")->first();
        if(!$ultima_busqueda_sin_finalizar){
            $this->lote->finaliza();
        }
    }

}
