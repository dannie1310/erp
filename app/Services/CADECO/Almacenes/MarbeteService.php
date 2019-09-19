<?php


namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\Inventario;
use App\Models\CADECO\Inventarios\Conteo;
use App\Models\CADECO\Inventarios\Marbete;
use App\Repositories\Repository;

class MarbeteService
{
    /**
     * @var Repository
     */
    protected $repository;
    /**
     * MarbeteService constructor
     */

    public function __construct(Marbete $model)
    {
        $this->repository = new Repository($model);
    }

    public function store($data)
    {
        $saldo = Inventario::query()->join('almacenes','almacenes.id_almacen', 'inventarios.id_almacen')
            ->where('inventarios.id_almacen','=', $data['id_almacen'])
            ->where('inventarios.id_material','=', $data['id_material'])->sum('inventarios.saldo');

        $folio_arr = Marbete::query()->where('id_inventario_fisico','=', $data['id_inventario_fisico'] )
            ->orderBy('folio', 'desc')->select('folio')->first()->toArray();

        $folio = $folio_arr['folio']+1;

        $datos = [
          'id_inventario_fisico'=> $data['id_inventario_fisico'],
          'id_almacen'=> $data['id_almacen'],
          'id_material'=> $data['id_material'],
          'folio'=>$folio,
          'saldo'=>$saldo,

        ];
       $marbete = Marbete::query()->create($datos);
       return $marbete;

    }
    public function paginate($data)
    {
            return $this->repository->paginate();
    }
    public function delete($data, $id)
    {
      Conteo::query()->where('id_marbete','=', $id)->delete();
       return $this->repository->delete($data, $id);
    }
}
