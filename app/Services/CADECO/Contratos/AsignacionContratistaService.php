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
use App\Repositories\Repository;
use App\Models\CADECO\Subcontrato;
use Illuminate\Support\Facades\DB;
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
                            'id_asignacion' => $asignacion->id,
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
                if(array_key_exists($asignacion->presupuestoContratista->id_moneda, $subcontratos)){
                    $subcontrato = $subcontratos[$asignacion->presupuestoContratista->id_moneda];
                }else{
                    $subcontratos[$asignacion->presupuestoContratista->id_moneda] = 
                    Subcontrato::Create([
                        'id_antecedente' => $asignacion->presupuestoContratista->id_antecedente,
                        'id_referente' => $partida->cotizacionCompra->id_transaccion,
                        'id_empresa' => $partida->cotizacionCompra->id_empresa,
                        'id_sucursal' => $partida->cotizacionCompra->id_sucursal,
                        'id_moneda' => $partida->cotizacion->id_moneda,
                        'observaciones' => $partida->cotizacionCompra->observaciones,
                        'porcentaje_anticipo_pactado' => $partida->cotizacionCompra->porcentaje_anticipo_pactado,
                    ]);
                }
                dd('pandita', array_key_exists(1, $subcontratos));
                // dd($asignacion->presupuestoContratista->id_antecedente);
                $subcontratos = Subcontrato::where('id_antecedente', '=', $asignacion->presupuestoContratista->id_antecedente)
                                        ->where('id_empresa', '=', $asignacion->presupuestoContratista->id_empresa)
                                        ->where('id_sucursal', '=', $asignacion->presupuestoContratista->id_sucursal)
                                        ->where('id_moneda', '=', $asignacion->presupuestoContratista->id_moneda)->get();
                
                foreach ($subcontratos as $key => $subcont) {
                    if($orden->complemento->id_asignacion_proveedor == $asignacion->id){
                        $orden_c = $orden;
                        break;
                    }
                }
                
                if(!$orden_c){
                    $orden_c = OrdenCompra::Create([
                        'id_antecedente' => $partida->cotizacionCompra->id_antecedente,
                        'id_referente' => $partida->cotizacionCompra->id_transaccion,
                        'id_empresa' => $partida->cotizacionCompra->id_empresa,
                        'id_sucursal' => $partida->cotizacionCompra->id_sucursal,
                        'id_moneda' => $partida->cotizacion->id_moneda,
                        'observaciones' => $partida->cotizacionCompra->observaciones,
                        'porcentaje_anticipo_pactado' => $partida->cotizacionCompra->porcentaje_anticipo_pactado,
                    ]);

                    $orden_c->complemento()->create(['id_transaccion' => $orden_c->id_transaccion, 'id_asignacion_proveedor'=>$asignacion->id]);
                }
            }
            dd('Pando',$partidas);
            dd('stop');
            DB::connection('cadeco')->commit();
            return $asignacion;
        }catch(Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
}