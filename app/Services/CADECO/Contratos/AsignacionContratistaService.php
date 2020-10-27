<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 15/07/2020
 * Time: 03:21 PM
 */

namespace App\Services\CADECO\Contratos;

use App\Facades\Context;
use App\Repositories\Repository;
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
}