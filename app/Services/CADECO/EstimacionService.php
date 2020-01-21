<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:21 PM
 */

namespace App\Services\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Item;
use App\Models\CADECO\Obra;
use App\PDF\Contratos\EstimacionFormato;
use App\PDF\Contratos\OrdenPagoEstimacion;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstimacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EstimacionService constructor.
     */
    public function __construct(Estimacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function find($id)
    {
        return $this->repository->where('id_transaccion', '=', $id);
    }

    public function pdfOrdenPago($id)
    {
        $pdf = new OrdenPagoEstimacion($id);
       return $pdf;
    }

    public function store($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $antes_iva = Obra::query()->find(Context::getIdObra())->datosContables->retencion_antes_iva;

                $estimacion = $this->repository->create($data);

                $pct_anticipo = $estimacion->subcontrato->porcentaje_anticipo;
                $pct_fondo_garantia = $estimacion->subcontrato->porcentaje_fondo_garantia;
                $empresa = $estimacion->subcontrato->empresa;

                $suma_importes = $this->estimaConceptos($estimacion, $data['conceptos']);

                $estimacion->calculaImportes();

                DB::connection('cadeco')->commit();

                return $estimacion;

        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function estimaConceptos($estimacion, $conceptos)
    {
        $suma = 0;
        foreach ($conceptos as $concepto)
        {
            $suma += $concepto['importe'];

            $pu = Item::query()
                ->where('id_transaccion', '=', $estimacion->id_antecedente)
                ->where('id_concepto', '=', $concepto['item_antecedente'])
                ->first()->precio_unitario;

            Item::query()->create([
                'id_transaccion' => $estimacion->id_transaccion,
                'id_antecedente' => $estimacion->id_antecedente,
                'item_antecedente' => $concepto['item_antecedente'],
                'id_concepto' => $concepto['id_concepto'],
                'cantidad' => $concepto['cantidad'],
                'cantidad_material' => 0,
                'cantidad_mano_obra' => 0,
                'importe' => $concepto['importe'],
                'precio_unitario' => $pu,
                'precio_material' => 0,
                'precio_mano_obra' => 0
            ]);
        }
        return $suma;
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function showEstimacionTable($id)
    {
        $estimacion= $this->repository->show($id);
        $numEstimacion=$estimacion->subcontratoEstimacion;

        $partidas=$estimacion->subcontrato->partidasOrdenadas;

        $suma_contrato=0;
        $suma_estimadoAnterior=0;
        $suma_estimacion=0;
        $suma_acumulado=0;
        $suma_porEstimar=0;

        $items=array();

        foreach ($partidas as $partida ){


            foreach($partida->ancestros as $ancestro){
                $items[$ancestro[1]]=$ancestro[0];

            }

           if($item = $partida->getEstimacionPartidaAttribute($id)) {
               $precioUnitario = $item->precio_unitario;
               $cantidadContrato = $partida->cantidad;
               $cantidadEstimadoAnterior = $item->getEstimadoAnteriorAttribute($id);
               $cantidadEstimacion = $item->cantidad;



               $items[$item->contrato->nivel] = Array(
                   'concepto' => $item->contrato->descripcion,
                   'unidad' => $item->contrato->unidad,
                   'precioUnitario' =>  $precioUnitario,
                   'cantidadContrato' => $cantidadContrato ,
                   'importeContrato' => $cantidadContrato * $precioUnitario,
                   'cantidadEstimadoAnterior' =>$cantidadEstimadoAnterior ,
                   'importeEstimadoAnterior' => $cantidadEstimadoAnterior * $precioUnitario,
                   'cantidadEstimacion' => $item->cantidad,
                   'importeEstimacion' => $cantidadEstimacion * $precioUnitario,
                   'cantidadAcumulado' => ($cantidadEstimadoAnterior + $cantidadEstimacion),
                   'importeAcumulado' => ($cantidadEstimadoAnterior + $cantidadEstimacion) * $precioUnitario,
                   'cantidadPorEstimar' => $cantidadContrato - ($cantidadEstimadoAnterior + $cantidadEstimacion),
                   'importePorEstimar' => ($cantidadContrato - ($cantidadEstimadoAnterior + $cantidadEstimacion)) * $precioUnitario,
               );

               /*Totales */
               $suma_contrato += $cantidadContrato * $precioUnitario;
               $suma_estimadoAnterior += $cantidadEstimadoAnterior * $precioUnitario;
               $suma_estimacion += $cantidadEstimacion * $precioUnitario;
               $suma_acumulado += ($cantidadEstimadoAnterior + $cantidadEstimacion) * $precioUnitario;
               $suma_porEstimar += ($cantidadContrato - ($cantidadEstimadoAnterior + $cantidadEstimacion)) * $precioUnitario;

           }else{
               $precioUnitario = $partida->precio_unitario;
               $cantidadContrato = $partida->cantidad;
               $cantidadEstimadoAnterior = $partida->getEstimadoAnteriorAttribute($id);
               $cantidadEstimacion = $partida->cantidad;
               $items[$partida->contrato->nivel] = Array(
                   'id_concepto' => $partida->contrato->id_concepto,
                   'concepto' => $partida->contrato->descripcion,
                   'unidad' => $partida->contrato->unidad,
                   'precioUnitario' => $precioUnitario,
                   'cantidadContrato' => $cantidadContrato,
                   'importeContrato' => $cantidadContrato * $precioUnitario,
                   'cantidadEstimadoAnterior' => $cantidadEstimadoAnterior,
                   'importeEstimadoAnterior' => $cantidadEstimadoAnterior * $precioUnitario,
                   'cantidadEstimacion' => 0,
                   'importeEstimacion' => 0,
                   'cantidadAcumulado' => ($cantidadEstimadoAnterior),
                   'importeAcumulado' => ($cantidadEstimadoAnterior ) * $precioUnitario,
                   'cantidadPorEstimar' => $cantidadContrato - ($cantidadEstimadoAnterior),
                   'importePorEstimar' => ($cantidadContrato - ($cantidadEstimadoAnterior)) * $precioUnitario,
               );
               $suma_contrato += $cantidadContrato * $precioUnitario;
               $suma_estimadoAnterior += $cantidadEstimadoAnterior * $precioUnitario;
               $suma_acumulado += ($cantidadEstimadoAnterior) * $precioUnitario;
               $suma_porEstimar += ($cantidadContrato - ($cantidadEstimadoAnterior)) * $precioUnitario;
           }
      }



        $result=array(
            'fecha_inicial'=>Carbon::parse($estimacion->cumplimiento)->format('d-m-Y'),
            'fecha_final'=>Carbon::parse($estimacion->vencimiento)->format('d-m-Y'),
            'fecha'=>Carbon::parse($estimacion->fecha)->format('d-m-Y'),
            'estimacion'=>$estimacion->toArray(),
            'numEstimacion'=>$numEstimacion,
            'empresa' => $estimacion->empresa->toArray(),
            'subcontrato' =>$estimacion->subcontrato->toArray(),
            'moneda' =>$estimacion->moneda->toArray(),
            'items'=>$items,
            'suma_contrato'=>number_format($suma_contrato,4),
            'suma_estimadoAnterior'=> number_format($suma_estimadoAnterior,4),
            'suma_estimacion'=>number_format($suma_estimacion,4),
            'suma_acumulado'=>number_format($suma_acumulado,4),
            'suma_porEstimar'=>number_format($suma_porEstimar,4),
        );

        return $result;

    }

    public function paginate($data)
    {
        $estimaciones = $this->repository;

        if(isset($data['numero_folio'])){
        $estimaciones = $estimaciones->where([['numero_folio','=',$data['numero_folio']]]);
        }

        return $estimaciones->paginate($data);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public  function aprobar($id)
    {
        $estimacion = $this->repository->show($id);
        try {
            DB::connection('cadeco')->beginTransaction();
            $estimacion->aprobar();
            DB::connection('cadeco')->commit();
            $estimacion->refresh();
            return $estimacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function revertirAprobacion($id)
    {
        $estimacion = $this->repository->show($id);
        try {
            DB::connection('cadeco')->beginTransaction();
            $estimacion->revertirAprobacion();
            DB::connection('cadeco')->commit();
            $estimacion->refresh();
            return $estimacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function pdfEstimacion($id)
    {
        $pdf = new EstimacionFormato($id);
        return $pdf;
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data']);
    }
}
