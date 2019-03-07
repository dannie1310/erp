<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2019
 * Time: 08:50 PM
 */

namespace App\Services\CADECO\Contratos\FondoGarantia;


use App\Facades\Context;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use App\Repositories\CADECO\SubcontratosFG\FondoGarantia\Repository;

class FondoGarantiaService
{
    /**
     * @var Repository
     */
    protected $repository;
    private $id_usuario;
    private $usuario;
    private $id_obra;

    public function __construct(FondoGarantia $model)
    {
        $this->repository = new Repository($model);
        $this->id_usuario = auth()->id();
        $this->usuario = (auth()->user())?auth()->user()->usuario:null;
        $this->id_obra = Context::getIdObra();
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function paginate($data)
    {
        $fondo_garantia = $this->repository;
        if (isset($data['subcontrato__numero_folio'])) {
            $fondo_garantia = $fondo_garantia->where([['transacciones.numero_folio', 'LIKE', '%' . $data['subcontrato__numero_folio'] . '%']]);
        }

        if (isset($data['subcontrato__referencia'])) {
            $fondo_garantia = $fondo_garantia->where([['transacciones.referencia', 'LIKE', '%' . $data['subcontrato__referencia'] . '%']]);
        }

        if (isset($data['empresa__razon_social'])) {
            $fondo_garantia = $fondo_garantia->where([['empresas.razon_social', 'LIKE', '%' . $data['empresa__razon_social'] . '%']]);
        }
        return $fondo_garantia->paginate($data);
    }

    public function store($data)
    {
        $data['id_usuario'] = $this->id_usuario;
        $data['usuario'] = $this->usuario;
        $data['id_obra'] = $this->id_obra;
        return $this->repository->create($data);
    }

    public function ajustarSaldo(array $data, $id)
    {
        $data['id_usuario'] = $this->id_usuario;
        $data['usuario'] = $this->usuario;
        $data['id_obra'] = $this->id_obra;
        return $this->repository->ajustarSaldo($data, $id);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function index()
    {
        return $this->repository->all();
    }
}