<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 15/07/2020
 * Time: 03:21 PM
 */

namespace App\Services\CADECO\Contratos;

use Exception;
use App\Facades\Context;
use App\PDF\Contratos\AsignacionFormato;
use App\Repositories\Repository;
use App\Models\CADECO\Subcontrato;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\ItemSubcontrato;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Subcontratos\AsignacionContratista;
use App\Models\CADECO\Subcontratos\AsignacionContratistaPartida;

class AsignacionContratistaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AsignacionContratistaService constructor.
     * @param AsignacionContratista $model
     */
    public function __construct(AsignacionContratista $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $asignaciones = $this->repository;

        if(isset($data['busqueda'])){
            $contratos = ContratoProyectado::where('numero_folio', 'LIKE', '%'.$data['busqueda'].'%')->
                                            orWhere('referencia', 'LIKE','%'.$data['busqueda'].'%' )->get();

            foreach ($contratos as $e){
                $asignaciones = $asignaciones->whereOr([['id_transaccion', '=', $e->id_transaccion]]);
            }
        }
        return $asignaciones->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store($data)
    {
        try{
            DB::connection('cadeco')->beginTransaction();
            $asignacion = $this->repository->create([
                'id_transaccion' => $data['id_contrato'],  // contrato proyectado
                'estado' => 1,
            ]);
            $registradas = 0;
            foreach($data['presupuestos'] as $presupuesto){
                foreach($presupuesto['partidas'] as $partida){
                    if($partida && $partida['cantidad_asignada'] > 0){
                        AsignacionContratistaPartida::create([
                            'id_asignacion' => $asignacion->id_asignacion,
                            'id_transaccion' => $presupuesto['id_transaccion'],
                            'id_concepto' => $partida['id_concepto'],
                            'cantidad_asignada' => $partida['cantidad_asignada'],
                            'cantidad_autorizada' => $partida['cantidad_asignada'],
                        ]);
                        $registradas ++;
                    }
                }
            }

            if($registradas == 0){
                abort(403,'La asignación debe tener al menos una partida con cantidad asignada a un proveedor.');
            }

            DB::connection('cadeco')->commit();
            return $asignacion;
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data']);
    }

    public function getAsignaciones($data){
        $asignaciones = $this->repository->all();
        $filtered = $asignaciones->reject(function ($asignacion, $key) {
            return $asignacion->contratoProyectado == null || $asignacion->contratoProyectado->id_obra != Context::getIdObra();
        });
        return $filtered->all();
    }

    public function generarSubcontrato($data){
        try{
            DB::connection('cadeco')->beginTransaction();
            $asignacion = $this->repository->show($data['id']);
            $partidas = $asignacion->partidas()->orderBy('id_concepto')->get();
            $subcontratos = [];
            foreach($partidas as $partida){
                $subcontrato = null;
                if(array_key_exists( $partida->presupuestoPartida->IdMoneda, $subcontratos) && array_key_exists($partida->id_transaccion, $subcontratos[1])){
                    $subcontrato = $subcontratos[ $partida->presupuestoPartida->IdMoneda][$partida->id_transaccion];
                }else{
                    $subcontratos[ $partida->presupuestoPartida->IdMoneda] = array();
                    $resp =
                     Subcontrato::Create([
                        'id_antecedente' => $asignacion->id_transaccion,
                        'id_empresa' => $partida->presupuesto->id_empresa,
                        'id_sucursal' => $partida->presupuesto->id_sucursal,
                        'id_moneda' =>  $partida->presupuestoPartida->IdMoneda,
                        'observaciones' => $partida->presupuesto->observaciones,
                    ]);
                    $subcontratos[ $partida->presupuestoPartida->IdMoneda][$partida->id_transaccion] = $resp;
                    $subcontrato = $resp;
                    AsignacionSubcontrato::create([
                        'id_asignacion' => $asignacion->id_asignacion,
                        'id_transaccion' => $resp->id_transaccion,
                    ]);
                }

                $descuento = $partida->presupuestoPartida->porcentajeDescuento / 100 * $partida->presupuestoPartida->precio_unitario;
                $importe = ($partida->presupuestoPartida->precio_unitario - $descuento) * $partida->cantidad_asignada;

                $partida_subc = ItemSubcontrato::create([
                    'id_transaccion' => $subcontrato->id_transaccion,
                    'id_antecedente' => $partida->id_transaccion,
                    'id_concepto' => $partida->id_concepto,
                    'cantidad' => $partida->cantidad_asignada,
                    'precio_unitario' => $partida->presupuestoPartida->precio_unitario - $descuento,
                    'descuento' => $partida->presupuestoPartida->porcentajeDescuento,
                    'cantidad_original1' => $partida->cantidad_asignada,
                    'precio_original1' => $partida->presupuestoPartida->precio_unitario - $descuento,
                    'id_asignacion' => $partida->id_asignacion,
                ]);

                $subtotal = $importe;
                $impuesto = $subtotal  * 0.16;
                $monto = $subtotal + $impuesto;

                $subcontrato->monto = $subcontrato->monto + $monto;
                $subcontrato->saldo = $subcontrato->saldo + $monto;
                $subcontrato->impuesto = $subcontrato->impuesto + $impuesto;
                $subcontrato->save();

            }
            $asignacion->estado = 2;
            $asignacion->save();
            DB::connection('cadeco')->commit();
            return $asignacion;
        }catch(Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function pdf($id)
    {
        $pdf = new AsignacionFormato($this->repository->show($id));
        return $pdf;
    }
}
