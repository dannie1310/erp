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

    public function showCodigo($id){
        $marbete = $this->repository->show($id);
        if(!$marbete || $marbete->invetarioFisico->estado != 0){
            throw new \Exception("El código del marbete no es válido", 400);
        }
        return $this->repository->show($id);
    }

    public function store($data)
    {

        $inventario = InventarioFisico::where('estado',0)->first();


        if (empty($inventario)){
            abort(400, 'No hay un inventario físico activo');
        }
        

        $existe_material = Marbete::where('id_almacen','=', $data['id_almacen'])
            ->where('id_material', '=', $data['id_material'])
            ->where('id_inventario_fisico','=', $inventario->id)
            ->first();

        if(!empty($existe_material)){
            abort(400, 'Ya existe un marbete asociado a este material en el inventario');
        }

        $saldo = Inventario::join('almacenes','almacenes.id_almacen', 'inventarios.id_almacen')
            ->where('inventarios.id_almacen','=', $data['id_almacen'])
            ->where('inventarios.id_material','=', $data['id_material'])->sum('inventarios.saldo');

        $folio_arr = Marbete::where('id_inventario_fisico','=', $inventario->id )
            ->orderBy('folio', 'desc')->select('folio')->first();

        if(is_null($folio_arr)){
            $folio = 1;
        }else {
            $folio = $folio_arr->folio + 1;
        }

        $datos = [
          'id_inventario_fisico'=> $inventario->id,
          'id_almacen'=> $data['id_almacen'],
          'id_material'=> $data['id_material'],
          'folio'=>$folio,
          'saldo'=>$saldo,
        ];
       $marbete = Marbete::create($datos);
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
           $almacen = Almacen::where('descripcion','LIKE', '%'.$data['id_almacen'].'%')->get();

           $this->repository->whereIn(['id_almacen', $almacen->pluck('id_almacen')]);

        }

        if(isset($data['id_material']))
        {
            $material = Material::where([['descripcion', 'LIKE', '%'.$data['id_material'].'%']])->get();

            foreach($material as $e){
                $marbetes = $marbetes->where([['id_material','=', $e->id_material]]);

            }
        }
            return $marbetes->paginate($data);
    }

    public function delete($data, $id)
    {


        $inventario = InventarioFisico::where('id',$data['id_inventario_fisico'])->get()->toArray();

        $estado=$inventario[0]['estado'];

        if ($estado==='1'){
            abort(400, 'No se puede eliminar un marbete de un Inventario Cerrado');
        }

      Conteo::where('id_marbete','=', $id)->delete();
       return $this->repository->delete($data, $id);
    }


    public function index($data)
    {
        return $this->repository->all($data);
    }
}
