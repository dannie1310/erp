<?php


namespace App\Services\CADECO\Almacenes;



use App\Models\CADECO\Almacen;
use App\Models\CADECO\Inventario;
use App\Models\CADECO\Inventarios\Conteo;
use App\Models\CADECO\Inventarios\InventarioFisico;
use App\Models\CADECO\Inventarios\Marbete;
use App\Models\CADECO\Material;
use App\Repositories\Repository;

class MarbeteService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * MarbeteService constructor.
     * @param Marbete $model
     */
    public function __construct(Marbete $model)
    {
        $this->repository = new Repository($model);
    }

    public function store($data)
    {

        $inventario = InventarioFisico::query()->where('estado',0)->first();

        if (empty($inventario)){
            abort(400, 'No hay un inventario físico activo');
        }

        $saldo = Inventario::query()->join('almacenes','almacenes.id_almacen', 'inventarios.id_almacen')
            ->where('inventarios.id_almacen','=', $data['id_almacen'])
            ->where('inventarios.id_material','=', $data['id_material'])->sum('inventarios.saldo');

        $folio_arr = Marbete::query()->where('id_inventario_fisico','=', $inventario->id )
            ->orderBy('folio', 'desc')->select('folio')->first()->toArray();

        $folio = $folio_arr['folio']+1;

        $datos = [
          'id_inventario_fisico'=> $inventario->id,
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

        $marbetes = $this->repository;

        if(isset($data['id_inventario_fisico']))
        {
          $marbetes = $marbetes->where([['id_inventario_fisico', 'LIKE', '%'.$data['id_inventario_fisico'].'%']]);
        }

        if(isset($data['folio']))
        {
            $marbetes = $marbetes->where([['folio', 'LIKE', '%'.$data['folio'].'%']]);
        }

        if(isset($data['id_almacen']))
        {
           $almacen = Almacen::query()->where('descripcion','LIKE', '%'.$data['id_almacen'].'%')->get();

           $this->repository->whereIn(['id_almacen', $almacen->pluck('id_almacen')]);

        }

        if(isset($data['id_material']))
        {
            $material = Material::query()->where([['descripcion', 'LIKE', '%'.$data['id_material'].'%']])->get();

            foreach($material as $e){
                $marbetes = $marbetes->where([['id_material','=', $e->id_material]]);

            }
        }
            return $marbetes->paginate($data);
    }

    public function delete($data, $id)
    {
      Conteo::query()->where('id_marbete','=', $id)->delete();
       return $this->repository->delete($data, $id);
    }


    public function index($data)
    {
        return $this->repository->all($data);
    }

}
