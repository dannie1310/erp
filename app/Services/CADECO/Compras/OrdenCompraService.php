<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/05/2019
 * Time: 02:16 PM
 */

namespace App\Services\CADECO\Compras;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\OrdenCompra;
use App\PDF\OrdenCompraFormato;
use App\Repositories\Repository;


class OrdenCompraService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(OrdenCompra $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
    public function paginate($data)
    {
        $ordenes = $this->repository;

        if(isset($data['numero_folio'])){
            $ordenes = $ordenes->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['id_antecedente'])){
            $solicitud = Solicitud::query()->where([['numero_folio', 'LIKE', '%'.$data['id_antecedente'].'%']])->get();
            foreach ($solicitud as $e){
                $ordenes = $ordenes->whereOr([['id_antecedente', '=', $e->id_transaccion]]);
            }
        }

        if(isset($data['id_empresa'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['id_empresa'].'%']])->get();
            foreach ($empresa as $e){
                $ordenes = $ordenes->whereOr([['id_empresa', '=', $e->id_empresa]]);
            }
        }

        if(isset($data['observaciones'])){
            $ordenes = $ordenes->where([['observaciones', 'LIKE', '%'.$data['observaciones'].'%']]);
        }

        return $ordenes->paginate($data);
    }
    public function pdfOrdenCompra($id)
    {
        $pdf = new OrdenCompraFormato($id);
        return $pdf;
    }
}