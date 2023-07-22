<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/02/19
 * Time: 05:00 PM
 */

namespace App\Services\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Material;
use App\Repositories\CADECO\Material\Repository;
use Illuminate\Support\Facades\DB;

class MaterialService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * MaterialService constructor.
     *
     * @param Material $model
     */
    public function __construct(Material $model)
    {
        $this->repository = new Repository($model);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        $material = $this->repository;

        if(isset($data['descripcion'])) {
            $material->where([['descripcion', 'LIKE', '%' . $data['descripcion'] . '%']]);
        }

        if(isset($data['numero_parte'])) {
            $material->where([['numero_parte', 'LIKE', '%' . $data['numero_parte'] . '%']]);
        }

        if(isset($data['unidad'])) {
            $material->where([['unidad', 'LIKE', '%' . $data['unidad'] . '%']]);
        }

        return $material->paginate($data);
    }
    public function store(array $data)
    {
        $niveles = $this->repository->where([['nivel', 'like', $data['tipo'].'___.']])->where([['tipo_material', '=', $data['tipo_material']]])->all();
dd($niveles);
        if($niveles->count() > 0){
            $nivel = $niveles->sortByDesc('nivel')->first()->nivel;
            dd($niveles);
            explode(".", $nivel)[1] == 999?abort(403, "La familia ya esta al limite de insumos permitidos, favor de ingresar una nueva familia y agregar el insumo."):'';
        }
        $datos = [
            'nivel' => $data['tipo'],
            'unidad' => $data['unidad'],
            'descripcion' => $data['descripcion'],
            'numero_parte' => $data['nu_parte'],
            'tipo_material' => $data['tipo_material'],
            'equivalencia' => $data['equivalencia'],
            'marca' => $data['marca']

        ];

        return $this->repository->create($datos);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminarInsumo();
    }

    public function catalogo_insumos($data)
    {
        $array = ['scope' => $data];
        return $this->repository->list($data);
    }

    public function update(array $data, $id)
    {
        return $this->show($id)->actualizarInsumo($data);
    }

    public function buscarMateriales($data){
        return $this->repository->buscarMateriales($data['busqueda']);
    }

    public function materialPorAlmacen($id)
    {
        return $this->repository->materialPorAlmacen($id['id']);
    }

    public function materialHistorico($data)
    {
        return $this->repository->materialHistorico($data['id'], $data['id_almacen']);
    }

    public function historicoSalida($data)
    {
        return $this->repository->historico_salida($data['id'], $data['id_almacen']);
    }

    public function historicoMovimientos($data)
    {
        return $this->repository->historicoMovimientos($data['id'], $data['id_almacen']);
    }
}
