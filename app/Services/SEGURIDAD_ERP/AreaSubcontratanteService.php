<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\AreaSubcontratante;
use Illuminate\Support\Facades\DB;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use App\Repositories\Repository;

class AreaSubcontratanteService
{
    protected $repository;

    public function __construct(TipoAreaSubcontratante $model){
        $this->repository = new Repository($model);
    }
    public function index($data)
    {
        return $this->repository->all($data);
    }
    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function porUsuario($data, $user_id)
    {
        $usuario = Usuario::query()->find($user_id);
        return $usuario->areasSubcontratantes()
            ->get();
    }

    public function asignacionAreas($data)
    {
        dd($data);
        $usuario = Usuario::query()->find($data['user_id']);

        foreach ($data['area_id'] as $area_id) {
            try {
                DB::connection( 'seguridad' )->beginTransaction();
                $datos = [
                    'id_usuario' => $data['user_id'],
                    'id_area_subcontratante' => $area_id
                ];

                AreaSubcontratante::query()->create( $datos );

                DB::connection( 'seguridad' )->commit();

            } catch (\Exception $e) {
                DB::connection( 'seguridad' )->rollBack();
                abort( 400, $e->getMessage() );
                throw $e;
            }
        }
        return $usuario->areasSubcontratantes()
            ->get();
    }

}