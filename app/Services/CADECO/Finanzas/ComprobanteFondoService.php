<?php


namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\ComprobanteFondo;
use App\Repositories\CADECO\Finanzas\ComprobanteFondo\Repository;

class ComprobanteFondoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ComprobanteFondoService constructor.
     * @param ComprobanteFondo $model
     */

    public function  __construct(ComprobanteFondo $model){
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $comprobantes = $this->repository;

        if(isset($data['fecha']))
        {
            $comprobantes = $comprobantes->where([['fecha', '=', $data['fecha']]]);
        }

        if(isset($data['numero_folio']))
        {
            $comprobantes = $comprobantes->where([['numero_folio','=', request( 'numero_folio' ) ]]);
        }

        if(isset($data['fondo__descripcion']))
        {
            $fondos = $this->repository->findFondo(request('fondo__descripcion'));
            $comprobantes = $comprobantes->whereIn(['id_referente',$fondos]);
        }

        if(isset($data['referencia']))
        {
            $comprobantes = $comprobantes->where([['referencia','LIKE', '%' . request( 'referencia') . '%' ]]);
        }
        return $comprobantes->paginate($data);
    }
}
