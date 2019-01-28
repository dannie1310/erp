<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 01:37 PM
 */

namespace App\Http\Controllers\v1;


use App\Models\CADECO\Almacen;
use App\Models\CADECO\Concepto;
use App\Models\CADECO\Contabilidad\EstatusPrepoliza;
use App\Models\CADECO\Empresa;
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
                'con_cuenta' => Empresa::query()->has('cuentaEmpresa')->count()
            ],
            'material' => [
                'total' => Material::query()->count(),
                'con_cuenta' => Material::query()->has('cuentaMaterial')->count()
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
}