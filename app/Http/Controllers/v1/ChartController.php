<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 01:37 PM
 */

namespace App\Http\Controllers\v1;


use App\Facades\Context;
use App\Models\CADECO\Almacen;
use App\Models\CADECO\Concepto;
use App\Models\CADECO\Contabilidad\EstatusPrepoliza;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Fondo;
use App\Models\CADECO\Material;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('context');
    }

    public function avanceCuentasContables() {
        $data = [
            'almacen' => [
                'total' => Almacen::query()->count(),
                'con_cuenta' => Almacen::query()->has('cuentaAlmacen')->count()
            ],
            'concepto' => [
                'total' => Concepto::query()->count(),
                'con_cuenta' => Concepto::query()->has('cuentaConcepto')->count()
            ],
            'empresa' => [
                'total' => Empresa::query()->count(),
                'con_cuenta' => Empresa::query()->has('cuentasEmpresa')->count()
            ],
            'material' => [
                'total' => Material::query()->count(),
                'con_cuenta' => Material::query()->has('cuentaMaterial')->count()
            ],
            'fondo' => [
                'total' => Fondo::query()->count(),
                'con_cuenta' => Fondo::query()->has('cuentaFondo')->count()
            ]
        ];

        return $data;
    }

    public function prepolizasSemanal() {
        $fechas = $this->getDates();

        $config = [
            'labels' => $fechas,
            'datasets' => []
        ];

        foreach (EstatusPrepoliza::all() as $estatus) {
            $d = [];
            $resp = collect( DB::connection('cadeco')->table(DB::raw('Contabilidad.int_polizas WITH (NOLOCK)'))->select(DB::raw("FORMAT(fecha, 'yyyy/MM/dd') as fecha_"), DB::raw(" COUNT(1) AS count"))
                ->whereBetween('Contabilidad.int_polizas.fecha', [$fechas[0], $fechas[count($fechas)-1]])
                ->where('Contabilidad.int_polizas.estatus', '=', $estatus->estatus)
                ->where('Contabilidad.int_polizas.id_obra_cadeco', '=', Context::getIdObra())
                ->groupBy('Contabilidad.int_polizas.fecha')->get());

            if(count($resp) > 0) {
                for ($i = 0; $i < count($fechas); $i++) {
                    foreach ($resp as $r){
                        if ($fechas[$i] == $r->fecha_){
                            $d[$i] = $r->count;
                            break;
                        }
                        $d[$i] = 0;
                    }
                }
            } else {
                for ($j = 0; $j < count($fechas); $j++) {
                    $d[$j] = 0;
                }
            }

            array_push($config['datasets'], [
                'label' => $estatus->descripcion,
                'backgroundColor' => $estatus->label,
                'borderColor' => $estatus->label,
                'data' => $d,
                'fill' => false
            ]);
        }
        return $config;
    }

    private function getDates()
    {
        $fechas = [];
        $hoy = Carbon::now();
        $pasado = Carbon::now()->subDays(7);


        for($date = $pasado; $date->lte($hoy); $date->addDay()) {
            $fechas[] = $date->format('Y/m/d');
        }

        return $fechas;
    }

    public function polizasDoughnut() {
        $labels=[];
        $data = [];
        $backgroundColor = [];
        $estatus=[];

        $acumulado = Poliza::query()->select(DB::raw("COUNT(1) AS count"), 'estatus')->groupBy('estatus')->get();
        foreach (EstatusPrePoliza::all() as $status) {
            for($i = 0; $i < count($acumulado); $i++){
                if($acumulado[$i]->estatus == $status->estatus){
                    $labels[] = $status->descripcion;
                    $data[] = $acumulado[$i]->count;
                    $backgroundColor[] = $status->label;
                    $estatus[] = $status->estatus;
                    break;
                }
            }
        }

        $acum = [
            'labels' => $labels,
            'estatus'=> $estatus,
            'datasets' => [[
                'data'=> $data,
                'backgroundColor'=> $backgroundColor
            ]]
        ];
        return response()->json($acum);
    }

    public function pagosAnticipados() {
        $labels=[];
        $data = [];
        $backgroundColor = [];
        $estatus=[];

        $acumulado = Poliza::query()->select(DB::raw("COUNT(1) AS count"), 'estatus')->groupBy('estatus')->get();
        foreach (EstatusPrePoliza::all() as $status) {
            for($i = 0; $i < count($acumulado); $i++){
                if($acumulado[$i]->estatus == $status->estatus){
                    $labels[] = $status->descripcion;
                    $data[] = $acumulado[$i]->count;
                    $backgroundColor[] = $status->label;
                    $estatus[] = $status->estatus;
                    break;
                }
            }
        }

        $acum = [
            'labels' => $labels,
            'estatus'=> $estatus,
            'datasets' => [[
                'data'=> $data,
                'backgroundColor'=> $backgroundColor
            ]]
        ];
        return response()->json($acum);
    }
}
