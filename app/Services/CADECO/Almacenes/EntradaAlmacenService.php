<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 12:57 PM
 */

namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\Empresa;
use App\Models\CADECO\EntradaMaterial;
use App\PDF\EntradaAlmacenFormato;
use App\Repositories\CADECO\EntradaAlmacen\Repository;

class EntradaAlmacenService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EntradaAlmacenService constructor.
     * @param EntradaMaterial $model
     */
    public function __construct(EntradaMaterial $model)
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
        if(isset($data['id_empresa'])){
            $empresas = Empresa::query()->where([['razon_social', 'LIKE', '%'.request('id_empresa').'%']])->get();
            foreach ($empresas as $a){
                $salida = $salida->whereOr([['id_empresa', '=', $a->id_empresa]]);
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

    public function pdfEntradaAlmacen($id)
    {
        $pdf = new EntradaAlmacenFormato($id);
        return $pdf;
    }

    public function store(array $data)
    {
        $datos = [
            'id_antecedente' => $data['id_orden_compra'],
            'remision' => $data['remision'],
            'observaciones' => $data['orden_compra']['observaciones'],
            'partidas' =>  $data['orden_compra']['partidas']['data']
        ];

        return $this->repository->create($datos);
    }
}
