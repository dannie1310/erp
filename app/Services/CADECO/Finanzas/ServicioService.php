<?php


namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Familia;
use App\Models\CADECO\Material;
use App\Repositories\CADECO\Material\Servicio\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ServicioService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FamiliaService constructor
     *
     * @param Familia $model
     */

    public function __construct(Familia $model)
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

    public function store(array $data)
    {
        $datos = [
            'tipo_material' => $data['tipo'],
            'descripcion' => $data['descripcion']
        ];

        return $this->repository->create($datos);
    }
    public function porServicio()
    {
        $servicios = $this->repository->paginateServicio();

        $permisos = collect($servicios);
        $perPage     = request('limit');
        $page = request('limit') && request('offset') != '' ? (request('offset') / request('limit')) + 1 : 1;
        request()->merge(['page' => $page]);
        $currentPage = Paginator::resolveCurrentPage();
        $currentPage = $currentPage ? $currentPage : 1;
        $offset      = ($currentPage * $perPage) - $perPage;
        $paginator = new LengthAwarePaginator(
            array_slice($permisos->toArray(), $offset, $perPage),
            count($permisos),
            $perPage
        );
        return $paginator;
    }

}
