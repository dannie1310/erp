<?php


namespace App\Repositories\CADECO\Material;

use App\Facades\Context;
use App\Models\CADECO\Material;
use Illuminate\Support\Facades\DB;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;


    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(Material $model)
    {
        $this->model = $model;
    }


    public function list($data)
    {
       return $this->model->lista_materiales($data);
    }

    public function buscarMateriales($dato){
        $resp = $this->model->where('descripcion', 'LIKE', '%'.$dato.'%')->whereIn('tipo_material', [1,4])->insumos()->limit(30)->get();
        return $resp;
    }

    public function materialPorAlmacen($data)
    {
        return $this->model->material_por_almacen($data);
    }

    public function materialHistorico($id, $id_almacen)
    {
        return $this->model->material_historico($id, $id_almacen);
    }

    public function historico_salida($id, $id_almacen)
    {
        return $this->model->historico_salida($id, $id_almacen);
    }

    public function historicoMovimientos($id, $id_almacen)
    {
        return $this->model->historico_movimientos($id, $id_almacen);
    }
}
