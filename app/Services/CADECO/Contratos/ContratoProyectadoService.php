<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:21 PM
 */

namespace App\Services\CADECO\Contratos;


use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Contratos\AreaSubcontratante;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class ContratoProyectadoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ContratoProyectadoService constructor.
     * @param ContratoProyectado $model
     */
    public function __construct(ContratoProyectado $model)
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

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->update([
            'fecha' => $data['fecha_date'],
            'cumplimiento' => $data['cumplimiento'],
            'vencimiento' => $data['vencimiento'],
            'referencia' => strtoupper($data['referencia'])
        ]);
    }

    public function paginate($data)
    {
        $cp_area = new AreaSubcontratante();
        $cp = $this->repository;

        if(isset($data['id_area_subcontratante'])){
            $area = TipoAreaSubcontratante::where([['descripcion', 'LIKE', '%'.request('id_area_subcontratante').'%']])->get();

            foreach ($area as $e){
                if(isset($e->id)){
                    $cp_areas = $cp_area::where([['id_area_subcontratante', '=', $e->id]])->get();
                    foreach ($cp_areas as $et){
                        $cp = $cp->whereOr([['id_transaccion', '=', $et->id_transaccion]]);
                    }
                }
            }

        }
        return $this->repository->paginate();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public  function actualiza($data, $id)
    {
        $area =  $data['id_area'];

        $transaccion = $this->repository->show($id);
        $transaccion_area = $transaccion->areasSubcontratantes;
        if(count($transaccion_area) > 0){
            $solicitud = AreaSubcontratante::find($id);
            $solicitud = $solicitud->actualiza($id, $data['id_area']);
            $transaccion->refresh();
            return $transaccion;

        }else{
            try {
                DB::connection('cadeco')->beginTransaction();
                $datos = [
                    'id_area_subcontratante' => $area,
                    'id_transaccion' => $id,
                ];
                $solicitud = AreaSubcontratante::create($datos);

                DB::connection('cadeco')->commit();
                $transaccion->refresh();
                return $transaccion ;
            } catch (\Exception $e) {
                DB::connection('cadeco')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        }
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data']);
    }
}
