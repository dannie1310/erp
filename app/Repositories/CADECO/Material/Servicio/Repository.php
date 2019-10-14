<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 02/05/2019
 * Time: 06:11 PM
 */

namespace App\Repositories\CADECO\Material\Servicio;

use App\Models\CADECO\Familia as Model;
use App\Models\CADECO\Material;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use \App\Repositories\Repository as repositorio;

class Repository extends repositorio implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;


    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    public function paginateServicio()
    {
        $front = '';
        $descri = request('descripcion');
        $colum = request('sort');
        $orden = request('order');
        if(isset($descri)){
            $front = $descri;
        }
        $materiales = Material::query()->whereRaw('LEN(nivel) = 4')->orderBy($colum,$orden)->where('descripcion','LIKE','%'.$front.'%')
            -> where('tipo_material','=',2)->get(['descripcion','nivel','id_material'])->toArray();

        $material_item = [];
        foreach ($materiales as $material){
            if(Material::query()->whereRaw('LEN(nivel) = 8')->where('nivel','like',$material['nivel'].'%')
                -> where('tipo_material','=',2)->where('marca','=',1)->first()){
                array_push($material_item,$material);
            };
        }
        return $material_item;
    }
}
