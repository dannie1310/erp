<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\Aviso;
use App\Models\SEGURIDAD_ERP\AvisoLeido;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Models\SEGURIDAD_ERP\Sistema;
use App\Repositories\Repository;

class SistemaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SistemaService constructor.
     * @param Sistema $model
     */
    public function __construct(Sistema $model)
    {
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

    public function porObra()
    {
        $sistema = Proyecto::where('base_datos','=',Context::getDatabase())->get();
        return $sistema[0]->sistemas()->where('id_obra','=',Context::getIdObra())
            ->get();
    }
    public function asignacionSistemas($data)
    {
        $sistema = Proyecto::where('base_datos','=',Context::getDatabase())->get();
        $sistema[0]->sistemas()->where('id_obra','=',Context::getIdObra())->get();

        if(!auth()->user()->can('habilitar_deshabilitar_sistema')) {
            throw new \Exception('No es posible asignar el sistema porque no cuenta con el permiso, favor de solicitar la asignación al administrador del sistema.', 403);
        }

        foreach ($data['sistema_id'] as $sistema_id) {
            try {
                $sistema[0]->sistemas()
                    ->wherePivot('id_obra', '=', Context::getIdObra())
                    ->wherePivot('id_proyecto', '=', $sistema[0]->id)
                    ->detach();

            } catch (\Exception $e) {}
        }
        foreach ($data['sistema_id'] as $sistema_id) {
            try {
                $sistema[0]->sistemas()->attach([$sistema_id => ['id_obra' => Context::getIdObra(), 'id_proyecto' => $sistema[0]->id,'created_at' => date('Y-m-d h:i:s')]]);
            } catch (\Exception $e) {}
        }

        return true;
    }

    public function leerAviso($id)
    {
        AvisoLeido::create([
            "id_usuario"=>auth()->id(),
            "id_aviso"=>$id
        ]);

        return [];
    }

    public function getAviso($ruta)
    {
        // $aviso = Aviso::noLeidoPorUsuario()->where("ruta_sistema","=",$ruta)->where("estatus","=",1)->first();
        $aviso = Aviso::where("ruta_sistema","=",$ruta)->where("estatus","=",1)->first();
        if($aviso){
            return $aviso;
        }
        return null;
    }

}
