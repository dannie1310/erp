<?php


namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\Almacen;
use App\Models\CADECO\SalidaAlmacen;
use App\Repositories\Repository;

class SalidaAlmacenService
{

    /**
     * @var $repository
     */
    protected $repository;

    public function __construct(SalidaAlmacen $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $salida = $this->repository;

        if(isset($data['numero_folio'])) {
            $salida = $salida->where( [['numero_folio', 'LIKE', '%' . request( 'numero_folio' ) . '%']] );
        }

        if(isset($data['fecha'])) {
            $salida = $salida->where( [['fecha', '=', request( 'fecha' )]] );
        }

        if(isset($data['referencia'])) {
            $salida = $salida->where( [['referencia', 'LIKE', '%' . request( 'referencia' ) . '%']] );
        }

        if(isset($data['id_almacen'])){
            $almacen = Almacen::query()->where([['descripcion', 'LIKE', '%'.request('id_almacen').'%']])->get();
            foreach ($almacen as $a){
                $salida = $salida->whereOr([['id_almacen', '=', $a->id_almacen]]);
            }
        }
        return $salida->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data'][0]);
    }
}