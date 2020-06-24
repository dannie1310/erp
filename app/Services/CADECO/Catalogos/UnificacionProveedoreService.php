<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 02/06/2020
 * Time: 06:00 PM
 */

namespace App\Services\CADECO\Catalogos;

use App\Models\CADECO\Empresa;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\Catalogos\UnificacionProveedores;

class UnificacionProveedoreService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * UnificacionProveedoreService constructor
     *
     * @param UnificacionProveedores $model
     */

    public function __construct(UnificacionProveedores $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function store(array $data){
        try {
            DB::connection('cadeco')->beginTransaction();
            $unificacion = $this->repository->create(['id_empresa_unificadora' => $data['id_empresa']]);
            if($data['tipo_empresa'] != $data['tipo_empresa_actualizado']){
                $unificacion->empresa->tipo_empresa = $data['tipo_empresa_actualizado'];
                $unificacion->empresa->save();
            }

            foreach($data['empresas_unificadas'] as $empresa_unificada){
                $empresa = Empresa::find($empresa_unificada['id_empresa']);
                $datos = [
                    'id_unificacion' => $unificacion->id,
                    'id_empresa_unificada' => $empresa_unificada['id_empresa'],
                    'tipo_empresa_unificada' => $empresa_unificada['tipo_empresa'],
                ];
                foreach($empresa->transacciones as $transaccion){
                    $datos['id_transaccion'] = $transaccion->id_transaccion;
                    $unificacion->cambios()->create($datos);
                    $transaccion->id_empresa =  $data['id_empresa'];
                    $transaccion->save();
                }
                foreach($empresa->solicitudCBE as $solicitud){
                    $datos['id_solicitud_movimiento'] = $solicitud->id;
                    $unificacion->cambios()->create($datos);
                    $solicitud->id_empresa = $data['id_empresa'];
                    $solicitud->save();
                }
                foreach($empresa->cuentasBancarias as $cuenta){
                    $datos['id_cuenta_bancaria_empresa'] = $cuenta->id;
                    $unificacion->cambios()->create($datos);
                    $cuenta->id_empresa = $data['id_empresa'];
                    $cuenta->save();
                }

                $empresa->tipo_empresa = 666;
                $empresa->save();
            }

            DB::connection('cadeco')->commit();

            return $unificacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
        
        
    }
}
