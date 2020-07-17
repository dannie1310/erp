<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 15/07/2020
 * Time: 03:21 PM
 */

namespace App\Services\CADECO\Contratos;

use App\Repositories\Repository;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Subcontratos\AsignacionContratista;

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
}