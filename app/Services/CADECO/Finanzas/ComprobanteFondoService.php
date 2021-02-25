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
        if(isset($data['fecha']))
        {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if(isset($data['numero_folio']))
        {
            $this->repository->where([['numero_folio','=', request( 'numero_folio' ) ]]);
        }

        if(isset($data['id_referente']))
        {
            $fondos = $this->repository->findFondo(request('id_referente'));
            $this->repository->whereIn(['id_referente',$fondos]);
        }

        if(isset($data['referencia']))
        {
            $this->repository->where([['referencia','LIKE', '%' . request( 'referencia') . '%' ]]);
        }
        return $this->repository->paginate($data);
    }
}
